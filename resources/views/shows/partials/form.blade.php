@csrf

<div class="space-y-8">
    <div class="grid gap-4 lg:grid-cols-2">
        <div>
            <x-input-label for="name" value="Nombre del bolo" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $show->name)" required />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>
        <div>
            <x-input-label for="tour_id" value="Gira" />
            <select id="tour_id" name="tour_id" class="mt-1 block w-full rounded-2xl border-slate-300 shadow-sm focus:border-sky-500 focus:ring-sky-500">
                <option value="">Sin gira</option>
                @foreach ($tours as $tour)
                    <option value="{{ $tour->id }}" @selected((string) old('tour_id', $show->tour_id) === (string) $tour->id)>{{ $tour->name }}</option>
                @endforeach
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('tour_id')" />
        </div>
        <div>
            <x-input-label for="date" value="Fecha" />
            <x-text-input id="date" name="date" type="date" class="mt-1 block w-full" :value="old('date', $show->date?->format('Y-m-d') ?? $show->date)" required />
            <x-input-error class="mt-2" :messages="$errors->get('date')" />
        </div>
        <div>
            <x-input-label for="status" value="Estado" />
            <select id="status" name="status" class="mt-1 block w-full rounded-2xl border-slate-300 shadow-sm focus:border-sky-500 focus:ring-sky-500">
                @foreach ($statusOptions as $value => $label)
                    <option value="{{ $value }}" @selected(old('status', $show->status) === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('status')" />
        </div>
        <div>
            <x-input-label for="city" value="Ciudad" />
            <x-text-input id="city" name="city" type="text" class="mt-1 block w-full" :value="old('city', $show->city)" required />
            <x-input-error class="mt-2" :messages="$errors->get('city')" />
        </div>
        <div>
            <x-input-label for="venue" value="Venue" />
            <x-text-input id="venue" name="venue" type="text" class="mt-1 block w-full" :value="old('venue', $show->venue)" />
            <x-input-error class="mt-2" :messages="$errors->get('venue')" />
        </div>
    </div>

    <div>
        <h3 class="text-lg font-semibold text-slate-900">Horarios</h3>
        <div class="mt-4 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ([
                'load_in_at' => 'Montaje',
                'meal_at' => 'Comida',
                'soundcheck_at' => 'Pruebas',
                'doors_at' => 'Apertura de puertas',
                'show_at' => 'Show',
                'show_end_at' => 'Fin show',
                'load_out_at' => 'Desmontaje',
            ] as $field => $label)
                <div>
                    <x-input-label :for="$field" :value="$label" />
                    <x-text-input :id="$field" :name="$field" type="time" class="mt-1 block w-full" :value="old($field, $show->getRawOriginal($field))" />
                    <x-input-error class="mt-2" :messages="$errors->get($field)" />
                </div>
            @endforeach
        </div>
    </div>

    <div>
        <h3 class="text-lg font-semibold text-slate-900">Contacto</h3>
        <div class="mt-4 grid gap-4 lg:grid-cols-2">
            <div>
                <x-input-label for="contact_name" value="Nombre" />
                <x-text-input id="contact_name" name="contact_name" type="text" class="mt-1 block w-full" :value="old('contact_name', $show->contact_name)" />
                <x-input-error class="mt-2" :messages="$errors->get('contact_name')" />
            </div>
            <div>
                <x-input-label for="contact_role" value="Rol" />
                <x-text-input id="contact_role" name="contact_role" type="text" class="mt-1 block w-full" :value="old('contact_role', $show->contact_role)" />
                <x-input-error class="mt-2" :messages="$errors->get('contact_role')" />
            </div>
            <div>
                <x-input-label for="contact_phone" value="Telefono" />
                <x-text-input id="contact_phone" name="contact_phone" type="text" class="mt-1 block w-full" :value="old('contact_phone', $show->contact_phone)" />
                <x-input-error class="mt-2" :messages="$errors->get('contact_phone')" />
            </div>
            <div>
                <x-input-label for="contact_email" value="Email" />
                <x-text-input id="contact_email" name="contact_email" type="email" class="mt-1 block w-full" :value="old('contact_email', $show->contact_email)" />
                <x-input-error class="mt-2" :messages="$errors->get('contact_email')" />
            </div>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-2">
        @foreach ([
            'lighting' => 'Iluminacion',
            'sound' => 'Sonido',
            'space' => 'Espacio / venue',
            'general' => 'Notas generales',
        ] as $prefix => $label)
            <div class="rounded-3xl bg-slate-50 p-5">
                <div class="flex items-center justify-between gap-4">
                    <h3 class="text-lg font-semibold text-slate-900">{{ $label }}</h3>
                    <label class="inline-flex items-center gap-2 text-sm font-medium text-slate-600">
                        <input type="checkbox" name="{{ $prefix }}_validated" value="1" class="rounded border-slate-300 text-sky-600 shadow-sm focus:ring-sky-500" @checked(old($prefix.'_validated', $show->{$prefix.'_validated'}))>
                        Validado
                    </label>
                </div>
                <textarea name="{{ $prefix }}_notes" rows="5" class="mt-4 block w-full rounded-2xl border-slate-300 shadow-sm focus:border-sky-500 focus:ring-sky-500">{{ old($prefix.'_notes', $show->{$prefix.'_notes'}) }}</textarea>
                <x-input-error class="mt-2" :messages="$errors->get($prefix.'_notes')" />
            </div>
        @endforeach
    </div>
</div>

<div class="mt-8 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
    <a href="{{ $show->exists ? route('shows.show', $show) : route('shows.index') }}" class="inline-flex items-center justify-center rounded-full border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">
        Cancelar
    </a>
    <button type="submit" class="inline-flex items-center justify-center rounded-full bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
        Guardar bolo
    </button>
</div>
