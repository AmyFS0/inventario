<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmpresaController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Empresa::class);

        $empresas = Empresa::query()
            ->when($request->filled('busqueda'), function ($query) use ($request) {
                $query->where('nombre', 'like', '%' . $request->input('busqueda') . '%')
                    ->orWhere('identificacion_fiscal', 'like', '%' . $request->input('busqueda') . '%');
            })
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        return view('empresas.index', compact('empresas'));
    }

    public function create(): View
    {
        $this->authorize('create', Empresa::class);

        return view('empresas.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Empresa::class);

        $datos = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'identificacion_fiscal' => ['nullable', 'string', 'max:50'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:30'],
            'correo' => ['nullable', 'email', 'max:255'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'estado' => ['required', 'in:activo,inactivo'],
        ]);

        if ($request->hasFile('logo')) {
            $datos['logo'] = $request->file('logo')->store('logos', 'public');
        }

        Empresa::create($datos);

        return redirect()->route('empresas.index')
            ->with('success', 'Empresa creada correctamente.');
    }

    public function show(Empresa $empresa): View
    {
        $this->authorize('view', $empresa);

        $empresa->load('sucursales');

        return view('empresas.show', compact('empresa'));
    }

    public function edit(Empresa $empresa): View
    {
        $this->authorize('update', $empresa);

        return view('empresas.edit', compact('empresa'));
    }

    public function update(Request $request, Empresa $empresa): RedirectResponse
    {
        $this->authorize('update', $empresa);

        $datos = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'identificacion_fiscal' => ['nullable', 'string', 'max:50'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:30'],
            'correo' => ['nullable', 'email', 'max:255'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'estado' => ['required', 'in:activo,inactivo'],
        ]);

        if ($request->hasFile('logo')) {
            $datos['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $empresa->update($datos);

        return redirect()->route('empresas.index')
            ->with('success', 'Empresa actualizada correctamente.');
    }

    public function destroy(Empresa $empresa): RedirectResponse
    {
        $this->authorize('delete', $empresa);

        $empresa->delete();

        return redirect()->route('empresas.index')
            ->with('success', 'Empresa eliminada correctamente.');
    }
}