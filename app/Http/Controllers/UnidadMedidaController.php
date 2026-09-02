<?php

namespace App\Http\Controllers;

use App\Models\UnidadMedida;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UnidadMedidaController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', UnidadMedida::class);

        $unidades = UnidadMedida::query()
            ->when($request->filled('busqueda'), function ($query) use ($request) {
                $query->where('nombre', 'like', '%' . $request->input('busqueda') . '%');
            })
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        return view('unidades_medida.index', compact('unidades'));
    }

    public function create(): View
    {
        $this->authorize('create', UnidadMedida::class);

        return view('unidades_medida.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', UnidadMedida::class);

        $datos = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'abreviatura' => ['nullable', 'string', 'max:20'],
        ]);

        UnidadMedida::create($datos);

        return redirect()->route('unidades-medida.index')
            ->with('success', 'Unidad de medida creada correctamente.');
    }

    public function edit(UnidadMedida $unidadMedida): View
    {
        $this->authorize('update', $unidadMedida);

        return view('unidades_medida.edit', compact('unidadMedida'));
    }

    public function update(Request $request, UnidadMedida $unidadMedida): RedirectResponse
    {
        $this->authorize('update', $unidadMedida);

        $datos = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'abreviatura' => ['nullable', 'string', 'max:20'],
        ]);

        $unidadMedida->update($datos);

        return redirect()->route('unidades-medida.index')
            ->with('success', 'Unidad de medida actualizada correctamente.');
    }

    public function destroy(UnidadMedida $unidadMedida): RedirectResponse
    {
        $this->authorize('delete', $unidadMedida);

        if ($unidadMedida->items()->exists()) {
            return redirect()->route('unidades-medida.index')
                ->with('error', 'No se puede eliminar la unidad porque posee ítems asociados.');
        }

        $unidadMedida->delete();

        return redirect()->route('unidades-medida.index')
            ->with('success', 'Unidad de medida eliminada correctamente.');
    }
}