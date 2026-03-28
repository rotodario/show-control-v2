<div class="overflow-x-auto rounded-[2rem] border border-slate-200 bg-white p-3 shadow-sm">
    <nav class="flex min-w-max gap-2">
        <a href="{{ route('account.profile') }}" class="whitespace-nowrap rounded-2xl px-4 py-3 text-sm font-semibold transition {{ ($accountSection ?? null) === 'profile' ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
            {{ __('ui.profile') }}
        </a>
        <a href="{{ route('account.alerts') }}" class="whitespace-nowrap rounded-2xl px-4 py-3 text-sm font-semibold transition {{ ($accountSection ?? null) === 'alerts' ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
            {{ __('ui.alerts') }}
        </a>
        <a href="{{ route('account.pdf') }}" class="whitespace-nowrap rounded-2xl px-4 py-3 text-sm font-semibold transition {{ ($accountSection ?? null) === 'pdf' ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
            {{ __('ui.pdf_branding') }}
        </a>
        <a href="{{ route('account.preferences') }}" class="whitespace-nowrap rounded-2xl px-4 py-3 text-sm font-semibold transition {{ ($accountSection ?? null) === 'preferences' ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
            {{ __('ui.preferences') }}
        </a>
        <a href="{{ route('account.mail') }}" class="whitespace-nowrap rounded-2xl px-4 py-3 text-sm font-semibold transition {{ ($accountSection ?? null) === 'mail' ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
            {{ __('ui.mail') }}
        </a>
    </nav>
</div>
