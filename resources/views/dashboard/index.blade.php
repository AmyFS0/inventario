<x-app-layout>
    @push('styles')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
    @endpush

    @php
        $user = auth()->user();
    @endphp

    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Panel de control</h1>
                <p class="text-sm text-gray-500 mt-1">Resumen general del inventario de {{ $user->empresa?->nombre ?? 'todas las empresas' }}.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                @if ($user->can('registrar entradas'))
                <a href="{{ route('movimientos.entrada') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-md bg-green-600 text-white text-sm font-medium hover:bg-green-700">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Registrar entrada
                </a>
                @endif
                @if ($user->can('registrar traslados'))
                <a href="{{ route('movimientos.traslado') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-md bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                    Trasladar inventario
                </a>
                @endif
                @if ($user->can('ver reportes'))
                <a href="{{ route('reportes.inventario') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-md bg-gray-800 text-white text-sm font-medium hover:bg-gray-900">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    Ver reportes
                </a>
                @endif
            </div>
        </div>

        <!-- Tarjetas resumen -->
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Ítems en catálogo</p>
                        <p class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($totalItems) }}</p>
                    </div>
                    <div class="h-11 w-11 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Total unidades</p>
                        <p class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($totalUnidades) }}</p>
                    </div>
                    <div class="h-11 w-11 rounded-lg bg-green-100 text-green-600 flex items-center justify-center">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Sucursales</p>
                        <p class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($totalUnidadesPorSucursal->count()) }}</p>
                    </div>
                    <div class="h-11 w-11 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21h18M5 21V7l7-4 7 4v14M9 21v-6a2 2 0 012-2h2a2 2 0 012 2v6"/></svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Bajo stock mínimo</p>
                        <p class="text-3xl font-bold {{ $itemsBajoMinimo->count() > 0 ? 'text-red-600' : 'text-green-600' }} mt-1">{{ number_format($itemsBajoMinimo->count()) }}</p>
                    </div>
                    <div class="h-11 w-11 rounded-lg {{ $itemsBajoMinimo->count() > 0 ? 'bg-red-100 text-red-600' : 'bg-green-100 text-green-600' }} flex items-center justify-center">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            <!-- Gráfico distribución por categoría -->
            <div class="xl:col-span-1 bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Distribución por categoría</h2>
                <canvas id="chartCategorias" height="220"></canvas>
            </div>

            <!-- Alertas de stock mínimo -->
            <div class="xl:col-span-2 bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-5 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Alertas de stock mínimo</h2>
                    <p class="text-sm text-gray-500">Ítems con stock igual o por debajo del mínimo configurado.</p>
                </div>
                @if ($itemsBajoMinimo->isEmpty())
                    <div class="p-8 text-center text-gray-500 text-sm">No hay ítems por debajo del stock mínimo.</div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Ítem</th>
                                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Stock</th>
                                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Mínimo</th>
                                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Estado</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($itemsBajoMinimo as $item)
                                    @php
                                        $stock = (float) $item->stock_total;
                                        $minimo = (float) $item->stock_minimo;
                                        $rojo = $stock < $minimo || $stock <= 0;
                                        $amarillo = !$rojo;
                                    @endphp
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-5 py-3">
                                            <div class="font-medium text-gray-900">{{ $item->nombre }}</div>
                                            <div class="text-xs text-gray-400">{{ $item->sku }}</div>
                                        </td>
                                        <td class="px-5 py-3 text-right font-semibold {{ $rojo ? 'text-red-600' : 'text-amber-600' }}">{{ number_format($stock, 0) }}</td>
                                        <td class="px-5 py-3 text-right text-gray-600">{{ number_format($minimo, 0) }}</td>
                                        <td class="px-5 py-3 text-center">
                                            @if ($rojo)
                                                <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">Crítico</span>
                                            @else
                                                <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">Bajo</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
            <!-- Últimos movimientos -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">Últimos movimientos</h2>
                        <p class="text-sm text-gray-500">Los últimos 10 registros de la bitácora.</p>
                    </div>
                    @if ($user->can('ver movimientos'))
                    <a href="{{ route('movimientos.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">Ver todos</a>
                    @endif
                </div>
                @if ($ultimosMovimientos->isEmpty())
                    <div class="p-8 text-center text-gray-500 text-sm">Aún no hay movimientos registrados.</div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Tipo</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Ítem</th>
                                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Cant.</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Usuario</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Fecha</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($ultimosMovimientos as $mov)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-5 py-3">
                                            @php
                                                $badge = match($mov->tipo) {
                                                    'entrada' => 'bg-green-100 text-green-700',
                                                    'salida' => 'bg-red-100 text-red-700',
                                                    'traslado' => 'bg-indigo-100 text-indigo-700',
                                                    'ajuste' => 'bg-amber-100 text-amber-700',
                                                    default => 'bg-gray-100 text-gray-700',
                                                };
                                            @endphp
                                            <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold capitalize {{ $badge }}">{{ $mov->tipo }}</span>
                                        </td>
                                        <td class="px-5 py-3 text-gray-900">{{ $mov->item?->nombre ?? 'N/A' }}</td>
                                        <td class="px-5 py-3 text-right font-semibold text-gray-900">{{ number_format((float) $mov->cantidad, 0) }}</td>
                                        <td class="px-5 py-3 text-gray-600">{{ $mov->usuario?->name }}</td>
                                        <td class="px-5 py-3 text-gray-500">{{ $mov->created_at->format('d/m/Y H:i') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <!-- Distribución por sucursal -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Inventario por sucursal</h2>
                @if ($totalUnidadesPorSucursal->isEmpty())
                    <div class="p-8 text-center text-gray-500 text-sm">Sin datos disponibles.</div>
                @else
                    <div class="space-y-4">
                        @foreach ($totalUnidadesPorSucursal as $s)
                            @php
                                $max = $totalUnidadesPorSucursal->max('total');
                                $pct = $max > 0 ? round(((float) $s->total / (float) $max) * 100) : 0;
                            @endphp
                            <div>
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="font-medium text-gray-700">{{ $s->sucursal }}</span>
                                    <span class="font-semibold text-gray-900">{{ number_format((float) $s->total, 0) }} uds</span>
                                </div>
                                <div class="h-2.5 rounded-full bg-gray-100">
                                    <div class="h-2.5 rounded-full bg-indigo-500" style="width: {{ $pct }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const ctx = document.getElementById('chartCategorias');
        if (ctx) {
            const labels = @json($distribucionCategorias->pluck('categoria')->values());
            const data = @json($distribucionCategorias->pluck('total')->map(fn ($v) => (float) $v)->values());
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: data,
                        backgroundColor: ['#6366f1', '#22c55e', '#f59e0b', '#ef4444', '#3b82f6', '#a855f7', '#14b8a6', '#f97316'],
                        borderWidth: 2,
                        borderColor: '#ffffff',
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'bottom', labels: { padding: 14, usePointStyle: true } }
                    }
                }
            });
        }
    </script>
    @endpush
</x-app-layout>