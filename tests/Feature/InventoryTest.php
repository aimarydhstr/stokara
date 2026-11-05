<?php
namespace Tests\Feature;
use App\Models\Category;
use App\Models\Item;
use App\Models\StockMovement;
use App\Models\User;
use App\Services\InventoryService;
use App\Services\Stock\StockIn;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;
class InventoryTest extends TestCase
{
    use RefreshDatabase;
    private User $operator;
    private Category $category;
    private Item $item;
    protected function setUp(): void
    {
        parent::setUp();
        $this->operator = User::factory()->create();
        $this->category = Category::create(['name' => 'Kertas', 'color' => 'blue']);
        $this->item = Item::create(['category_id' => $this->category->id, 'sku' => 'TEST-001', 'name' => 'Kertas Uji', 'unit' => 'Rim', 'minimum_stock' => 5, 'unit_price' => 50000]);
        $this->actingAs($this->operator);
    }
    private function movement(string $type = 'in', int $quantity = 20, array $extra = []): array
    {
        return array_replace(['type' => $type, 'item_id' => $this->item->id, 'quantity' => $quantity, 'occurred_at' => today()->toDateString(), 'request_token' => (string) Str::uuid(), 'notes' => 'Pengujian inventori'], $extra);
    }
    public function test_stock_in_out_preserve_stock_and_audit_history(): void
    {
        $this->post('/movements', $this->movement())->assertRedirect(route('movements.index'));
        $this->post('/movements', $this->movement('out', 5))->assertSessionHasNoErrors();
        $this->assertSame(15, $this->item->fresh()->stock);
        $this->assertDatabaseHas('stock_movements', ['type' => 'out', 'quantity' => 5, 'stock_before' => 20, 'stock_after' => 15, 'user_id' => $this->operator->id]);
        $this->assertSame(2, StockMovement::count());
    }
    public function test_insufficient_stock_rolls_back_every_change(): void
    {
        $this->post('/movements', $this->movement('in', 5));
        $this->post('/movements', $this->movement('out', 6))->assertSessionHasErrors('quantity');
        $this->assertSame(5, $this->item->fresh()->stock);
        $this->assertSame(1, StockMovement::count());
    }
    public function test_empty_stock_rejects_stock_out(): void
    {
        $this->post('/movements', $this->movement('out', 1))->assertSessionHasErrors('quantity');
        $this->assertDatabaseCount('stock_movements', 0);
    }
    public function test_repeated_submission_does_not_apply_stock_twice(): void
    {
        $data = $this->movement();
        $this->post('/movements', $data)->assertSessionHasNoErrors();
        $this->post('/movements', $data)->assertSessionHasErrors('request_token');
        $this->assertSame(20, $this->item->fresh()->stock);
        $this->assertDatabaseCount('stock_movements', 1);
    }
    public function test_service_also_guards_duplicate_requests(): void
    {
        $service = app(InventoryService::class);
        $data = $this->movement();
        $service->record(new StockIn, $this->item->id, 20, $this->operator, $data);
        try {
            $service->record(new StockIn, $this->item->id, 20, $this->operator, $data);
            $this->fail('Duplicate request should be rejected.');
        } catch (ValidationException) {
            $this->assertSame(20, $this->item->fresh()->stock);
            $this->assertDatabaseCount('stock_movements', 1);
        }
    }
    public function test_invalid_quantity_type_date_and_item_are_rejected(): void
    {
        $this->post('/movements', $this->movement('in', 0))->assertSessionHasErrors('quantity');
        $this->post('/movements', $this->movement('invalid'))->assertSessionHasErrors('type');
        $this->post('/movements', $this->movement(extra: ['quantity' => 1.5]))->assertSessionHasErrors('quantity');
        $this->post('/movements', $this->movement(extra: ['occurred_at' => today()->addDay()->toDateString()]))->assertSessionHasErrors('occurred_at');
        $this->post('/movements', $this->movement(extra: ['item_id' => 999999]))->assertSessionHasErrors('item_id');
        $this->assertDatabaseCount('stock_movements', 0);
        $this->assertSame(0, $this->item->fresh()->stock);
    }
    public function test_item_create_update_cannot_bypass_stock_ledger(): void
    {
        $data = ['sku' => 'NEW-001', 'name' => 'Pulpen', 'category_id' => $this->category->id, 'unit' => 'Box', 'minimum_stock' => 2, 'unit_price' => 15000, 'stock' => 900];
        $this->post('/items', $data)->assertSessionHasNoErrors();
        $created = Item::where('sku', 'NEW-001')->firstOrFail();
        $this->assertSame(0, $created->stock);
        $this->put('/items/'.$created->id, array_replace($data, ['name' => 'Pulpen Biru', 'stock' => 1000]))->assertSessionHasNoErrors();
        $this->assertSame('Pulpen Biru', $created->fresh()->name);
        $this->assertSame(0, $created->fresh()->stock);
    }
    public function test_duplicate_sku_is_rejected(): void
    {
        $this->post('/items', ['sku' => $this->item->sku, 'name' => 'Duplikat', 'category_id' => $this->category->id, 'unit' => 'Pcs', 'minimum_stock' => 0, 'unit_price' => 0])->assertSessionHasErrors('sku');
    }
    public function test_unused_item_can_be_deleted_and_historical_item_is_protected(): void
    {
        $unused = Item::create(['category_id' => $this->category->id, 'sku' => 'EMPTY', 'name' => 'Kosong', 'unit' => 'Pcs', 'minimum_stock' => 0, 'unit_price' => 0]);
        $this->delete('/items/'.$unused->id)->assertRedirect(route('items.index'));
        $this->assertDatabaseMissing('items', ['id' => $unused->id]);
        $this->post('/movements', $this->movement('in', 5));
        $this->post('/movements', $this->movement('out', 5));
        $this->delete('/items/'.$this->item->id)->assertSessionHas('error');
        $this->assertDatabaseHas('items', ['id' => $this->item->id, 'stock' => 0]);
        $this->assertDatabaseCount('stock_movements', 2);
    }
    public function test_category_crud_and_referenced_category_protection(): void
    {
        $this->post('/categories', ['name' => 'Kategori Baru', 'color' => 'green'])->assertSessionHasNoErrors();
        $category = Category::where('name', 'Kategori Baru')->firstOrFail();
        $this->put('/categories/'.$category->id, ['name' => 'Kategori Diperbarui', 'color' => 'purple'])->assertSessionHasNoErrors();
        $this->delete('/categories/'.$category->id)->assertSessionHas('success');
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
        $this->delete('/categories/'.$this->category->id)->assertSessionHas('error');
        $this->assertDatabaseHas('categories', ['id' => $this->category->id]);
    }
    public function test_all_pages_render_with_database_relationships(): void
    {
        $this->post('/movements', $this->movement());
        foreach (['/', '/items', '/items/create', '/items/'.$this->item->id, '/items/'.$this->item->id.'/edit', '/categories', '/movements', '/movements/create?type=in', '/movements/create?type=out', '/?period=30'] as $url) {
            $this->get($url)->assertOk()->assertSee('Stokara');
        }
    }
    public function test_empty_search_and_stock_filters(): void
    {
        $this->get('/items?q=TidakAda')->assertOk()->assertSee('Barang tidak ditemukan');
        $this->get('/items?status=low')->assertOk()->assertSee($this->item->name);
        $this->get('/items?status=available')->assertOk()->assertDontSee('href="'.route('items.show', $this->item).'"', false);
    }
    public function test_movement_filters_and_csv_have_same_selection(): void
    {
        $this->post('/movements', $this->movement('in', 20));
        $this->post('/movements', $this->movement('out', 5));
        $this->get('/movements?type=out&from='.today()->toDateString().'&to='.today()->toDateString())->assertOk()->assertViewHas('movements', fn ($rows) => $rows->total() === 1);
        $this->get('/movements?to='.today()->toDateString())->assertOk();
        $export = $this->get('/movements/export?type=out')->assertOk()->assertDownload();
        $this->assertStringContainsString('Keluar', $export->streamedContent());
        $this->assertStringNotContainsString('Masuk', $export->streamedContent());
    }
    public function test_csv_neutralizes_spreadsheet_formulas(): void
    {
        $this->post('/movements', $this->movement(extra: ['notes' => '=1+1']));
        $this->assertStringContainsString("'=1+1", $this->get('/movements/export')->streamedContent());
    }
    public function test_dashboard_chart_uses_real_daily_quantities(): void
    {
        $this->post('/movements', $this->movement('in', 20));
        $this->get('/')->assertOk()->assertViewHas('series', fn ($series) => $series->last()['in'] === 20)->assertViewHas('stats', fn ($stats) => $stats['incoming'] === 20 && $stats['low'] === 0);
    }
    public function test_dashboard_queries_remain_bounded(): void
    {
        $this->post('/movements', $this->movement('in', 20));
        DB::enableQueryLog();
        $this->get('/')->assertOk();
        $queries = count(DB::getQueryLog());
        DB::disableQueryLog();
        $this->assertLessThanOrEqual(15, $queries);
    }
    public function test_invalid_login_is_rejected_valid_login_and_logout_work(): void
    {
        auth()->logout();
        $user = User::factory()->create(['email' => 'operator@example.test', 'password' => Hash::make('test-password-123')]);
        $this->post('/login', ['email' => $user->email, 'password' => 'incorrect'])->assertSessionHasErrors('email');
        $this->assertGuest();
        $this->post('/login', ['email' => $user->email, 'password' => 'test-password-123'])->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
        $this->post('/logout')->assertRedirect('/login');
        $this->assertGuest();
    }
    public function test_missing_item_returns_not_found(): void
    {
        $this->get('/items/999999')->assertNotFound();
    }
}