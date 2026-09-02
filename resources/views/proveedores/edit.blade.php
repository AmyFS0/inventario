<x-app-layout>
    <div class="max-w-2xl mx-auto space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Editar proveedor</h1>
                <p class="text-sm text-gray-500 mt-1">{{ $proveedor->nombre }}</p>
            </div>
            <a href="{{ route('proveedores.index') }}" class="text-sm text-gray-600 hover:text-gray-900">← Volver</a>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <form method="POST" action="{{ route('proveedores.update', $proveedor) }}" class="space-y-5">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Nombre *</label>
                        <input type="text" name="nombre" value="{{ old('nombre', $proveedor->nombre) }}" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('nombre') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Persona de contacto</label>
                        <input type="text" name="contacto" value="{{ old('contacto', $proveedor->contacto) }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('contacto') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Teléfono</label>
                        <input type="text" name="telefono" value="{{ old('telefono', $proveedor->telefono) }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('telefono') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Correo electrónico</label>
                        <input type="email" name="correo" value="{{ old('correo', $proveedor->correo) }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('correo') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <a href="{{ route('proveedores.index') }}" class="px-4 py-2 rounded-md border border-gray-300 text-sm text-gray-700 hover:bg-gray-50">Cancelar</a>
                    <button type="submit" class="px-5 py-2 rounded-md bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700">Actualizar proveedor</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>