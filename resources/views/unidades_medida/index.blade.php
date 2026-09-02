<x-app-layout>
    <div class="space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Unidades de medida</h1>
                <p class="text-sm text-gray-500 mt-1">Catálogo global de unidades de medida.</p>
            </div>
            @can('crear unidades de medida')
            <a href="{{ route('unidades-medida.create') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-md bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Nueva unidad
            </a>
            @endcan
        </div>

        <form method="GET" action="{{ route('unidades-medida.index') }}" class="flex gap-2">
            <input type="text" name="busqueda" value="{{ request('busqueda') }}" placeholder="Buscar unidad..."
                class="flex-1 sm:max-w-sm rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
            <button type="submit" class="px-4 py-2 rounded-md bg-gray-800 text-white text-sm hover:bg-gray-900">Buscar</button>
        </form>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Unidad</th>
                            <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Abreviatura</th>
                            <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Ítems</th>
                            <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($unidades as $unidad)
                            <tr class="hover:bg-gray-50">
                                <td class="px-5 py-3 font-medium text-gray-900">{{ $unidad->nombre }}</td>
                                <td class="px-5 py-3 text-center">
                                    <span class="inline-flex px-2.5 py-1 rounded-md bg-gray-100 text-gray-700 text-xs font-semibold">{{ $unidad->abreviatura }}</span>
                                </td>
                                <td class="px-5 py-3 text-center text-gray-600">{{ $unidad->items_count ?? $unidad->items->count() }}</td>
                                <td class="px-5 py-3">
                                    <div class="flex items-center justify-center gap-2">
                                        @can('editar unidades de medida')
                                        <a href="{{ route('unidades-medida.edit', $unidad) }}" class="text-amber-600 hover:text-amber-800 font-medium text-xs">Editar</a>
                                        @endcan
                                        @can('eliminar unidades de medida')
                                        <form method="POST" action="{{ route('unidades-medida.destroy', $unidad) }}" onsubmit="return confirm('¿Seguro que deseas eliminar esta unidad?');">
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
                                <td colspan="4" class="px-5 py-10 text-center text-gray-500">No hay unidades de medida registradas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($unidades->hasPages())
                <div class="px-5 py-3 border-t border-gray-200">
                    {{ $unidades->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>