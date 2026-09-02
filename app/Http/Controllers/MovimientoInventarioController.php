<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\InventarioArea;
use App\Models\Item;
use App\Models\MovimientoInventario;
use App\Services\InventarioService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class MovimientoInventarioController extends Controller
{
    protected InventarioService $service;

    public function __construct(InventarioService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request): View
    {
        $this->authorize('viewAny', MovimientoInventario::class);

        $user = auth()->user();

        $movimientos = MovimientoInventario::query()
            ->with(['item', 'areaOrigen.sucursal', 'areaDestino.sucursal', 'usuario'])
            ->when(!$user->esSuperAdmin(), function ($query) use ($user) {
                $query->whereHas('item', function ($sub) use ($user) {
                    $sub->where('empresa_id', $user->empresa_id);
                });
            })
            ->when($request->filled('tipo'), function ($query) use ($request) {
                $query->where('tipo', $request->input('tipo'));
            })
            ->when($request->filled('item_id'), function ($query) use ($request) {
                $query->where('item_id', $request->input('item_id'));
            })
            ->when($request->filled('usuario_id'), function ($query) use ($request) {
                $query->where('usuario_id', $request->input('usuario_id'));
            })
            ->when($request->filled('desde'), function ($query) use ($request) {
                $query->whereDate('created_at', '>=', $request->input('desde'));
            })
            ->when($request->filled('hasta'), function ($query) use ($request) {
                $query->whereDate('created_at', '<=', $request->input('hasta'));
            })
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        $items = Item::query()
            ->when(!$user->esSuperAdmin(), function ($query) use ($user) {
                $query->where('empresa_id', $user->empresa_id);
            })
            ->orderBy('nombre')
            ->get();

        return view('movimientos.index', compact('movimientos', 'items'));
    }

    public function indexEntrada(): View
    {
        $this->authorize('createEntrada', MovimientoInventario::class);

        return $this->formulario('entrada');
    }

    public function indexSalida(): View
    {
        $this->authorize('createSalida', MovimientoInventario::class);

        return $this->formulario('salida');
    }

    public function indexTraslado(): View
    {
        $this->authorize('createTraslado', MovimientoInventario::class);

        return $this->formulario('traslado');
    }

    public function indexAjuste(): View
    {
        $this->authorize('createAjuste', MovimientoInventario::class);

        return $this->formulario('ajuste');
    }

    private function formulario(string $tipo): View
    {
        $user = auth()->user();

        $items = Item::query()
            ->when(!$user->esSuperAdmin(), function ($query) use ($user) {
                $query->where('empresa_id', $user->empresa_id);
            })
            ->where('estado', 'activo')
            ->orderBy('nombre')
            ->get();

        $areas = Area::query()
            ->with(['sucursal.empresa'])
            ->where('estado', 'activo')
            ->whereHas('sucursal', function ($query) use ($user) {
                if (!$user->esSuperAdmin()) {
                    $query->where('empresa_id', $user->empresa_id);
                }
            })
            ->orderBy('nombre')
            ->get();

        if ($user->esEncargadoArea()) {
            $areas = $areas->filter(fn (Area $area) => $area->encargado_id === $user->id)->values();
        }

        return view('movimientos.' . $tipo, compact('items', 'areas', 'tipo'));
    }

    public function stockDisponible(Request $request): JsonResponse
    {
        $request->validate([
            'item_id' => ['required', 'exists:items,id'],
            'area_id' => ['required', 'exists:areas,id'],
        ]);

        $item = Item::findOrFail($request->input('item_id'));
        $area = Area::findOrFail($request->input('area_id'));

        if (!auth()->user()->esSuperAdmin()
            && $item->empresa_id !== auth()->user()->empresa_id) {
            abort(403);
        }

        $registro = InventarioArea::where('item_id', $item->id)
            ->where('area_id', $area->id)
            ->first();

        return response()->json([
            'disponible' => $registro ? (float) $registro->cantidad : 0,
            'unidad' => $item->unidadMedida?->abreviatura ?? 'und',
        ]);
    }

    public function entrada(Request $request): RedirectResponse
    {
        $this->authorize('createEntrada', MovimientoInventario::class);

        $datos = $request->validate([
            'item_id' => ['required', 'exists:items,id'],
            'area_id' => ['required', 'exists:areas,id'],
            'cantidad' => ['required', 'numeric', 'gt:0'],
            'motivo' => ['nullable', 'string', 'max:500'],
            'observacion' => ['nullable', 'string', 'max:1000'],
        ]);

        try {
            $this->service->entrada(
                Item::findOrFail($datos['item_id']),
                Area::findOrFail($datos['area_id']),
                (float) $datos['cantidad'],
                auth()->user(),
                $datos['motivo'],
                $datos['observacion'],
            );
        } catch (\Throwable $e) {
            return redirect()->back()->withInput()
                ->with('error', $e->getMessage());
        }

        return redirect()->route('movimientos.index')
            ->with('success', 'Entrada de inventario registrada correctamente.');
    }

    public function salida(Request $request): RedirectResponse
    {
        $this->authorize('createSalida', MovimientoInventario::class);

        $datos = $request->validate([
            'item_id' => ['required', 'exists:items,id'],
            'area_id' => ['required', 'exists:areas,id'],
            'cantidad' => ['required', 'numeric', 'gt:0'],
            'motivo' => ['required', 'string', 'max:500'],
            'observacion' => ['nullable', 'string', 'max:1000'],
        ]);

        try {
            $this->service->salida(
                Item::findOrFail($datos['item_id']),
                Area::findOrFail($datos['area_id']),
                (float) $datos['cantidad'],
                auth()->user(),
                $datos['motivo'],
                $datos['observacion'],
            );
        } catch (\Throwable $e) {
            return redirect()->back()->withInput()
                ->with('error', $e->getMessage());
        }

        return redirect()->route('movimientos.index')
            ->with('success', 'Salida de inventario registrada correctamente.');
    }

    public function traslado(Request $request): RedirectResponse
    {
        $this->authorize('createTraslado', MovimientoInventario::class);

        $datos = $request->validate([
            'item_id' => ['required', 'exists:items,id'],
            'area_origen_id' => ['required', 'exists:areas,id'],
            'area_destino_id' => ['required', 'exists:areas,id', 'different:area_origen_id'],
            'cantidad' => ['required', 'numeric', 'gt:0'],
            'motivo' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $this->service->traslado(
                Item::findOrFail($datos['item_id']),
                Area::findOrFail($datos['area_origen_id']),
                Area::findOrFail($datos['area_destino_id']),
                (float) $datos['cantidad'],
                auth()->user(),
                $datos['motivo'],
            );
        } catch (\Throwable $e) {
            return redirect()->back()->withInput()
                ->with('error', $e->getMessage());
        }

        return redirect()->route('movimientos.index')
            ->with('success', 'Traslado de inventario registrado correctamente.');
    }

    public function ajuste(Request $request): RedirectResponse
    {
        $this->authorize('createAjuste', MovimientoInventario::class);

        $datos = $request->validate([
            'item_id' => ['required', 'exists:items,id'],
            'area_id' => ['required', 'exists:areas,id'],
            'cantidad' => ['required', 'numeric', 'not_in:0'],
            'motivo' => ['required', 'string', 'max:500'],
            'observacion' => ['nullable', 'string', 'max:1000'],
        ]);

        try {
            $this->service->ajuste(
                Item::findOrFail($datos['item_id']),
                Area::findOrFail($datos['area_id']),
                (float) $datos['cantidad'],
                auth()->user(),
                $datos['motivo'],
                $datos['observacion'],
            );
        } catch (\Throwable $e) {
            return redirect()->back()->withInput()
                ->with('error', $e->getMessage());
        }

        return redirect()->route('movimientos.index')
            ->with('success', 'Ajuste de inventario registrado correctamente.');
    }
}