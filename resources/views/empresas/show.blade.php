<x-app-layout>
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center gap-4">
                @if ($empresa->logo)
                    <img src="{{ asset('storage/' . $empresa->logo) }}" class="h-14 w-14 rounded-full object-cover" alt="">
                @else
                    <div class="h-14 w-14 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-xl font-bold">{{ strtoupper(substr($empresa->nombre, 0, 1)) }}</div>
                @endif
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $empresa->nombre }}</h1>
                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $empresa->estado === 'activo' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">{{ $empresa->estado }}</span>
                </div>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('empresas.index') }}" class="px-4 py-2 rounded-md border border-gray-300 text-sm text-gray-700 hover:bg-gray-50">← Volver</a>
                @can('editar empresas')
                <a href="{{ route('empresas.edit', $empresa) }}" class="px-4 py-2 rounded-md bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700">Editar</a>
                @endcan
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Información general</h2>
                    <dl class="space-y-3 text-sm">
                        <div class="flex justify-between"><dt class="text-gray-500">RTN</dt><dd class="font-medium text-gray-900">{{ $empresa->identificacion_fiscal }}</dd></div>
                        <div class="flex justify-between"><dt class="text-gray-500">Teléfono</dt><dd class="font-medium text-gray-900">{{ $empresa->telefono }}</dd></div>
                        <div class="flex justify-between"><dt class="text-gray-500">Correo</dt><dd class="font-medium text-gray-900">{{ $empresa->correo }}</dd></div>
                        <div class="flex justify-between"><dt class="text-gray-500">Dirección</dt><dd class="font-medium text-gray-900 text-right">{{ $empresa->direccion }}</dd></div>
                        <div class="flex justify-between"><dt class="text-gray-500">Creado</dt><dd class="font-medium text-gray-900">{{ $empresa->created_at->format('d/m/Y') }}</dd></div>
                    </dl>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                    <h2 class="text-lg font-semibold text-gray-900 mb-3">Estadísticas</h2>
                    <dl class="grid grid-cols-2 gap-4 text-sm">
                        <div class="rounded-lg bg-indigo-50 p-3">
                            <dt class="text-indigo-600 font-medium">Sucursales</dt>
                            <dd class="text-2xl font-bold text-gray-900">{{ $empresa->sucursales->count() }}</dd>
                        </div>
                        <div class="rounded-lg bg-green-50 p-3">
                            <dt class="text-green-600 font-medium">Áreas</dt>
                            <dd class="text-2xl font-bold text-gray-900">{{ $empresa->areas->count() }}</dd>
                        </div>
                        <div class="rounded-lg bg-amber-50 p-3">
                            <dt class="text-amber-600 font-medium">Ítems</dt>
                            <dd class="text-2xl font-bold text-gray-900">{{ $empresa->items->count() }}</dd>
                        </div>
                        <div class="rounded-lg bg-gray-50 p-3">
                            <dt class="text-gray-600 font-medium">Usuarios</dt>
                            <dd class="text-2xl font-bold text-gray-900">{{ $empresa->usuarios ? $empresa->usuarios->count() : 0 }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Sucursales</h2>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Nombre</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Dirección</th>
                                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Áreas</th>
                                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Estado</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse ($empresa->sucursales as $sucursal)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-5 py-3 font-medium text-gray-900">{{ $sucursal->nombre }}</td>
                                        <td class="px-5 py-3 text-gray-600">{{ $sucursal->direccion }}</td>
                                        <td class="px-5 py-3 text-center text-gray-600">{{ $sucursal->areas->count() }}</td>
                                        <td class="px-5 py-3 text-center">
                                            <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold {{ $sucursal->estado === 'activo' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">{{ $sucursal->estado }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="px-5 py-10 text-center text-gray-500">Esta empresa no tiene sucursales.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>