<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">{{ __('ui.shows') }}</p>
            <h2 class="text-2xl font-semibold text-slate-900">{{ __('ui.new_show') }}</h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <x-validation-summary />
            <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
                <form method="POST" action="{{ route('shows.store') }}">
                    @include('shows.partials.form')
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
