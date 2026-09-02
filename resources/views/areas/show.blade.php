<x-app-layout>
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $area->nombre }}</h1>
                <p class="text-sm text-gray-500 mt-1">{{ $area->sucursal?->nombre }} · {{ $area->sucursal?->empresa?->nombre }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('areas.index') }}" class="px-4 py-2 rounded-md border border-gray-300 text-sm text-gray-700 hover:bg-gray-50">← Volver</a>
                @can('editar areas')
                <a href="{{ route('areas.edit', $area) }}" class="px-4 py-2 rounded-md bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700">Editar</a>
                @endcan
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Información</h2>
                    <dl class="space-y-3 text-sm">
                        <div class="flex justify-between"><dt class="text-gray-500">Encargado</dt><dd class="font-medium text-gray-900">{{ $area->encargado?->name ?? 'Sin encargado' }}</dd></div>
                        <div class="flex justify-between"><dt class="text-gray-500">Estado</dt>
                            <dd><span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $area->estado === 'activo' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">{{ $area->estado }}</span></dd>
                        </div>
                        <div class="flex justify-between"><dt class="text-gray-500">Ítems</dt><dd class="font-medium text-gray-900">{{ $area->items->count() }}</dd></div>
                    </dl>
                    @if ($area->descripcion)
                        <p class="mt-4 text-sm text-gray-600 border-t border-gray-100 pt-4">{{ $area->descripcion }}</p>
                    @endif
                </div>
            </div>

            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">Inventario del área</h2>
                            <p class="text-sm text-gray-500">Ítems almacenados con su cantidad actual.</p>
                        </div>
                        @if (auth()->user()->can('registrar entradas'))
                        <a href="{{ route('movimientos.entrada', ['area_id' => $area->id]) }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">Registrar entrada</a>
                        @endif
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Ítem</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Categoría</th>
                                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Cantidad</th>
                                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Estado</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse ($area->items as $item)
                                    @php
                                        $stock = (float) $item->pivot->cantidad;
                                        $minimo = (float) $item->stock_minimo;
                                        $badge = $stock <= 0 ? 'bg-red-100 text-red-700' : ($stock < $minimo ? 'bg-amber-100 text-amber-700' : 'bg-green-100 text-green-700');
                                        $txt = $stock <= 0 ? 'Sin stock' : ($stock < $minimo ? 'Bajo' : 'OK');
                                    @endphp
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-5 py-3">
                                            <div class="font-medium text-gray-900">{{ $item->nombre }}</div>
                                            <div class="text-xs text-gray-400">{{ $item->sku }}</div>
                                        </td>
                                        <td class="px-5 py-3 text-gray-600">{{ $item->categoria?->nombre }}</td>
                                        <td class="px-5 py-3 text-right font-semibold text-gray-900">{{ number_format($stock, 0) }} <span class="text-xs font-normal text-gray-400">{{ $item->unidadMedida?->abreviatura }}</span></td>
                                        <td class="px-5 py-3 text-center">
                                            <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold {{ $badge }}">{{ $txt }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="px-5 py-10 text-center text-gray-500">Esta área no tiene inventario.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>