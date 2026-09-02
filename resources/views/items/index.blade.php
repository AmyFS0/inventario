<x-app-layout>
    <div class="space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Ítems del inventario</h1>
                <p class="text-sm text-gray-500 mt-1">Catálogo de productos y materiales.</p>
            </div>
            @can('crear items')
            <a href="{{ route('items.create') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-md bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Nuevo ítem
            </a>
            @endcan
        </div>

        <form method="GET" action="{{ route('items.index') }}" class="flex flex-wrap gap-2">
            <input type="text" name="busqueda" value="{{ request('busqueda') }}" placeholder="Buscar por nombre o SKU..."
                class="flex-1 sm:max-w-sm rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
            <select name="categoria_id" class="rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">Todas las categorías</option>
                @foreach ($categorias as $categoria)
                    <option value="{{ $categoria->id }}" @selected(request('categoria_id') == $categoria->id)>{{ $categoria->nombre }}</option>
                @endforeach
            </select>
            <select name="estado" class="rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">Todos los estados</option>
                <option value="activo" @selected(request('estado') === 'activo')>Activos</option>
                <option value="inactivo" @selected(request('estado') === 'inactivo')>Inactivos</option>
            </select>
            <button type="submit" class="px-4 py-2 rounded-md bg-gray-800 text-white text-sm hover:bg-gray-900">Filtrar</button>
        </form>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Ítem</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Categoría</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Unidad</th>
                            <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Stock total</th>
                            <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Costo unit.</th>
                            <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Estado</th>
                            <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($items as $item)
                            @php
                                $stock = (float) $item->stock_total;
                                $minimo = (float) $item->stock_minimo;
                                $badge = $stock <= 0 ? 'bg-red-100 text-red-700' : ($stock < $minimo ? 'bg-amber-100 text-amber-700' : 'bg-green-100 text-green-700');
                                $txt = $stock <= 0 ? 'Sin stock' : ($stock < $minimo ? 'Bajo' : 'OK');
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-5 py-3">
                                    <div class="flex items-center gap-3">
                                        @if ($item->imagen)
                                            <img src="{{ asset('storage/' . $item->imagen) }}" class="h-10 w-10 rounded-lg object-cover" alt="">
                                        @else
                                            <div class="h-10 w-10 rounded-lg bg-gray-100 flex items-center justify-center">
                                                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                            </div>
                                        @endif
                                        <div>
                                            <div class="font-medium text-gray-900">{{ $item->nombre }}</div>
                                            <div class="text-xs text-gray-400">{{ $item->sku }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-3 text-gray-600">{{ $item->categoria?->nombre }}</td>
                                <td class="px-5 py-3 text-gray-600">{{ $item->unidadMedida?->abreviatura }}</td>
                                <td class="px-5 py-3 text-right">
                                    <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold {{ $badge }}">{{ number_format($stock, 0) }} uds</span>
                                </td>
                                <td class="px-5 py-3 text-right text-gray-600">$ {{ number_format((float) $item->costo_unitario, 2) }}</td>
                                <td class="px-5 py-3 text-center">
                                    <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold {{ $item->estado === 'activo' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">{{ $item->estado }}</span>
                                </td>
                                <td class="px-5 py-3">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('items.show', $item) }}" class="text-indigo-600 hover:text-indigo-800 font-medium text-xs">Ver</a>
                                        @can('editar items')
                                        <a href="{{ route('items.edit', $item) }}" class="text-amber-600 hover:text-amber-800 font-medium text-xs">Editar</a>
                                        @endcan
                                        @can('eliminar items')
                                        <form method="POST" action="{{ route('items.destroy', $item) }}" onsubmit="return confirm('¿Seguro que deseas eliminar este ítem?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800 font-medium text-xs">Eliminar</button>
                                        </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-10 text-center text-gray-500">No hay ítems registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($items->hasPages())
                <div class="px-5 py-3 border-t border-gray-200">
                    {{ $items->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>