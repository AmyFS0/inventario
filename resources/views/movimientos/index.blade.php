<x-app-layout>
    <div class="space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Movimientos de inventario</h1>
                <p class="text-sm text-gray-500 mt-1">Bitácora de entradas, salidas, traslados y ajustes.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                @can('registrar entradas')
                <a href="{{ route('movimientos.entrada') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-md bg-green-600 text-white text-sm font-medium hover:bg-green-700">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Entrada
                </a>
                @endcan
                @can('registrar salidas')
                <a href="{{ route('movimientos.salida') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-md bg-red-600 text-white text-sm font-medium hover:bg-red-700">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Salida
                </a>
                @endcan
                @can('registrar traslados')
                <a href="{{ route('movimientos.traslado') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-md bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                    Traslado
                </a>
                @endcan
                @can('registrar ajustes')
                <a href="{{ route('movimientos.ajuste') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-md bg-amber-600 text-white text-sm font-medium hover:bg-amber-700">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    Ajuste
                </a>
                @endcan
            </div>
        </div>

        <form method="GET" action="{{ route('movimientos.index') }}" class="flex flex-wrap gap-2">
            <select name="tipo" class="rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">Todos los tipos</option>
                @foreach (['entrada', 'salida', 'traslado', 'ajuste'] as $tipo)
                    <option value="{{ $tipo }}" @selected(request('tipo') === $tipo)>{{ ucfirst($tipo) }}</option>
                @endforeach
            </select>
            <select name="item_id" class="rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">Todos los ítems</option>
                @foreach ($items as $item)
                    <option value="{{ $item->id }}" @selected(request('item_id') == $item->id)>{{ $item->nombre }}</option>
                @endforeach
            </select>
            <input type="date" name="desde" value="{{ request('desde') }}" class="rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
            <input type="date" name="hasta" value="{{ request('hasta') }}" class="rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
            <button type="submit" class="px-4 py-2 rounded-md bg-gray-800 text-white text-sm hover:bg-gray-900">Filtrar</button>
            <a href="{{ route('movimientos.index') }}" class="px-4 py-2 rounded-md border border-gray-300 text-sm text-gray-700 hover:bg-gray-50">Limpiar</a>
        </form>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Tipo</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Ítem</th>
                            <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Cantidad</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Área</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Usuario</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Motivo</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Fecha</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($movimientos as $mov)
                            @php
                                $badge = match($mov->tipo) {
                                    'entrada' => 'bg-green-100 text-green-700',
                                    'salida' => 'bg-red-100 text-red-700',
                                    'traslado' => 'bg-indigo-100 text-indigo-700',
                                    'ajuste' => 'bg-amber-100 text-amber-700',
                                    default => 'bg-gray-100 text-gray-700',
                                };
                                $cantBadge = $mov->tipo === 'salida' || ($mov->tipo === 'ajuste' && $mov->cantidad < 0)
                                    ? 'text-red-600' : 'text-green-600';
                                $signo = ($mov->tipo === 'salida' || ($mov->tipo === 'ajuste' && $mov->cantidad < 0)) ? '- ' : '+ ';
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-5 py-3 text-center">
                                    <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold capitalize {{ $badge }}">{{ $mov->tipo }}</span>
                                </td>
                                <td class="px-5 py-3">
                                    <div class="font-medium text-gray-900">{{ $mov->item?->nombre }}</div>
                                    <div class="text-xs text-gray-400">{{ $mov->item?->sku }}</div>
                                </td>
                                <td class="px-5 py-3 text-right font-semibold {{ $cantBadge }}">{{ $signo }}{{ number_format((float) $mov->cantidad, 0) }}</td>
                                <td class="px-5 py-3 text-gray-600">
                                    @if ($mov->tipo === 'traslado')
                                        {{ $mov->areaOrigen?->nombre }} <span class="text-gray-400">→</span> {{ $mov->areaDestino?->nombre }}
                                        <div class="text-xs text-gray-400">{{ $mov->areaOrigen?->sucursal?->nombre }} → {{ $mov->areaDestino?->sucursal?->nombre }}</div>
                                    @else
                                        {{ $mov->areaOrigen?->nombre ?? $mov->areaDestino?->nombre }}
                                        <div class="text-xs text-gray-400">{{ $mov->areaOrigen?->sucursal?->nombre ?? $mov->areaDestino?->sucursal?->nombre }}</div>
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-gray-600">{{ $mov->usuario?->name }}</td>
                                <td class="px-5 py-3 text-gray-500 max-w-xs truncate" title="{{ $mov->motivo }}">{{ $mov->motivo }}</td>
                                <td class="px-5 py-3 text-gray-500 whitespace-nowrap">{{ $mov->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-10 text-center text-gray-500">No hay movimientos registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($movimientos->hasPages())
                <div class="px-5 py-3 border-t border-gray-200">
                    {{ $movimientos->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>