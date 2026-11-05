<?php

namespace App\Http\Controllers;

use App\Http\Requests\MovementRequest;
use App\Models\Item;
use App\Models\StockMovement;
use App\Services\InventoryService;
use App\Services\Stock\StockIn;
use App\Services\Stock\StockOut;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MovementController extends Controller
{
    private function filtered(Request $request)
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:120'], 'type' => ['nullable', 'in:in,out'],
            'from' => ['nullable', 'date_format:Y-m-d'], 'to' => ['nullable', 'date_format:Y-m-d', ...($request->filled('from') ? ['after_or_equal:from'] : [])],
        ]);
        return StockMovement::with(['item.category', 'user'])
            ->when($filters['q'] ?? null, fn ($query, $q) => $query->where(fn ($query) => $query->where('reference', 'like', '%'.$q.'%')->orWhereHas('item', fn ($item) => $item->where('name', 'like', '%'.$q.'%')->orWhere('sku', 'like', '%'.$q.'%'))))
            ->when($filters['type'] ?? null, fn ($query, $type) => $query->where('type', $type))
            ->when($filters['from'] ?? null, fn ($query, $date) => $query->where('occurred_at', '>=', $date))
            ->when($filters['to'] ?? null, fn ($query, $date) => $query->where('occurred_at', '<=', $date));
    }

    public function index(Request $request): View
    {
        $query = $this->filtered($request);
        $summary = (clone $query)->reorder()->selectRaw('type, SUM(quantity) as total')->groupBy('type')->pluck('total', 'type');
        $movements = $query->latest('id')->paginate(12)->withQueryString();
        return view('movements.index', compact('movements', 'summary'));
    }

    public function create(Request $request): View
    {
        $type = $request->query('type') === 'out' ? 'out' : 'in';
        $items = Item::with('category')->orderBy('name')->get();
        return view('movements.create', compact('type', 'items'));
    }

    public function store(MovementRequest $request, InventoryService $service): RedirectResponse
    {
        $data = $request->validated();
        $operation = $data['type'] === 'in' ? new StockIn : new StockOut;
        $movement = $service->record($operation, (int) $data['item_id'], (int) $data['quantity'], $request->user(), $data);
        return redirect()->route('movements.index')->with('success', 'Transaksi '.$movement->reference.' berhasil dicatat. Stok sudah diperbarui.');
    }

    public function export(Request $request): StreamedResponse
    {
        $query = $this->filtered($request)->latest('id');
        return response()->streamDownload(function () use ($query) {
            $output = fopen('php://output', 'w');
            fwrite($output, "\xEF\xBB\xBF");
            fputcsv($output, ['Referensi', 'Tanggal', 'Kode Barang', 'Nama Barang', 'Jenis', 'Jumlah', 'Stok Sebelum', 'Stok Sesudah', 'Petugas', 'Keterangan'], ',', '"', '');
            $query->chunk(500, function ($rows) use ($output) {
                foreach ($rows as $row) {
                    $cells = [$row->reference, $row->occurred_at->format('Y-m-d'), $row->item->sku, $row->item->name, $row->type === 'in' ? 'Masuk' : 'Keluar', $row->quantity, $row->stock_before, $row->stock_after, $row->user->name, $row->notes];
                    $cells = array_map(fn ($value) => is_string($value) && preg_match('/^[=+@\-\t\r\n]/', $value) ? "'".$value : $value, $cells);
                    fputcsv($output, $cells, ',', '"', '');
                }
            });
            fclose($output);
        }, 'riwayat-stok-'.today()->format('Y-m-d').'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}