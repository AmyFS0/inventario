<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoriaController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Categoria::class);

        $categorias = Categoria::query()
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

        return view('categorias.index', compact('categorias'));
    }

    public function create(): View
    {
        $this->authorize('create', Categoria::class);

        return view('categorias.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Categoria::class);

        $datos = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
        ]);

        Categoria::create([
            'empresa_id' => auth()->user()->empresa_id,
            'nombre' => $datos['nombre'],
        ]);

        return redirect()->route('categorias.index')
            ->with('success', 'Categoría creada correctamente.');
    }

    public function edit(Categoria $categoria): View
    {
        $this->authorize('update', $categoria);

        return view('categorias.edit', compact('categoria'));
    }

    public function update(Request $request, Categoria $categoria): RedirectResponse
    {
        $this->authorize('update', $categoria);

        $datos = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
        ]);

        $categoria->update($datos);

        return redirect()->route('categorias.index')
            ->with('success', 'Categoría actualizada correctamente.');
    }

    public function destroy(Categoria $categoria): RedirectResponse
    {
        $this->authorize('delete', $categoria);

        if ($categoria->items()->exists()) {
            return redirect()->route('categorias.index')
                ->with('error', 'No se puede eliminar la categoría porque posee ítems asociados.');
        }

        $categoria->delete();

        return redirect()->route('categorias.index')
            ->with('success', 'Categoría eliminada correctamente.');
    }
}