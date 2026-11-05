<?php
namespace Tests\Unit;
use App\Services\Stock\StockIn;
use App\Services\Stock\StockOut;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;
class StockOperationTest extends TestCase
{
    public function test_polymorphic_stock_operations_and_exact_depletion(): void
    {
        $this->assertSame(25, (new StockIn)->calculate(20, 5));
        $this->assertSame(15, (new StockOut)->calculate(20, 5));
        $this->assertSame(0, (new StockOut)->calculate(20, 20));
    }
    #[DataProvider('invalidOperations')]
    public function test_invalid_operations_are_rejected(string $class, int $stock, int $quantity): void
    {
        $this->expectException(ValidationException::class);
        (new $class)->calculate($stock, $quantity);
    }
    public static function invalidOperations(): array
    {
        return [[StockIn::class, 0, 0], [StockIn::class, 0, -1], [StockOut::class, 10, 0], [StockOut::class, 10, -1], [StockOut::class, 10, 11], [StockIn::class, 1000000000, 1]];
    }
}