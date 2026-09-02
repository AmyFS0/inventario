<x-app-layout>
    <div class="space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Reporte de movimientos</h1>
                <p class="text-sm text-gray-500 mt-1">Bitácora completa de movimientos con filtros.</p>
            </div>
            @php
                $queryExport = http_build_query($filtros);
            @endphp
            <div class="flex gap-2">
                @can('exportar reportes')
                <a href="{{ route('reportes.movimientos.excel') }}?{{ $queryExport }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-md bg-green-600 text-white text-sm font-medium hover:bg-green-700">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Excel
                </a>
                <a href="{{ route('reportes.movimientos.pdf') }}?{{ $queryExport }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-md bg-red-600 text-white text-sm font-medium hover:bg-red-700">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    PDF
                </a>
                @endcan
            </div>
        </div>

        <form method="GET" action="{{ route('reportes.movimientos') }}" class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                <select name="tipo" class="rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Todos los tipos</option>
                    @foreach (['entrada', 'salida', 'traslado', 'ajuste'] as $tipo)
                        <option value="{{ $tipo }}" @selected(($filtros['tipo'] ?? '') === $tipo)>{{ ucfirst($tipo) }}</option>
                    @endforeach
                </select>
                <select name="item_id" class="rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Todos los ítems</option>
                    @foreach ($items as $item)
                        <option value="{{ $item->id }}" @selected(($filtros['item_id'] ?? '') == $item->id)>{{ $item->nombre }}</option>
                    @endforeach
                </select>
                <select name="usuario_id" class="rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Todos los usuarios</option>
                    @foreach ($usuarios as $usuario)
                        <option value="{{ $usuario->id }}" @selected(($filtros['usuario_id'] ?? '') == $usuario->id)>{{ $usuario->name }}</option>
                    @endforeach
                </select>
                <input type="date" name="desde" value="{{ $filtros['desde'] ?? '' }}" class="rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                <input type="date" name="hasta" value="{{ $filtros['hasta'] ?? '' }}" class="rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div class="flex gap-2 mt-3">
                <button type="submit" class="px-4 py-2 rounded-md bg-gray-800 text-white text-sm hover:bg-gray-900">Filtrar</button>
                <a href="{{ route('reportes.movimientos') }}" class="px-4 py-2 rounded-md border border-gray-300 text-sm text-gray-700 hover:bg-gray-50">Limpiar</a>
            </div>
        </form>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Fecha</th>
                            <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Tipo</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Ítem</th>
                            <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Cant.</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Área origen</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Área destino</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Usuario</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Motivo</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($filas as $mov)
                            @php
                                $badge = match($mov->tipo) {
                                    'entrada' => 'bg-green-100 text-green-700',
                                    'salida' => 'bg-red-100 text-red-700',
                                    'traslado' => 'bg-indigo-100 text-indigo-700',
                                    'ajuste' => 'bg-amber-100 text-amber-700',
                                    default => 'bg-gray-100 text-gray-700',
                                };
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-5 py-3 text-gray-500 whitespace-nowrap">{{ $mov->created_at->format('d/m/Y H:i') }}</td>
                                <td class="px-5 py-3 text-center">
                                    <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold capitalize {{ $badge }}">{{ $mov->tipo }}</span>
                                </td>
                                <td class="px-5 py-3">
                                    <div class="font-medium text-gray-900">{{ $mov->item?->nombre }}</div>
                                    <div class="text-xs text-gray-400">{{ $mov->item?->sku }}</div>
                                </td>
                                <td class="px-5 py-3 text-right font-semibold text-gray-900">{{ number_format((float) $mov->cantidad, 0) }}</td>
                                <td class="px-5 py-3 text-gray-600">{{ $mov->areaOrigen?->nombre ?? '-' }}</td>
                                <td class="px-5 py-3 text-gray-600">{{ $mov->areaDestino?->nombre ?? '-' }}</td>
                                <td class="px-5 py-3 text-gray-600">{{ $mov->usuario?->name }}</td>
                                <td class="px-5 py-3 text-gray-500 max-w-xs truncate" title="{{ $mov->motivo }}">{{ $mov->motivo }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-5 py-10 text-center text-gray-500">Sin resultados para los filtros seleccionados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-5 py-3 border-t border-gray-200 text-sm text-gray-600">
                Total de registros: <strong>{{ count($filas) }}</strong>
            </div>
        </div>
    </div>
</x-app-layout>