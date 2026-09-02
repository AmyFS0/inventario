<x-app-layout>
    <div class="max-w-3xl mx-auto space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Registrar entrada</h1>
                <p class="text-sm text-gray-500 mt-1">Aumenta el stock de un ítem en un área.</p>
            </div>
            <a href="{{ route('movimientos.index') }}" class="text-sm text-gray-600 hover:text-gray-900">← Volver</a>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <form method="POST" action="{{ route('movimientos.entrada.store') }}" class="space-y-5">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Ítem *</label>
                        <select name="item_id" id="item_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">-- Seleccione --</option>
                            @foreach ($items as $item)
                                <option value="{{ $item->id }}" @selected(old('item_id') == $item->id)>{{ $item->nombre }} ({{ $item->sku }})</option>
                            @endforeach
                        </select>
                        @error('item_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Área de destino *</label>
                        <select name="area_id" id="area_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">-- Seleccione --</option>
                            @foreach ($areas as $area)
                                <option value="{{ $area->id }}" @selected(old('area_id') == $area->id)>{{ $area->nombre }} — {{ $area->sucursal?->nombre }}</option>
                            @endforeach
                        </select>
                        @error('area_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Cantidad *</label>
                        <input type="number" step="0.01" min="0.01" name="cantidad" id="cantidad" value="{{ old('cantidad') }}" required placeholder="0.00"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('cantidad') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Motivo</label>
                        <input type="text" name="motivo" value="{{ old('motivo') }}" placeholder="Ej. Compra al proveedor"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('motivo') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Observación</label>
                        <textarea name="observacion" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('observacion') }}</textarea>
                        @error('observacion') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div id="infoStock" class="hidden rounded-md bg-gray-50 border border-gray-200 px-4 py-3 text-sm text-gray-600"></div>

                <div class="flex justify-end gap-3 pt-2">
                    <a href="{{ route('movimientos.index') }}" class="px-4 py-2 rounded-md border border-gray-300 text-sm text-gray-700 hover:bg-gray-50">Cancelar</a>
                    <button type="submit" class="px-5 py-2 rounded-md bg-green-600 text-white text-sm font-medium hover:bg-green-700">Registrar entrada</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        const itemSel = document.getElementById('item_id');
        const areaSel = document.getElementById('area_id');
        const info = document.getElementById('infoStock');
        const caja = document.getElementById('cantidad');

        async function consultarStock() {
            if (!itemSel.value || !areaSel.value) { info.classList.add('hidden'); return; }
            try {
                const params = new URLSearchParams({ item_id: itemSel.value, area_id: areaSel.value });
                const resp = await fetch("{{ route('movimientos.stock') }}?" + params.toString(), {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await resp.json();
                const actual = Number(data.disponible).toLocaleString('es-HN');
                info.innerHTML = 'Stock actual en el área: <strong>' + actual + ' ' + (data.unidad || 'und') + '</strong>.';
                info.classList.remove('hidden');
            } catch (e) {
                info.innerHTML = 'No se pudo consultar el stock disponible.';
                info.classList.remove('hidden');
            }
        }

        [itemSel, areaSel, caja].forEach(el => el && el.addEventListener('change', consultarStock));
        consultarStock();
    </script>
    @endpush
</x-app-layout>