<x-app-layout>
    <div class="space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Sucursales</h1>
                <p class="text-sm text-gray-500 mt-1">Sucursales y ubicaciones registradas.</p>
            </div>
            @can('crear sucursales')
            <a href="{{ route('sucursales.create') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-md bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Nueva sucursal
            </a>
            @endcan
        </div>

        <form method="GET" action="{{ route('sucursales.index') }}" class="flex flex-wrap gap-2">
            <input type="text" name="busqueda" value="{{ request('busqueda') }}" placeholder="Buscar por nombre..."
                class="flex-1 sm:max-w-sm rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
            @if ($empresas->count() > 1)
            <select name="empresa_id" class="rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">Todas las empresas</option>
                @foreach ($empresas as $empresa)
                    <option value="{{ $empresa->id }}" @selected(request('empresa_id') == $empresa->id)>{{ $empresa->nombre }}</option>
                @endforeach
            </select>
            @endif
            <button type="submit" class="px-4 py-2 rounded-md bg-gray-800 text-white text-sm hover:bg-gray-900">Filtrar</button>
        </form>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Sucursal</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Empresa</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Dirección</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Teléfono</th>
                            <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Áreas</th>
                            <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Estado</th>
                            <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($sucursales as $sucursal)
                            <tr class="hover:bg-gray-50">
                                <td class="px-5 py-3 font-medium text-gray-900">{{ $sucursal->nombre }}</td>
                                <td class="px-5 py-3 text-gray-600">{{ $sucursal->empresa?->nombre }}</td>
                                <td class="px-5 py-3 text-gray-600">{{ $sucursal->direccion }}</td>
                                <td class="px-5 py-3 text-gray-600">{{ $sucursal->telefono }}</td>
                                <td class="px-5 py-3 text-center text-gray-600">{{ $sucursal->areas_count ?? $sucursal->areas->count() }}</td>
                                <td class="px-5 py-3 text-center">
                                    <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold {{ $sucursal->estado === 'activo' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">{{ $sucursal->estado }}</span>
                                </td>
                                <td class="px-5 py-3">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('sucursales.show', $sucursal) }}" class="text-indigo-600 hover:text-indigo-800 font-medium text-xs">Ver</a>
                                        @can('editar sucursales')
                                        <a href="{{ route('sucursales.edit', $sucursal) }}" class="text-amber-600 hover:text-amber-800 font-medium text-xs">Editar</a>
                                        @endcan
                                        @can('eliminar sucursales')
                                        <form method="POST" action="{{ route('sucursales.destroy', $sucursal) }}" onsubmit="return confirm('¿Seguro que deseas eliminar esta sucursal?');">
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
                                <td colspan="7" class="px-5 py-10 text-center text-gray-500">No hay sucursales registradas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($sucursales->hasPages())
                <div class="px-5 py-3 border-t border-gray-200">
                    {{ $sucursales->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>