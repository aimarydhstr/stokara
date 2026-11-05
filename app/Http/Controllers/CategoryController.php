<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        return view('categories.index', ['categories' => Category::withCount('items')->orderBy('name')->paginate(12)]);
    }

    public function store(Request $request): RedirectResponse
    {
        Category::create($this->validated($request));
        return back()->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $category->update($this->validated($request, $category));
        return back()->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        return DB::transaction(function () use ($category) {
            $locked = Category::lockForUpdate()->findOrFail($category->id);
            if ($locked->items()->exists()) {
                return back()->with('error', 'Kategori masih digunakan oleh barang dan tidak dapat dihapus.');
            }
            $locked->delete();
            return back()->with('success', 'Kategori berhasil dihapus.');
        });
    }

    private function validated(Request $request, ?Category $category = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:80', Rule::unique('categories')->ignore($category)],
            'description' => ['nullable', 'string', 'max:255'],
            'color' => ['required', Rule::in(['green', 'blue', 'purple', 'orange', 'pink', 'slate'])],
        ], [], ['name' => 'nama kategori', 'description' => 'deskripsi', 'color' => 'warna kategori']);
    }
}