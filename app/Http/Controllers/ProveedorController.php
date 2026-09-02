<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProveedorController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Proveedor::class);

        $proveedores = Proveedor::query()
            ->with('empresa')
            ->when(!auth()->user()->esSuperAdmin(), function ($query) {
                $query->where('empresa_id', auth()->user()->empresa_id);
            })
            ->when($request->filled('busqueda'), function ($query) use ($request) {
                $query->where('nombre', 'like', '%' . $request->input('busqueda') . '%');
            })
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        return view('proveedores.index', compact('proveedores'));
    }

    public function create(): View
    {
        $this->authorize('create', Proveedor::class);

        return view('proveedores.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Proveedor::class);

        $datos = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'contacto' => ['nullable', 'string', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:30'],
            'correo' => ['nullable', 'email', 'max:255'],
        ]);

        Proveedor::create([
            'empresa_id' => auth()->user()->empresa_id,
            ...$datos,
        ]);

        return redirect()->route('proveedores.index')
            ->with('success', 'Proveedor creado correctamente.');
    }

    public function edit(Proveedor $proveedor): View
    {
        $this->authorize('update', $proveedor);

        return view('proveedores.edit', compact('proveedor'));
    }

    public function update(Request $request, Proveedor $proveedor): RedirectResponse
    {
        $this->authorize('update', $proveedor);

        $datos = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'contacto' => ['nullable', 'string', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:30'],
            'correo' => ['nullable', 'email', 'max:255'],
        ]);

        $proveedor->update($datos);

        return redirect()->route('proveedores.index')
            ->with('success', 'Proveedor actualizado correctamente.');
    }

    public function destroy(Proveedor $proveedor): RedirectResponse
    {
        $this->authorize('delete', $proveedor);

        if ($proveedor->items()->exists()) {
            return redirect()->route('proveedores.index')
                ->with('error', 'No se puede eliminar el proveedor porque posee ítems asociados.');
        }

        $proveedor->delete();

        return redirect()->route('proveedores.index')
            ->with('success', 'Proveedor eliminado correctamente.');
    }
}