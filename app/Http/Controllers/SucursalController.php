<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\Sucursal;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SucursalController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Sucursal::class);

        $sucursales = Sucursal::query()
            ->with('empresa')
            ->when(!auth()->user()->esSuperAdmin(), function ($query) {
                $query->where('empresa_id', auth()->user()->empresa_id);
            })
            ->when($request->filled('empresa_id'), function ($query) use ($request) {
                $query->where('empresa_id', $request->input('empresa_id'));
            })
            ->when($request->filled('busqueda'), function ($query) use ($request) {
                $query->where('nombre', 'like', '%' . $request->input('busqueda') . '%');
            })
            ->orderBy('empresa_id')
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        $empresas = Empresa::when(!auth()->user()->esSuperAdmin(), function ($query) {
            $query->where('id', auth()->user()->empresa_id);
        })->get();

        return view('sucursales.index', compact('sucursales', 'empresas'));
    }

    public function create(): View
    {
        $this->authorize('create', Sucursal::class);

        $empresas = Empresa::when(!auth()->user()->esSuperAdmin(), function ($query) {
            $query->where('id', auth()->user()->empresa_id);
        })->get();

        return view('sucursales.create', compact('empresas'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Sucursal::class);

        $datos = $request->validate([
            'empresa_id' => ['required', 'exists:empresas,id'],
            'nombre' => ['required', 'string', 'max:255'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:30'],
            'estado' => ['required', 'in:activo,inactivo'],
        ]);

        if (!auth()->user()->esSuperAdmin()
            && $datos['empresa_id'] != auth()->user()->empresa_id) {
            abort(403, 'No puede crear sucursales para otra empresa.');
        }

        Sucursal::create($datos);

        return redirect()->route('sucursales.index')
            ->with('success', 'Sucursal creada correctamente.');
    }

    public function show(Sucursal $sucursal): View
    {
        $this->authorize('view', $sucursal);

        $sucursal->load(['empresa', 'areas.encargado', 'areas.items']);

        return view('sucursales.show', compact('sucursal'));
    }

    public function edit(Sucursal $sucursal): View
    {
        $this->authorize('update', $sucursal);

        $empresas = Empresa::when(!auth()->user()->esSuperAdmin(), function ($query) {
            $query->where('id', auth()->user()->empresa_id);
        })->get();

        return view('sucursales.edit', compact('sucursal', 'empresas'));
    }

    public function update(Request $request, Sucursal $sucursal): RedirectResponse
    {
        $this->authorize('update', $sucursal);

        $datos = $request->validate([
            'empresa_id' => ['required', 'exists:empresas,id'],
            'nombre' => ['required', 'string', 'max:255'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:30'],
            'estado' => ['required', 'in:activo,inactivo'],
        ]);

        if (!auth()->user()->esSuperAdmin()
            && $datos['empresa_id'] != auth()->user()->empresa_id) {
            abort(403, 'No puede asignar la sucursal a otra empresa.');
        }

        $sucursal->update($datos);

        return redirect()->route('sucursales.index')
            ->with('success', 'Sucursal actualizada correctamente.');
    }

    public function destroy(Sucursal $sucursal): RedirectResponse
    {
        $this->authorize('delete', $sucursal);

        $sucursal->delete();

        return redirect()->route('sucursales.index')
            ->with('success', 'Sucursal eliminada correctamente.');
    }
}