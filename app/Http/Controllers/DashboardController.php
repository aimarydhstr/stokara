<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $period = (string) $request->query('period', '7');
        $days = in_array($period, ['7', '14', '30'], true) ? (int) $period : 7;
        $start = today()->subDays($days - 1);
        $totals = StockMovement::where('occurred_at', '>=', $start)->where('occurred_at', '<=', today())
            ->selectRaw('type, SUM(quantity) as total')->groupBy('type')->pluck('total', 'type');
        $daily = StockMovement::whereBetween('occurred_at', [$start, today()])
            ->selectRaw('occurred_at, type, SUM(quantity) as total')->groupBy('occurred_at', 'type')->get();
        $series = collect(range(0, $days - 1))->map(function ($offset) use ($start, $daily) {
            $date = $start->copy()->addDays($offset);
            $entries = $daily->filter(fn ($entry) => $entry->occurred_at->isSameDay($date));
            return ['label' => $date->translatedFormat('d M'), 'in' => (int) $entries->where('type', 'in')->sum('total'), 'out' => (int) $entries->where('type', 'out')->sum('total')];
        });
        $stats = [
            'items' => Item::count(),
            'incoming' => (int) ($totals['in'] ?? 0),
            'outgoing' => (int) ($totals['out'] ?? 0),
            'low' => Item::lowStock()->count(),
            'value' => (float) Item::selectRaw('COALESCE(SUM(stock * unit_price), 0) as total')->value('total'),
        ];
        $recent = StockMovement::with(['item.category', 'user'])->latest('created_at')->latest('id')->limit(6)->get();
        $lowItems = Item::with('category')->lowStock()->orderBy('stock')->limit(4)->get();
        $categories = Category::withCount('items')->orderByDesc('items_count')->get();
        return view('dashboard', compact('stats', 'recent', 'lowItems', 'categories', 'series', 'days'));
    }
}