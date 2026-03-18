<nav x-data="{ open: false }" class="relative z-50 border-b border-white/70 bg-white/90 backdrop-blur">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 justify-between gap-4">
            <div class="flex min-w-0 items-center gap-6">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-slate-900 text-sm font-bold text-white">SC</span>
                    <div class="hidden sm:block">
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Show Control</p>
                        <p class="text-sm font-semibold text-slate-900">Tours & Production</p>
                    </div>
                </a>

                <div class="hidden items-center gap-2 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        Dashboard
                    </x-nav-link>
                    @can('manage shows')
                        <x-nav-link :href="route('shows.index')" :active="request()->routeIs('shows.index', 'shows.create', 'shows.store', 'shows.show', 'shows.edit', 'shows.update', 'shows.destroy', 'shows.pdf', 'shows.documents.*')">
                            Bolos
                        </x-nav-link>
                        <x-nav-link :href="route('shows.calendar')" :active="request()->routeIs('shows.calendar')">
                            Calendario
                        </x-nav-link>
                    @endcan
                    @can('manage tours')
                        <x-nav-link :href="route('tours.index')" :active="request()->routeIs('tours.*')">
                            Giras
                        </x-nav-link>
                    @endcan
                    @can('manage access')
                        <x-nav-link :href="route('shared-accesses.index')" :active="request()->routeIs('shared-accesses.*')">
                            Accesos
                        </x-nav-link>
                    @endcan
                </div>
            </div>

            <div class="hidden items-center gap-3 sm:flex">
                <div class="text-right">
                    <p class="text-sm font-semibold text-slate-900">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-slate-500">{{ Auth::user()->getRoleNames()->implode(', ') ?: 'Sin rol' }}</p>
                </div>

                <x-dropdown align="right" width="48" contentClasses="overflow-hidden rounded-2xl border border-slate-200 bg-white py-2 shadow-xl">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center rounded-full border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-600 shadow-sm transition hover:text-slate-900">
                            Cuenta
                            <svg class="ms-2 h-4 w-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="border-b border-slate-100 px-4 py-3">
                            <p class="text-sm font-semibold text-slate-900">{{ Auth::user()->name }}</p>
                            <p class="mt-1 text-xs text-slate-500">{{ Auth::user()->email }}</p>
                        </div>

                        <button
                            type="button"
                            x-data
                            @click="$store.theme.toggle()"
                            class="flex w-full items-center justify-between px-4 py-3 text-left text-sm font-medium text-slate-600 transition hover:bg-slate-100 hover:text-slate-900"
                        >
                            <span>Modo oscuro</span>
                            <span class="rounded-full px-2 py-1 text-xs font-semibold" :class="$store.theme.current === 'dark' ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-600'">
                                <span x-text="$store.theme.current === 'dark' ? 'On' : 'Off'"></span>
                            </span>
                        </button>

                        <x-dropdown-link :href="route('profile.edit')">
                            Perfil
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                                Cerrar sesion
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <div class="flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center rounded-xl border border-slate-200 p-2 text-slate-500">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden border-t border-slate-200 bg-white sm:hidden">
        <div class="space-y-1 px-4 py-3">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                Dashboard
            </x-responsive-nav-link>
            @can('manage shows')
                <x-responsive-nav-link :href="route('shows.index')" :active="request()->routeIs('shows.index', 'shows.create', 'shows.store', 'shows.show', 'shows.edit', 'shows.update', 'shows.destroy', 'shows.pdf', 'shows.documents.*')">
                    Bolos
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('shows.calendar')" :active="request()->routeIs('shows.calendar')">
                    Calendario
                </x-responsive-nav-link>
            @endcan
            @can('manage tours')
                <x-responsive-nav-link :href="route('tours.index')" :active="request()->routeIs('tours.*')">
                    Giras
                </x-responsive-nav-link>
            @endcan
            @can('manage access')
                <x-responsive-nav-link :href="route('shared-accesses.index')" :active="request()->routeIs('shared-accesses.*')">
                    Accesos
                </x-responsive-nav-link>
            @endcan
            <button
                type="button"
                x-data
                @click="$store.theme.toggle()"
                class="block w-full rounded-2xl px-4 py-3 text-start text-base font-semibold text-slate-600 transition hover:bg-slate-100 hover:text-slate-900"
            >
                <span class="flex items-center justify-between gap-3">
                    <span>Modo oscuro</span>
                    <span class="rounded-full px-2 py-1 text-xs font-semibold" :class="$store.theme.current === 'dark' ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-600'">
                        <span x-text="$store.theme.current === 'dark' ? 'On' : 'Off'"></span>
                    </span>
                </span>
            </button>
            <x-responsive-nav-link :href="route('profile.edit')">
                Perfil
            </x-responsive-nav-link>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                    Cerrar sesion
                </x-responsive-nav-link>
            </form>
        </div>
    </div>
</nav>
