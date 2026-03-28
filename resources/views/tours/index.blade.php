<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500 dark:text-slate-400">{{ __('ui.tours') }}</p>
                <h2 class="text-2xl font-semibold text-slate-900 dark:text-slate-100">{{ __('ui.tours') }}</h2>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ __('ui.tours_index_description') }}</p>
            </div>
            <div class="flex flex-col gap-3 sm:flex-row">
                <a href="{{ route('tours.google-calendar.index') }}" class="inline-flex items-center justify-center rounded-full border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">
                    {{ __('ui.import_ics_url') }}
                </a>
                <a href="{{ route('tours.create') }}" class="inline-flex items-center justify-center rounded-full bg-sky-600 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-500">
                    {{ __('ui.new_tour') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <x-status-message />

            <div class="grid gap-4 lg:grid-cols-2">
                @forelse ($tours as $tour)
                    <article class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900/90">
                        <div class="flex items-start gap-4">
                            <span class="mt-1 h-16 w-3 rounded-full" style="background-color: {{ $tour->color }}"></span>
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <h3 class="truncate text-xl font-semibold text-slate-900 dark:text-slate-100">{{ $tour->name }}</h3>
                                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ __('ui.updated_at_human', ['time' => $tour->updated_at->diffForHumans()]) }}</p>
                                    </div>
                                    <div class="flex flex-wrap gap-2">
                                        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-600 dark:bg-slate-800 dark:text-slate-300">{{ __('ui.shows_count', ['count' => $tour->shows_count]) }}</span>
                                        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-600 dark:bg-slate-800 dark:text-slate-300">{{ __('ui.contacts_count', ['count' => $tour->contacts_count]) }}</span>
                                        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-600 dark:bg-slate-800 dark:text-slate-300">{{ __('ui.docs_count', ['count' => $tour->documents_count]) }}</span>
                                    </div>
                                </div>

                                <p class="mt-4 text-sm leading-6 text-slate-600 dark:text-slate-300">{{ $tour->notes ?: __('ui.no_tour_notes_yet') }}</p>

                                <div class="mt-6 flex flex-col gap-3 sm:flex-row">
                                    <a href="{{ route('tours.show', $tour) }}" class="inline-flex items-center justify-center rounded-full bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800">
                                        {{ __('ui.open_record') }}
                                    </a>
                                    <a href="{{ route('tours.edit', $tour) }}" class="inline-flex items-center justify-center rounded-full border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">
                                        {{ __('ui.edit') }}
                                    </a>
                                    <form method="POST" action="{{ route('tours.destroy', $tour) }}" onsubmit="return confirm('{{ __('ui.confirm_delete_tour') }}');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex w-full items-center justify-center rounded-full border border-rose-200 px-4 py-2.5 text-sm font-semibold text-rose-700 transition hover:bg-rose-50 dark:border-rose-900 dark:text-rose-300 dark:hover:bg-rose-950/50 sm:w-auto">
                                            {{ __('ui.delete') }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="rounded-[2rem] border border-dashed border-slate-300 bg-white p-10 text-center text-sm text-slate-500 lg:col-span-2 dark:border-slate-700 dark:bg-slate-900/90 dark:text-slate-400">
                        {{ __('ui.no_tours_yet') }}
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
