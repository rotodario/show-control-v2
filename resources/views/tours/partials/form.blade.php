@csrf

<div class="grid gap-6">
    <div class="grid gap-6 lg:grid-cols-[1fr_180px]">
        <div>
            <x-input-label for="name" :value="__('ui.tour_name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $tour->name)" required autofocus />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="color" :value="__('ui.color')" />
            <input id="color" name="color" type="color" value="{{ old('color', $tour->color ?? '#2563EB') }}" class="mt-1 block h-11 w-full rounded-xl border-slate-300 bg-white shadow-sm" required>
            <x-input-error class="mt-2" :messages="$errors->get('color')" />
        </div>
    </div>

    <div>
        <x-input-label for="notes" :value="__('ui.notes')" />
        <textarea id="notes" name="notes" rows="6" class="mt-1 block w-full rounded-2xl border-slate-300 shadow-sm focus:border-sky-500 focus:ring-sky-500">{{ old('notes', $tour->notes) }}</textarea>
        <x-input-error class="mt-2" :messages="$errors->get('notes')" />
    </div>
</div>

<div class="mt-8 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
    <a href="{{ $tour->exists ? route('tours.show', $tour) : route('tours.index') }}" class="inline-flex items-center justify-center rounded-full border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">
        {{ __('ui.cancel') }}
    </a>
    <button type="submit" class="inline-flex items-center justify-center rounded-full bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
        {{ __('ui.save_tour') }}
    </button>
</div>
