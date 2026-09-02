<?php

namespace App\Http\Controllers;

use App\Exports\InventarioExport;
use App\Exports\MovimientosExport;
use App\Models\Area;
use App\Models\InventarioArea;
use App\Models\Item;
use App\Models\MovimientoInventario;
use App\Models\Sucursal;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReporteController extends Controller
{
    public function inventario(Request $request): View
    {
        abort_unless(auth()->user()->can('ver reportes'), 403);

        $user = auth()->user();

        $query = InventarioArea::query()
            ->join('items', 'items.id', '=', 'inventario_area.item_id')
            ->join('areas', 'areas.id', '=', 'inventario_area.area_id')
            ->join('sucursales', 'sucursales.id', '=', 'areas.sucursal_id')
            ->join('empresas', 'empresas.id', '=', 'sucursales.empresa_id')
            ->join('categorias', 'categorias.id', '=', 'items.categoria_id', 'left')
            ->join('unidades_medida', 'unidades_medida.id', '=', 'items.unidad_medida_id', 'left')
            ->leftJoin('users', 'users.id', '=', 'areas.encargado_id')
            ->select(
                'empresas.nombre as empresa',
                'sucursales.nombre as sucursal',
                'areas.nombre as area',
                'items.sku',
                'items.nombre as item',
                'categorias.nombre as categoria',
                'inventario_area.cantidad',
                'unidades_medida.abreviatura as unidad',
                'users.name as responsable',
                'items.estado'
            )
            ->where('inventario_area.cantidad', '>', 0);

        if (!$user->esSuperAdmin()) {
            $query->where('empresas.id', $user->empresa_id);
        }

        if ($request->filled('empresa_id')) {
            $query->where('empresas.id', $request->input('empresa_id'));
        }
        if ($request->filled('sucursal_id')) {
            $query->where('sucursales.id', $request->input('sucursal_id'));
        }
        if ($request->filled('area_id')) {
            $query->where('areas.id', $request->input('area_id'));
        }
        if ($request->filled('categoria_id')) {
            $query->where('items.categoria_id', $request->input('categoria_id'));
        }
        if ($request->filled('busqueda')) {
            $busqueda = $request->input('busqueda');
            $query->where(function ($sub) use ($busqueda) {
                $sub->where('items.nombre', 'like', '%' . $busqueda . '%')
                    ->orWhere('items.sku', 'like', '%' . $busqueda . '%');
            });
        }

        $filas = $query->orderBy('sucursal')->orderBy('area')->orderBy('item')->get();

        $filtros = $request->only(['empresa_id', 'sucursal_id', 'area_id', 'categoria_id', 'busqueda']);
        $sucursales = $this->sucursalesAccesibles();
        $areas = $this->areasAccesibles();
        $itemsCategorias = \App\Models\Categoria::query()
            ->when(!$user->esSuperAdmin(), function ($q) use ($user) {
                $q->where('empresa_id', $user->empresa_id);
            })
            ->orderBy('nombre')
            ->get();

        return view('reportes.inventario', compact('filas', 'filtros', 'sucursales', 'areas', 'itemsCategorias'));
    }

    public function movimientos(Request $request): View
    {
        abort_unless(auth()->user()->can('ver reportes'), 403);

        $user = auth()->user();

        $query = MovimientoInventario::query()
            ->with(['item', 'areaOrigen', 'areaDestino', 'usuario']);

        if (!$user->esSuperAdmin()) {
            $query->whereHas('item', function ($q) use ($user) {
                $q->where('empresa_id', $user->empresa_id);
            });
        }

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->input('tipo'));
        }
        if ($request->filled('item_id')) {
            $query->where('item_id', $request->input('item_id'));
        }
        if ($request->filled('usuario_id')) {
            $query->where('usuario_id', $request->input('usuario_id'));
        }
        if ($request->filled('desde')) {
            $query->whereDate('created_at', '>=', $request->input('desde'));
        }
        if ($request->filled('hasta')) {
            $query->whereDate('created_at', '<=', $request->input('hasta'));
        }

        $filas = $query->orderByDesc('created_at')->get();

        $filtros = $request->only(['tipo', 'item_id', 'usuario_id', 'desde', 'hasta']);

        $items = Item::query()
            ->when(!auth()->user()->esSuperAdmin(), function ($q) {
                $q->where('empresa_id', auth()->user()->empresa_id);
            })
            ->orderBy('nombre')
            ->get();

        $usuarios = User::query()
            ->when(!$user->esSuperAdmin(), function ($q) use ($user) {
                $q->where('empresa_id', $user->empresa_id);
            })
            ->orderBy('name')
            ->get();

        return view('reportes.movimientos', compact('filas', 'filtros', 'items', 'usuarios'));
    }

    public function exportarInventarioExcel(Request $request): BinaryFileResponse
    {
        abort_unless(auth()->user()->can('exportar reportes'), 403);

        $filas = $this->filasInventario($request);

        return Excel::download(new InventarioExport($filas), 'reporte-inventario.xlsx');
    }

    public function exportarInventarioPdf(Request $request)
    {
        abort_unless(auth()->user()->can('exportar reportes'), 403);

        $filas = $this->filasInventario($request);

        $fecha = now()->format('d/m/Y H:i');

        $pdf = Pdf::loadView('reportes.pdf.inventario', compact('filas', 'fecha'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('reporte-inventario.pdf');
    }

    public function exportarMovimientosExcel(Request $request): BinaryFileResponse
    {
        abort_unless(auth()->user()->can('exportar reportes'), 403);

        $filas = $this->filasMovimientos($request);

        return Excel::download(new MovimientosExport($filas), 'reporte-movimientos.xlsx');
    }

    public function exportarMovimientosPdf(Request $request)
    {
        abort_unless(auth()->user()->can('exportar reportes'), 403);

        $filas = $this->filasMovimientos($request);

        $fecha = now()->format('d/m/Y H:i');

        $pdf = Pdf::loadView('reportes.pdf.movimientos', compact('filas', 'fecha'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('reporte-movimientos.pdf');
    }

    private function filasInventario(Request $request): array
    {
        $user = auth()->user();

        $query = InventarioArea::query()
            ->join('items', 'items.id', '=', 'inventario_area.item_id')
            ->join('areas', 'areas.id', '=', 'inventario_area.area_id')
            ->join('sucursales', 'sucursales.id', '=', 'areas.sucursal_id')
            ->join('empresas', 'empresas.id', '=', 'sucursales.empresa_id')
            ->join('categorias', 'categorias.id', '=', 'items.categoria_id', 'left')
            ->join('unidades_medida', 'unidades_medida.id', '=', 'items.unidad_medida_id', 'left')
            ->leftJoin('users', 'users.id', '=', 'areas.encargado_id')
            ->select(
                'empresas.nombre as empresa',
                'sucursales.nombre as sucursal',
                'areas.nombre as area',
                'items.sku',
                'items.nombre as item',
                'categorias.nombre as categoria',
                'inventario_area.cantidad',
                'unidades_medida.abreviatura as unidad',
                'users.name as responsable',
                'items.estado'
            )
            ->where('inventario_area.cantidad', '>', 0);

        if (!$user->esSuperAdmin()) {
            $query->where('empresas.id', $user->empresa_id);
        }

        if ($request->filled('empresa_id')) {
            $query->where('empresas.id', $request->input('empresa_id'));
        }
        if ($request->filled('sucursal_id')) {
            $query->where('sucursales.id', $request->input('sucursal_id'));
        }
        if ($request->filled('area_id')) {
            $query->where('areas.id', $request->input('area_id'));
        }
        if ($request->filled('categoria_id')) {
            $query->where('items.categoria_id', $request->input('categoria_id'));
        }
        if ($request->filled('busqueda')) {
            $busqueda = $request->input('busqueda');
            $query->where(function ($sub) use ($busqueda) {
                $sub->where('items.nombre', 'like', '%' . $busqueda . '%')
                    ->orWhere('items.sku', 'like', '%' . $busqueda . '%');
            });
        }

        return $query->get()->map(function ($row) {
            $row->responsable = $row->responsable ?? 'Sin asignar';
            $row->unidad = $row->unidad ?? 'und';
            return $row;
        })->all();
    }

    private function filasMovimientos(Request $request): array
    {
        $user = auth()->user();

        $query = MovimientoInventario::query()
            ->with(['item', 'areaOrigen', 'areaDestino', 'usuario']);

        if (!$user->esSuperAdmin()) {
            $query->whereHas('item', function ($q) use ($user) {
                $q->where('empresa_id', $user->empresa_id);
            });
        }

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->input('tipo'));
        }
        if ($request->filled('item_id')) {
            $query->where('item_id', $request->input('item_id'));
        }
        if ($request->filled('usuario_id')) {
            $query->where('usuario_id', $request->input('usuario_id'));
        }
        if ($request->filled('desde')) {
            $query->whereDate('created_at', '>=', $request->input('desde'));
        }
        if ($request->filled('hasta')) {
            $query->whereDate('created_at', '<=', $request->input('hasta'));
        }

        return $query->get()->map(function (MovimientoInventario $mov) {
            return [
                'fecha' => $mov->created_at->format('d/m/Y H:i'),
                'tipo' => strtoupper($mov->tipo),
                'item' => $mov->item?->nombre ?? 'N/A',
                'sku' => $mov->item?->sku ?? 'N/A',
                'cantidad' => $mov->cantidad,
                'area_origen' => $mov->areaOrigen?->nombre ?? '-',
                'area_destino' => $mov->areaDestino?->nombre ?? '-',
                'usuario' => $mov->usuario?->name ?? 'N/A',
                'motivo' => $mov->motivo ?? '',
            ];
        })->all();
    }

    private function sucursalesAccesibles()
    {
        return Sucursal::query()
            ->with('empresa')
            ->when(!auth()->user()->esSuperAdmin(), function ($q) {
                $q->where('empresa_id', auth()->user()->empresa_id);
            })
            ->orderBy('nombre')
            ->get();
    }

    private function areasAccesibles()
    {
        $user = auth()->user();

        return Area::query()
            ->with('sucursal')
            ->where('estado', 'activo')
            ->when(!$user->esSuperAdmin(), function ($q) use ($user) {
                $q->whereHas('sucursal', fn ($sub) => $sub->where('empresa_id', $user->empresa_id));
            })
            ->orderBy('nombre')
            ->get();
    }
}