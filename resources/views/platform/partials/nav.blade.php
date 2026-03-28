<div class="overflow-x-auto rounded-[2rem] border border-slate-200 bg-white p-3 shadow-sm">
    <nav class="flex min-w-max gap-2">
        <a href="{{ route('platform.users.index') }}" class="whitespace-nowrap rounded-2xl px-4 py-3 text-sm font-semibold transition {{ request()->routeIs('platform.users.*') ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
            {{ __('ui.users') }}
        </a>
        <a href="{{ route('platform.settings.edit') }}" class="whitespace-nowrap rounded-2xl px-4 py-3 text-sm font-semibold transition {{ request()->routeIs('platform.settings.*') ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
            {{ __('ui.settings') }}
        </a>
        <a href="{{ route('platform.mail.edit') }}" class="whitespace-nowrap rounded-2xl px-4 py-3 text-sm font-semibold transition {{ request()->routeIs('platform.mail.*') ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
            {{ __('ui.mail') }}
        </a>
        <a href="{{ route('platform.tools.index') }}" class="whitespace-nowrap rounded-2xl px-4 py-3 text-sm font-semibold transition {{ request()->routeIs('platform.tools.*') ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
            {{ __('ui.tools') }}
        </a>
    </nav>
</div>
