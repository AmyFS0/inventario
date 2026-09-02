<aside x-data="{ open: false }"
    class="bg-slate-900 text-slate-300 lg:w-64 lg:flex-shrink-0 lg:min-h-screen lg:static lg:translate-x-0 fixed inset-y-0 left-0 z-40 w-64 transform transition-transform duration-200 -translate-x-full"
    :class="{ '-translate-x-full': !open, 'translate-x-0': open }"
    @click.outside="open = false">

    <div class="flex items-center justify-between px-5 h-16 border-b border-slate-800">
        <a href="{{ route('dashboard') }}" class="flex items-center space-x-3">
            <div class="h-9 w-9 rounded-lg bg-indigo-500 flex items-center justify-center text-white font-bold text-lg">I</div>
            <div>
                <div class="font-bold text-white leading-tight">Inventario</div>
                <div class="text-xs text-slate-400 leading-tight">Mipymes</div>
            </div>
        </a>
        <button class="lg:hidden text-slate-400 hover:text-white" @click="open = false">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    <nav class="px-3 py-4 space-y-1 overflow-y-auto max-h-[calc(100vh-4rem)]">
    @php
        $user = auth()->user();
        $can = fn (string $p) => $user->can($p);
    @endphp

        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-md text-sm {{ request()->routeIs('dashboard') ? 'bg-slate-800 text-white' : 'hover:bg-slate-800 hover:text-white' }}">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
            Dashboard
        </a>

        @if ($can('ver empresas'))
        <a href="{{ route('empresas.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md text-sm {{ request()->routeIs('empresas*') ? 'bg-slate-800 text-white' : 'hover:bg-slate-800 hover:text-white' }}">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            Empresas
        </a>
        @endif

        @if ($can('ver sucursales'))
        <a href="{{ route('sucursales.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md text-sm {{ request()->routeIs('sucursales*') ? 'bg-slate-800 text-white' : 'hover:bg-slate-800 hover:text-white' }}">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21h18M5 21V7l7-4 7 4v14M9 21v-6a2 2 0 012-2h2a2 2 0 012 2v6"/></svg>
            Sucursales
        </a>
        @endif

        @if ($can('ver areas'))
        <a href="{{ route('areas.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md text-sm {{ request()->routeIs('areas*') ? 'bg-slate-800 text-white' : 'hover:bg-slate-800 hover:text-white' }}">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
            Áreas
        </a>
        @endif

        @if ($can('ver items'))
        <a href="{{ route('items.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md text-sm {{ request()->routeIs('items*') ? 'bg-slate-800 text-white' : 'hover:bg-slate-800 hover:text-white' }}">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            Ítems / Catálogo
        </a>
        @endif

        @if ($can('ver categorias'))
        <a href="{{ route('categorias.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md text-sm {{ request()->routeIs('categorias*') ? 'bg-slate-800 text-white' : 'hover:bg-slate-800 hover:text-white' }}">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
            Categorías
        </a>
        @endif

        @if ($can('ver unidades'))
        <a href="{{ route('unidades-medida.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md text-sm {{ request()->routeIs('unidades-medida*') ? 'bg-slate-800 text-white' : 'hover:bg-slate-800 hover:text-white' }}">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            Unidades de Medida
        </a>
        @endif

        @if ($can('ver proveedores'))
        <a href="{{ route('proveedores.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md text-sm {{ request()->routeIs('proveedores*') ? 'bg-slate-800 text-white' : 'hover:bg-slate-800 hover:text-white' }}">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
            Proveedores
        </a>
        @endif

        @if ($can('ver movimientos'))
        <a href="{{ route('movimientos.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md text-sm {{ request()->routeIs('movimientos.index') ? 'bg-slate-800 text-white' : 'hover:bg-slate-800 hover:text-white' }}">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Movimientos
        </a>
        @endif

        @if ($can('ver reportes'))
        <a href="{{ route('reportes.inventario') }}" class="flex items-center gap-3 px-3 py-2 rounded-md text-sm {{ request()->routeIs('reportes*') ? 'bg-slate-800 text-white' : 'hover:bg-slate-800 hover:text-white' }}">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            Reportes
        </a>
        @endif

        @if ($can('gestionar usuarios'))
        <a href="{{ route('usuarios.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md text-sm {{ request()->routeIs('usuarios*') ? 'bg-slate-800 text-white' : 'hover:bg-slate-800 hover:text-white' }}">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            Usuarios
        </a>
        @endif
    </nav>
</aside>