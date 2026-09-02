<x-app-layout>
    <div class="space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Empresas</h1>
                <p class="text-sm text-gray-500 mt-1">Gestión de empresas de la plataforma.</p>
            </div>
            @can('crear empresas')
            <a href="{{ route('empresas.create') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-md bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Nueva empresa
            </a>
            @endcan
        </div>

        <!-- Búsqueda -->
        <form method="GET" action="{{ route('empresas.index') }}" class="flex gap-2">
            <input type="text" name="busqueda" value="{{ request('busqueda') }}" placeholder="Buscar por nombre o RTN..."
                class="flex-1 sm:max-w-sm rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
            <button type="submit" class="px-4 py-2 rounded-md bg-gray-800 text-white text-sm hover:bg-gray-900">Buscar</button>
        </form>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Nombre</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">RTN</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Teléfono</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Correo</th>
                            <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Sucursales</th>
                            <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Estado</th>
                            <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($empresas as $empresa)
                            <tr class="hover:bg-gray-50">
                                <td class="px-5 py-3">
                                    <div class="flex items-center gap-3">
                                        @if ($empresa->logo)
                                            <img src="{{ asset('storage/' . $empresa->logo) }}" class="h-9 w-9 rounded-full object-cover" alt="">
                                        @else
                                            <div class="h-9 w-9 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold">{{ strtoupper(substr($empresa->nombre, 0, 1)) }}</div>
                                        @endif
                                        <span class="font-medium text-gray-900">{{ $empresa->nombre }}</span>
                                    </div>
                                </td>
                                <td class="px-5 py-3 text-gray-600">{{ $empresa->identificacion_fiscal }}</td>
                                <td class="px-5 py-3 text-gray-600">{{ $empresa->telefono }}</td>
                                <td class="px-5 py-3 text-gray-600">{{ $empresa->correo }}</td>
                                <td class="px-5 py-3 text-center text-gray-600">{{ $empresa->sucursales_count ?? $empresa->sucursales->count() }}</td>
                                <td class="px-5 py-3 text-center">
                                    <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold {{ $empresa->estado === 'activo' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">{{ $empresa->estado }}</span>
                                </td>
                                <td class="px-5 py-3">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('empresas.show', $empresa) }}" class="text-indigo-600 hover:text-indigo-800 font-medium text-xs">Ver</a>
                                        @can('editar empresas')
                                        <a href="{{ route('empresas.edit', $empresa) }}" class="text-amber-600 hover:text-amber-800 font-medium text-xs">Editar</a>
                                        @endcan
                                        @can('eliminar empresas')
                                        <form method="POST" action="{{ route('empresas.destroy', $empresa) }}" onsubmit="return confirm('¿Seguro que deseas eliminar esta empresa? Esta acción no se puede deshacer.');">
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
                                <td colspan="7" class="px-5 py-10 text-center text-gray-500">No hay empresas registradas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($empresas->hasPages())
                <div class="px-5 py-3 border-t border-gray-200">
                    {{ $empresas->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>