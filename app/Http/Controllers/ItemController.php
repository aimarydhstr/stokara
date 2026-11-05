<?php

namespace App\Http\Controllers;

use App\Http\Requests\ItemRequest;
use App\Models\Category;
use App\Models\Item;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ItemController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate(['q' => ['nullable', 'string', 'max:120'], 'category' => ['nullable', 'integer'], 'status' => ['nullable', 'in:low,available,empty']]);
        $query = Item::with('category')
            ->when($filters['q'] ?? null, fn ($query, $q) => $query->where(fn ($query) => $query->where('name', 'like', '%'.$q.'%')->orWhere('sku', 'like', '%'.$q.'%')))
            ->when($filters['category'] ?? null, fn ($query, $category) => $query->where('category_id', $category));
        match ($filters['status'] ?? null) {
            'low' => $query->lowStock(),
            'available' => $query->whereColumn('stock', '>', 'minimum_stock'),
            'empty' => $query->where('stock', 0),
            default => null,
        };
        $items = $query->orderBy('name')->paginate(10)->withQueryString();
        $categories = Category::orderBy('name')->get();
        $counts = ['all' => Item::count(), 'low' => Item::lowStock()->count(), 'empty' => Item::where('stock', 0)->count()];
        return view('items.index', compact('items', 'categories', 'counts'));
    }

    public function create(): View
    {
        return view('items.form', ['item' => new Item(['unit' => 'Pcs', 'minimum_stock' => 5, 'unit_price' => 0]), 'categories' => Category::orderBy('name')->get()]);
    }

    public function store(ItemRequest $request): RedirectResponse
    {
        $item = Item::create($request->validated());
        return redirect()->route('items.show', $item)->with('success', 'Barang berhasil ditambahkan. Catat barang masuk untuk menambah stok.');
    }

    public function show(Item $item): View
    {
        $item->load('category');
        $movements = $item->movements()->with('user')->latest('id')->paginate(10);
        return view('items.show', compact('item', 'movements'));
    }

    public function edit(Item $item): View
    {
        return view('items.form', ['item' => $item, 'categories' => Category::orderBy('name')->get()]);
    }

    public function update(ItemRequest $request, Item $item): RedirectResponse
    {
        $item->update($request->validated());
        return redirect()->route('items.show', $item)->with('success', 'Informasi barang berhasil diperbarui.');
    }

    public function destroy(Item $item): RedirectResponse
    {
        return DB::transaction(function () use ($item) {
            $locked = Item::lockForUpdate()->findOrFail($item->id);
            if ($locked->stock > 0 || $locked->movements()->exists()) {
                return back()->with('error', 'Barang dengan stok atau riwayat transaksi tidak dapat dihapus agar jejak pencatatan tetap terjaga.');
            }
            $locked->delete();
            return redirect()->route('items.index')->with('success', 'Barang berhasil dihapus.');
        });
    }
}