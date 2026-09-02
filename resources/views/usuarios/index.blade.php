<x-app-layout>
    <div class="space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Usuarios</h1>
                <p class="text-sm text-gray-500 mt-1">Gestión de usuarios del sistema.</p>
            </div>
            @can('gestionar usuarios')
            <a href="{{ route('usuarios.create') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-md bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Nuevo usuario
            </a>
            @endcan
        </div>

        <form method="GET" action="{{ route('usuarios.index') }}" class="flex gap-2">
            <input type="text" name="busqueda" value="{{ request('busqueda') }}" placeholder="Buscar por nombre o correo..."
                class="flex-1 sm:max-w-sm rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
            <button type="submit" class="px-4 py-2 rounded-md bg-gray-800 text-white text-sm hover:bg-gray-900">Buscar</button>
        </form>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Usuario</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Empresa</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Roles</th>
                            <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Estado</th>
                            <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($usuarios as $usuario)
                            <tr class="hover:bg-gray-50 {{ $usuario->trashed() ? 'opacity-60' : '' }}">
                                <td class="px-5 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="h-9 w-9 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold">{{ strtoupper(substr($usuario->name, 0, 1)) }}</div>
                                        <div>
                                            <div class="font-medium text-gray-900">{{ $usuario->name }} @if($usuario->trashed())<span class="text-xs text-gray-400">(eliminado)</span>@endif</div>
                                            <div class="text-xs text-gray-400">{{ $usuario->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-3 text-gray-600">{{ $usuario->empresa?->nombre ?? '—' }}</td>
                                <td class="px-5 py-3">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach ($usuario->roles as $rol)
                                            <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-700">{{ $rol->name }}</span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-5 py-3 text-center">
                                    <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold {{ $usuario->estado === 'activo' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">{{ $usuario->estado }}</span>
                                </td>
                                <td class="px-5 py-3">
                                    <div class="flex items-center justify-center gap-2">
                                        @can('gestionar usuarios')
                                        <a href="{{ route('usuarios.edit', $usuario) }}" class="text-amber-600 hover:text-amber-800 font-medium text-xs">Editar</a>
                                        @if (!$usuario->trashed())
                                        <form method="POST" action="{{ route('usuarios.destroy', $usuario) }}" onsubmit="return confirm('¿Seguro que deseas eliminar este usuario?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800 font-medium text-xs">Eliminar</button>
                                        </form>
                                        @endif
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-10 text-center text-gray-500">No hay usuarios registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($usuarios->hasPages())
                <div class="px-5 py-3 border-t border-gray-200">
                    {{ $usuarios->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>