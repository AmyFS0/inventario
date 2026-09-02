<x-app-layout>
    <div class="max-w-3xl mx-auto space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Nuevo usuario</h1>
                <p class="text-sm text-gray-500 mt-1">Cree una cuenta de usuario con sus roles.</p>
            </div>
            <a href="{{ route('usuarios.index') }}" class="text-sm text-gray-600 hover:text-gray-900">← Volver</a>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <form method="POST" action="{{ route('usuarios.store') }}" class="space-y-5">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nombre *</label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Correo electrónico *</label>
                        <input type="email" name="email" value="{{ old('email') }}" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Contraseña *</label>
                        <input type="password" name="password" required autocomplete="new-password"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Confirmar contraseña *</label>
                        <input type="password" name="password_confirmation" required autocomplete="new-password"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Empresa</label>
                        <select name="empresa_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">-- Sin empresa --</option>
                            @foreach ($empresas as $empresa)
                                <option value="{{ $empresa->id }}" @selected(old('empresa_id', auth()->user()->empresa_id) == $empresa->id)>{{ $empresa->nombre }}</option>
                            @endforeach
                        </select>
                        @error('empresa_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Estado</label>
                        <select name="estado" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="activo" @selected(old('estado', 'activo') === 'activo')>Activo</option>
                            <option value="inactivo" @selected(old('estado') === 'inactivo')>Inactivo</option>
                        </select>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Roles *</label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            @foreach ($roles as $rol)
                                <label class="flex items-center gap-3 rounded-md border border-gray-200 px-4 py-3 cursor-pointer hover:bg-gray-50">
                                    <input type="checkbox" name="roles[]" value="{{ $rol->id }}"
                                        @checked(in_array($rol->id, (array) old('roles', [])))
                                        class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                    <div>
                                        <span class="text-sm font-medium text-gray-900">{{ $rol->name }}</span>
                                        <span class="block text-xs text-gray-400">Asigna los permisos de este rol.</span>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        @error('roles') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        @error('roles.*') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <a href="{{ route('usuarios.index') }}" class="px-4 py-2 rounded-md border border-gray-300 text-sm text-gray-700 hover:bg-gray-50">Cancelar</a>
                    <button type="submit" class="px-5 py-2 rounded-md bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700">Guardar usuario</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>