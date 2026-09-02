<x-app-layout>
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $sucursal->nombre }}</h1>
                <p class="text-sm text-gray-500 mt-1">{{ $sucursal->empresa?->nombre }} · {{ $sucursal->direccion }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('sucursales.index') }}" class="px-4 py-2 rounded-md border border-gray-300 text-sm text-gray-700 hover:bg-gray-50">← Volver</a>
                @can('editar sucursales')
                <a href="{{ route('sucursales.edit', $sucursal) }}" class="px-4 py-2 rounded-md bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700">Editar</a>
                @endcan
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Información</h2>
                    <dl class="space-y-3 text-sm">
                        <div class="flex justify-between"><dt class="text-gray-500">Teléfono</dt><dd class="font-medium text-gray-900">{{ $sucursal->telefono }}</dd></div>
                        <div class="flex justify-between"><dt class="text-gray-500">Estado</dt>
                            <dd><span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $sucursal->estado === 'activo' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">{{ $sucursal->estado }}</span></dd>
                        </div>
                        <div class="flex justify-between"><dt class="text-gray-500">Áreas</dt><dd class="font-medium text-gray-900">{{ $sucursal->areas->count() }}</dd></div>
                    </dl>
                </div>
            </div>

            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Áreas de la sucursal</h2>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Área</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Encargado</th>
                                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Ítems</th>
                                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Estado</th>
                                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Acción</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse ($sucursal->areas as $area)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-5 py-3 font-medium text-gray-900">{{ $area->nombre }}</td>
                                        <td class="px-5 py-3 text-gray-600">{{ $area->encargado?->name }}</td>
                                        <td class="px-5 py-3 text-center text-gray-600">{{ $area->items->count() }}</td>
                                        <td class="px-5 py-3 text-center">
                                            <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold {{ $area->estado === 'activo' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">{{ $area->estado }}</span>
                                        </td>
                                        <td class="px-5 py-3 text-center">
                                            <a href="{{ route('areas.show', $area) }}" class="text-indigo-600 hover:text-indigo-800 font-medium text-xs">Ver</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="px-5 py-10 text-center text-gray-500">Sin áreas asignadas.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>