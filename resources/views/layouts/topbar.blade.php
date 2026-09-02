<header class="bg-white border-b border-gray-200 sticky top-0 z-30">
    <div class="px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <button class="lg:hidden text-gray-500 hover:text-gray-800" onclick="document.querySelector('aside').classList.add('translate-x-0'); document.querySelector('aside').classList.remove('-translate-x-full');">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
            <a href="{{ route('dashboard') }}" class="text-xs sm:text-sm text-gray-500">
                {{ request()->routeIs('dashboard') ? '' : '' }}
                <span class="font-semibold text-gray-900 hidden sm:inline">{{ config('app.name', 'Inventario Mipymes') }}</span>
            </a>
        </div>

        <div class="flex items-center gap-4">
            @php
                $roles = auth()->user()->getRoleNames();
            @endphp
            @if (auth()->user()->empresa)
                <span class="hidden md:inline-flex items-center gap-2 text-xs px-3 py-1 rounded-full bg-indigo-50 text-indigo-700 font-medium">
                    {{ auth()->user()->empresa->nombre }}
                </span>
            @endif
            @if ($roles->count())
                <span class="hidden md:inline-flex items-center gap-2 text-xs px-3 py-1 rounded-full bg-gray-100 text-gray-600 font-medium capitalize">
                    {{ $roles->first() }}
                </span>
            @endif

            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" class="flex items-center gap-2 text-sm text-gray-700 hover:text-gray-900">
                    <span class="h-8 w-8 rounded-full bg-indigo-500 text-white flex items-center justify-center font-semibold text-sm">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </span>
                    <span class="hidden sm:inline">
                        <div class="leading-tight font-medium">{{ auth()->user()->name }}</div>
                        <div class="leading-tight text-xs text-gray-400">{{ auth()->user()->email }}</div>
                    </span>
                    <svg class="fill-current h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>

                <div x-show="open" @click.away="open = false"
                    class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg border border-gray-200 py-1 z-50">
                    <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Mi perfil</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">Cerrar sesión</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>