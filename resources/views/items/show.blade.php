<x-app-layout>
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center gap-4">
                @if ($item->imagen)
                    <img src="{{ asset('storage/' . $item->imagen) }}" class="h-14 w-14 rounded-lg object-cover" alt="">
                @endif
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $item->nombre }}</h1>
                    <p class="text-sm text-gray-500 mt-1">{{ $item->sku }} · {{ $item->categoria?->nombre }} · {{ $item->empresa?->nombre }}</p>
                </div>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('items.index') }}" class="px-4 py-2 rounded-md border border-gray-300 text-sm text-gray-700 hover:bg-gray-50">← Volver</a>
                @can('editar items')
                <a href="{{ route('items.edit', $item) }}" class="px-4 py-2 rounded-md bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700">Editar</a>
                @endcan
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Información</h2>
                    <dl class="space-y-3 text-sm">
                        <div class="flex justify-between"><dt class="text-gray-500">Estado</dt>
                            <dd><span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $item->estado === 'activo' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">{{ $item->estado }}</span></dd>
                        </div>
                        <div class="flex justify-between"><dt class="text-gray-500">Unidad</dt><dd class="font-medium text-gray-900">{{ $item->unidadMedida?->nombre }}</dd></div>
                        <div class="flex justify-between"><dt class="text-gray-500">Proveedor</dt><dd class="font-medium text-gray-900 text-right">{{ $item->proveedor?->nombre }}</dd></div>
                        <div class="flex justify-between"><dt class="text-gray-500">Costo unitario</dt><dd class="font-medium text-gray-900">L. {{ number_format((float) $item->costo_unitario, 2) }}</dd></div>
                        <div class="flex justify-between"><dt class="text-gray-500">Stock mínimo</dt><dd class="font-medium text-gray-900">{{ number_format((float) $item->stock_minimo, 0) }}</dd></div>
                    </dl>
                    @if ($item->descripcion)
                        <p class="mt-4 text-sm text-gray-600 border-t border-gray-100 pt-4">{{ $item->descripcion }}</p>
                    @endif
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                    <h2 class="text-lg font-semibold text-gray-900 mb-3">Stock total</h2>
                    @php
                        $stockTotal = (float) $item->inventarioArea->sum('cantidad');
                        $minimo = (float) $item->stock_minimo;
                        $badge = $stockTotal <= 0 ? 'bg-red-100 text-red-700' : ($stockTotal < $minimo ? 'bg-amber-100 text-amber-700' : 'bg-green-100 text-green-700');
                    @endphp
                    <div class="flex items-center gap-3">
                        <p class="text-4xl font-bold text-gray-900">{{ number_format($stockTotal, 0) }}</p>
                        <span class="text-gray-400">{{ $item->unidadMedida?->abreviatura }}</span>
                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold {{ $badge }}">{{ $stockTotal <= 0 ? 'Sin stock' : ($stockTotal < $minimo ? 'Bajo' : 'OK') }}</span>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Distribución en áreas</h2>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Área</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Sucursal</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Encargado</th>
                                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Cantidad</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse ($item->inventarioArea as $inv)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-5 py-3 font-medium text-gray-900">{{ $inv->area?->nombre }}</td>
                                        <td class="px-5 py-3 text-gray-600">{{ $inv->area?->sucursal?->nombre }}</td>
                                        <td class="px-5 py-3 text-gray-600">{{ $inv->area?->encargado?->name }}</td>
                                        <td class="px-5 py-3 text-right font-semibold text-gray-900">{{ number_format((float) $inv->cantidad, 0) }} <span class="text-xs font-normal text-gray-400">{{ $item->unidadMedida?->abreviatura }}</span></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="px-5 py-10 text-center text-gray-500">Este ítem no tiene stock asignado a ninguna área.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>