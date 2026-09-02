<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\InventarioArea;
use App\Models\Sucursal;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AreaController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Area::class);

        $user = auth()->user();

        $areas = Area::query()
            ->with(['sucursal.empresa', 'encargado'])
            ->when(!$user->esSuperAdmin(), function ($query) use ($user) {
                $query->whereHas('sucursal', function ($sub) use ($user) {
                    $sub->where('empresa_id', $user->empresa_id);
                });
            })
            ->when($user->esEncargadoArea(), function ($query) use ($user) {
                $query->where('encargado_id', $user->id);
            })
            ->when($request->filled('sucursal_id'), function ($query) use ($request) {
                $query->where('sucursal_id', $request->input('sucursal_id'));
            })
            ->when($request->filled('busqueda'), function ($query) use ($request) {
                $query->where('nombre', 'like', '%' . $request->input('busqueda') . '%');
            })
            ->orderBy('sucursal_id')
            ->paginate(10)
            ->withQueryString();

        $sucursales = Sucursal::query()
            ->when(!$user->esSuperAdmin(), function ($query) use ($user) {
                $query->where('empresa_id', $user->empresa_id);
            })
            ->get();

        return view('areas.index', compact('areas', 'sucursales'));
    }

    public function create(): View
    {
        $this->authorize('create', Area::class);

        $user = auth()->user();

        $sucursales = Sucursal::query()
            ->when(!$user->esSuperAdmin(), function ($query) use ($user) {
                $query->where('empresa_id', $user->empresa_id);
            })
            ->get();

        $encargados = User::query()
            ->when(!$user->esSuperAdmin(), function ($query) use ($user) {
                $query->where('empresa_id', $user->empresa_id);
            })
            ->where('estado', 'activo')
            ->orderBy('name')
            ->get();

        return view('areas.create', compact('sucursales', 'encargados'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Area::class);

        $datos = $request->validate([
            'sucursal_id' => ['required', 'exists:sucursales,id'],
            'encargado_id' => ['nullable', 'exists:users,id'],
            'nombre' => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string', 'max:500'],
            'estado' => ['required', 'in:activo,inactivo'],
        ]);

        $sucursal = Sucursal::findOrFail($datos['sucursal_id']);

        if (!auth()->user()->esSuperAdmin()
            && $sucursal->empresa_id !== auth()->user()->empresa_id) {
            abort(403, 'No puede crear áreas para otra empresa.');
        }

        Area::create($datos);

        return redirect()->route('areas.index')
            ->with('success', 'Área creada correctamente.');
    }

    public function show(Area $area): View
    {
        $this->authorize('view', $area);

        $area->load([
            'sucursal.empresa',
            'encargado',
            'items' => fn ($query) => $query->orderBy('nombre'),
        ]);

        return view('areas.show', compact('area'));
    }

    public function edit(Area $area): View
    {
        $this->authorize('update', $area);

        $user = auth()->user();

        $sucursales = Sucursal::query()
            ->when(!$user->esSuperAdmin(), function ($query) use ($user) {
                $query->where('empresa_id', $user->empresa_id);
            })
            ->get();

        $encargados = User::query()
            ->when(!$user->esSuperAdmin(), function ($query) use ($user) {
                $query->where('empresa_id', $user->empresa_id);
            })
            ->where('estado', 'activo')
            ->orderBy('name')
            ->get();

        return view('areas.edit', compact('area', 'sucursales', 'encargados'));
    }

    public function update(Request $request, Area $area): RedirectResponse
    {
        $this->authorize('update', $area);

        $datos = $request->validate([
            'sucursal_id' => ['required', 'exists:sucursales,id'],
            'encargado_id' => ['nullable', 'exists:users,id'],
            'nombre' => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string', 'max:500'],
            'estado' => ['required', 'in:activo,inactivo'],
        ]);

        if (!auth()->user()->esSuperAdmin()
            && $area->sucursal->empresa_id !== auth()->user()->empresa_id) {
            abort(403, 'No puede editar áreas de otra empresa.');
        }

        $sucursal = Sucursal::findOrFail($datos['sucursal_id']);
        if (!auth()->user()->esSuperAdmin()
            && $sucursal->empresa_id !== auth()->user()->empresa_id) {
            abort(403, 'No puede asignar un área a otra empresa.');
        }

        $area->update($datos);

        return redirect()->route('areas.index')
            ->with('success', 'Área actualizada correctamente.');
    }

    public function destroy(Area $area): RedirectResponse
    {
        $this->authorize('delete', $area);

        $tieneStock = InventarioArea::where('area_id', $area->id)
            ->where('cantidad', '>', 0)
            ->exists();

        if ($tieneStock) {
            return redirect()->route('areas.index')
                ->with('error', 'No se puede eliminar el área porque posee inventario activo. Debe trasladar o dar salida al stock primero.');
        }

        $area->delete();

        return redirect()->route('areas.index')
            ->with('success', 'Área eliminada correctamente.');
    }
}