<x-app-layout>
    <div class="space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Categorías</h1>
                <p class="text-sm text-gray-500 mt-1">Clasificación de los ítems del inventario.</p>
            </div>
            @can('crear categorias')
            <a href="{{ route('categorias.create') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-md bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Nueva categoría
            </a>
            @endcan
        </div>

        <form method="GET" action="{{ route('categorias.index') }}" class="flex gap-2">
            <input type="text" name="busqueda" value="{{ request('busqueda') }}" placeholder="Buscar categoría..."
                class="flex-1 sm:max-w-sm rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
            <button type="submit" class="px-4 py-2 rounded-md bg-gray-800 text-white text-sm hover:bg-gray-900">Buscar</button>
        </form>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div id="itemsAreaGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 p-5">
                @forelse ($categorias as $categoria)
                    <div class="rounded-lg border border-gray-200 p-4 flex flex-col">
                        <div class="flex items-start justify-between">
                            <div class="h-10 w-10 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                            </div>
                            @if (!auth()->user()->esSuperAdmin())
                                <span class="text-xs text-gray-400">{{ $categoria->empresa?->nombre }}</span>
                            @endif
                        </div>
                        <h3 class="mt-3 font-semibold text-gray-900">{{ $categoria->nombre }}</h3>
                        <p class="text-xs text-gray-400 mt-1">{{ $categoria->items_count ?? $categoria->items->count() }} ítem(s)</p>
                        <div class="mt-3 flex items-center justify-end gap-2 border-t border-gray-100 pt-3">
                            @can('editar categorias')
                            <a href="{{ route('categorias.edit', $categoria) }}" class="text-amber-600 hover:text-amber-800 font-medium text-xs">Editar</a>
                            @endcan
                            @can('eliminar categorias')
                            <form method="POST" action="{{ route('categorias.destroy', $categoria) }}" onsubmit="return confirm('¿Seguro que deseas eliminar esta categoría?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 font-medium text-xs">Eliminar</button>
                            </form>
                            @endcan
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-10 text-center text-gray-500">No hay categorías registradas.</div>
                @endforelse
            </div>
            @if ($categorias->hasPages())
                <div class="px-5 py-3 border-t border-gray-200">
                    {{ $categorias->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>