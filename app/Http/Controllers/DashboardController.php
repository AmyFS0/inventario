<?php

namespace App\Http\Controllers;

use App\Models\InventarioArea;
use App\Models\Item;
use App\Models\MovimientoInventario;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        abort_unless(auth()->user()->can('ver dashboard'), 403);

        $user = auth()->user();

        $baseItem = Item::query()
            ->when(!$user->esSuperAdmin(), fn ($q) => $q->where('empresa_id', $user->empresa_id));

        $totalItems = (clone $baseItem)->where('estado', 'activo')->count();

        $baseInventario = InventarioArea::query()
            ->join('items', 'items.id', '=', 'inventario_area.item_id')
            ->when(!$user->esSuperAdmin(), function ($query) use ($user) {
                $query->where('items.empresa_id', $user->empresa_id);
            });

        $totalUnidades = (clone $baseInventario)->sum('inventario_area.cantidad');

        $totalUnidadesPorSucursal = (clone $baseInventario)
            ->join('areas', 'areas.id', '=', 'inventario_area.area_id')
            ->join('sucursales', 'sucursales.id', '=', 'areas.sucursal_id')
            ->select('sucursales.nombre as sucursal', DB::raw('SUM(inventario_area.cantidad) as total'))
            ->groupBy('sucursales.id', 'sucursales.nombre')
            ->orderByDesc('total')
            ->get();

        $itemsBajoMinimo = (clone $baseItem)
            ->where('estado', 'activo')
            ->withSum('inventarioArea as stock_total', 'cantidad')
            ->get()
            ->filter(function (Item $item) {
                return (float) $item->stock_total <= (float) $item->stock_minimo;
            })
            ->sortBy('stock_total')
            ->take(15)
            ->values();

        $ultimosMovimientos = MovimientoInventario::query()
            ->with(['item', 'areaOrigen', 'areaDestino', 'usuario'])
            ->when(!$user->esSuperAdmin(), function ($query) use ($user) {
                $query->whereHas('item', fn ($q) => $q->where('empresa_id', $user->empresa_id));
            })
            ->orderByDesc('created_at')
            ->take(10)
            ->get();

        $distribucionCategorias = (clone $baseInventario)
            ->join('categorias', 'categorias.id', '=', 'items.categoria_id')
            ->select('categorias.nombre as categoria', DB::raw('SUM(inventario_area.cantidad) as total'))
            ->groupBy('categorias.id', 'categorias.nombre')
            ->orderByDesc('total')
            ->get();

        return view('dashboard.index', compact(
            'totalItems',
            'totalUnidades',
            'totalUnidadesPorSucursal',
            'itemsBajoMinimo',
            'ultimosMovimientos',
            'distribucionCategorias',
        ));
    }
}