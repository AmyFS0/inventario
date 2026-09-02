<x-app-layout>
    <div class="max-w-xl mx-auto space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Nueva categoría</h1>
                <p class="text-sm text-gray-500 mt-1">Cree una categoría para clasificar ítems.</p>
            </div>
            <a href="{{ route('categorias.index') }}" class="text-sm text-gray-600 hover:text-gray-900">← Volver</a>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <form method="POST" action="{{ route('categorias.store') }}" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nombre *</label>
                    <input type="text" name="nombre" value="{{ old('nombre') }}" required placeholder="Ej. Electrónica"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('nombre') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <a href="{{ route('categorias.index') }}" class="px-4 py-2 rounded-md border border-gray-300 text-sm text-gray-700 hover:bg-gray-50">Cancelar</a>
                    <button type="submit" class="px-5 py-2 rounded-md bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700">Guardar categoría</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>