<x-app-layout>
    @php
        $empresas = auth()->user()->esSuperAdmin()
            ? \App\Models\Empresa::orderBy('nombre')->get()
            : collect();
    @endphp

    <div class="space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Reporte de inventario</h1>
                <p class="text-sm text-gray-500 mt-1">Existencia actual de ítems por área, con filtros.</p>
            </div>
            @php
                $queryExport = http_build_query($filtros);
            @endphp
            <div class="flex gap-2">
                @can('exportar reportes')
                <a href="{{ route('reportes.inventario.excel') }}?{{ $queryExport }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-md bg-green-600 text-white text-sm font-medium hover:bg-green-700">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Excel
                </a>
                <a href="{{ route('reportes.inventario.pdf') }}?{{ $queryExport }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-md bg-red-600 text-white text-sm font-medium hover:bg-red-700">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    PDF
                </a>
                @endcan
            </div>
        </div>

        <form method="GET" action="{{ route('reportes.inventario') }}" class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                @if ($empresas->isNotEmpty())
                <select name="empresa_id" class="rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Todas las empresas</option>
                    @foreach ($empresas as $empresa)
                        <option value="{{ $empresa->id }}" @selected(($filtros['empresa_id'] ?? '') == $empresa->id)>{{ $empresa->nombre }}</option>
                    @endforeach
                </select>
                @endif
                <select name="sucursal_id" class="rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Todas las sucursales</option>
                    @foreach ($sucursales as $sucursal)
                        <option value="{{ $sucursal->id }}" @selected(($filtros['sucursal_id'] ?? '') == $sucursal->id)>{{ $sucursal->nombre }}</option>
                    @endforeach
                </select>
                <select name="area_id" class="rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Todas las áreas</option>
                    @foreach ($areas as $area)
                        <option value="{{ $area->id }}" @selected(($filtros['area_id'] ?? '') == $area->id)>{{ $area->nombre }}</option>
                    @endforeach
                </select>
                <select name="categoria_id" class="rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Todas las categorías</option>
                    @foreach ($itemsCategorias as $categoria)
                        <option value="{{ $categoria->id }}" @selected(($filtros['categoria_id'] ?? '') == $categoria->id)>{{ $categoria->nombre }}</option>
                    @endforeach
                </select>
                <input type="text" name="busqueda" value="{{ $filtros['busqueda'] ?? '' }}" placeholder="Buscar ítem o SKU..."
                    class="rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div class="flex gap-2 mt-3">
                <button type="submit" class="px-4 py-2 rounded-md bg-gray-800 text-white text-sm hover:bg-gray-900">Filtrar</button>
                <a href="{{ route('reportes.inventario') }}" class="px-4 py-2 rounded-md border border-gray-300 text-sm text-gray-700 hover:bg-gray-50">Limpiar</a>
            </div>
        </form>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Empresa</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Sucursal</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Área</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Ítem</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Categoría</th>
                            <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Cantidad</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Responsable</th>
                            <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($filas as $fila)
                            <tr class="hover:bg-gray-50">
                                <td class="px-5 py-3 text-gray-600">{{ $fila->empresa }}</td>
                                <td class="px-5 py-3 text-gray-600">{{ $fila->sucursal }}</td>
                                <td class="px-5 py-3 text-gray-600">{{ $fila->area }}</td>
                                <td class="px-5 py-3">
                                    <div class="font-medium text-gray-900">{{ $fila->item }}</div>
                                    <div class="text-xs text-gray-400">{{ $fila->sku }}</div>
                                </td>
                                <td class="px-5 py-3 text-gray-600">{{ $fila->categoria }}</td>
                                <td class="px-5 py-3 text-right font-semibold text-gray-900">{{ number_format((float) $fila->cantidad, 0) }} <span class="text-xs font-normal text-gray-400">{{ $fila->unidad }}</span></td>
                                <td class="px-5 py-3 text-gray-600">{{ $fila->responsable }}</td>
                                <td class="px-5 py-3 text-center">
                                    <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold {{ $fila->estado === 'activo' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">{{ $fila->estado }}</span>
                                </td>
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