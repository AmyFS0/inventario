<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\InventarioArea;
use App\Models\Item;
use App\Models\Proveedor;
use App\Models\UnidadMedida;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ItemController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Item::class);

        $user = auth()->user();

        $items = Item::query()
            ->with(['categoria', 'unidadMedida', 'proveedor'])
            ->withSum('inventarioArea as stock_total', 'cantidad')
            ->when(!$user->esSuperAdmin(), function ($query) use ($user) {
                $query->where('empresa_id', $user->empresa_id);
            })
            ->when($request->filled('busqueda'), function ($query) use ($request) {
                $busqueda = $request->input('busqueda');
                $query->where(function ($sub) use ($busqueda) {
                    $sub->where('nombre', 'like', '%' . $busqueda . '%')
                        ->orWhere('sku', 'like', '%' . $busqueda . '%');
                });
            })
            ->when($request->filled('categoria_id'), function ($query) use ($request) {
                $query->where('categoria_id', $request->input('categoria_id'));
            })
            ->when($request->filled('estado'), function ($query) use ($request) {
                $query->where('estado', $request->input('estado'));
            })
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        $categorias = Categoria::query()
            ->when(!$user->esSuperAdmin(), function ($query) use ($user) {
                $query->where('empresa_id', $user->empresa_id);
            })
            ->orderBy('nombre')
            ->get();

        return view('items.index', compact('items', 'categorias'));
    }

    public function create(): View
    {
        $this->authorize('create', Item::class);

        $user = auth()->user();

        $categorias = Categoria::query()
            ->when(!$user->esSuperAdmin(), function ($query) use ($user) {
                $query->where('empresa_id', $user->empresa_id);
            })
            ->orderBy('nombre')
            ->get();

        $unidades = UnidadMedida::orderBy('nombre')->get();

        $proveedores = Proveedor::query()
            ->when(!$user->esSuperAdmin(), function ($query) use ($user) {
                $query->where('empresa_id', $user->empresa_id);
            })
            ->orderBy('nombre')
            ->get();

        return view('items.create', compact('categorias', 'unidades', 'proveedores'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Item::class);

        $user = auth()->user();

        $datos = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'sku' => ['nullable', 'string', 'max:100'],
            'categoria_id' => ['required', 'exists:categorias,id'],
            'unidad_medida_id' => ['required', 'exists:unidades_medida,id'],
            'proveedor_id' => ['nullable', 'exists:proveedores,id'],
            'descripcion' => ['nullable', 'string', 'max:1000'],
            'imagen' => ['nullable', 'image', 'max:2048'],
            'costo_unitario' => ['nullable', 'numeric', 'min:0'],
            'stock_minimo' => ['nullable', 'numeric', 'min:0'],
            'estado' => ['required', 'in:activo,inactivo'],
        ]);

        $siguiente = Item::withTrashed()->where('empresa_id', $user->empresa_id)->count() + 1;
        $datos['sku'] = $datos['sku'] ?: 'SKU-' . str_pad((string) $siguiente, 4, '0', STR_PAD_LEFT);
        $datos['costo_unitario'] = $datos['costo_unitario'] ?? 0;
        $datos['stock_minimo'] = $datos['stock_minimo'] ?? 0;

        $categoria = Categoria::findOrFail($datos['categoria_id']);
        if (!auth()->user()->esSuperAdmin() && $categoria->empresa_id !== $user->empresa_id) {
            abort(403, 'Categoría inválida para su empresa.');
        }

        $datos['empresa_id'] = $user->empresa_id;

        if ($request->hasFile('imagen')) {
            $datos['imagen'] = $request->file('imagen')->store('items', 'public');
        }

        try {
            Item::create($datos);
        } catch (\Illuminate\Database\QueryException $e) {
            if (DB::getDriverName() === 'mysql' && $e->errorInfo[1] === 1062) {
                return redirect()->back()->withInput()
                    ->with('error', 'El código SKU ya existe para esta empresa.');
            }
            throw $e;
        }

        return redirect()->route('items.index')
            ->with('success', 'Ítem creado correctamente.');
    }

    public function show(Item $item): View
    {
        $this->authorize('view', $item);

        $item->load([
            'empresa',
            'categoria',
            'unidadMedida',
            'proveedor',
            'inventarioArea.area.sucursal',
            'inventarioArea.area.encargado',
        ]);

        return view('items.show', compact('item'));
    }

    public function edit(Item $item): View
    {
        $this->authorize('update', $item);

        $user = auth()->user();

        $categorias = Categoria::query()
            ->when(!$user->esSuperAdmin(), function ($query) use ($user) {
                $query->where('empresa_id', $user->empresa_id);
            })
            ->orderBy('nombre')
            ->get();

        $unidades = UnidadMedida::orderBy('nombre')->get();

        $proveedores = Proveedor::query()
            ->when(!$user->esSuperAdmin(), function ($query) use ($user) {
                $query->where('empresa_id', $user->empresa_id);
            })
            ->orderBy('nombre')
            ->get();

        return view('items.edit', compact('item', 'categorias', 'unidades', 'proveedores'));
    }

    public function update(Request $request, Item $item): RedirectResponse
    {
        $this->authorize('update', $item);

        $datos = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'sku' => ['nullable', 'string', 'max:100'],
            'categoria_id' => ['required', 'exists:categorias,id'],
            'unidad_medida_id' => ['required', 'exists:unidades_medida,id'],
            'proveedor_id' => ['nullable', 'exists:proveedores,id'],
            'descripcion' => ['nullable', 'string', 'max:1000'],
            'imagen' => ['nullable', 'image', 'max:2048'],
            'costo_unitario' => ['nullable', 'numeric', 'min:0'],
            'stock_minimo' => ['nullable', 'numeric', 'min:0'],
            'estado' => ['required', 'in:activo,inactivo'],
        ]);

        $datos['costo_unitario'] = $datos['costo_unitario'] ?? 0;
        $datos['stock_minimo'] = $datos['stock_minimo'] ?? 0;
        $datos['sku'] = $datos['sku'] ?: $item->sku;

        if ($request->hasFile('imagen')) {
            $datos['imagen'] = $request->file('imagen')->store('items', 'public');
        }

        try {
            $item->update($datos);
        } catch (\Illuminate\Database\QueryException $e) {
            if (DB::getDriverName() === 'mysql' && $e->errorInfo[1] === 1062) {
                return redirect()->back()->withInput()
                    ->with('error', 'El código SKU ya existe para esta empresa.');
            }
            throw $e;
        }

        return redirect()->route('items.index')
            ->with('success', 'Ítem actualizado correctamente.');
    }

    public function destroy(Item $item): RedirectResponse
    {
        $this->authorize('delete', $item);

        $tieneStock = InventarioArea::where('item_id', $item->id)
            ->where('cantidad', '>', 0)
            ->exists();

        if ($tieneStock) {
            return redirect()->route('items.index')
                ->with('error', 'No se puede eliminar el ítem porque posee stock activo. Debe trasladar o dar salida al inventario primero.');
        }

        $item->delete();

        return redirect()->route('items.index')
            ->with('success', 'Ítem eliminado correctamente.');
    }
}