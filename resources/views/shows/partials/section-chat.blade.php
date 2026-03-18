@php
    $messages = $messages ?? collect();
@endphp

<div id="section-chat-{{ $section }}" class="mt-6 border-t border-slate-200 pt-5">
    <div class="flex items-center justify-between gap-3">
        <h4 class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-500">Chat interno</h4>
        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">{{ $messages->count() }}</span>
    </div>

    <div class="mt-4 space-y-3">
        @forelse ($messages as $message)
            <article class="rounded-2xl border border-slate-200 bg-white p-4">
                <p class="whitespace-pre-line text-sm leading-6 text-slate-700">{{ $message->message }}</p>
                <div class="mt-3 flex flex-wrap items-center gap-x-2 gap-y-1 text-xs font-semibold uppercase tracking-[0.16em]" style="color: {{ $message->accentColor() }}">
                    <span>{{ $message->authorDisplayName() }}</span>
                    @if ($message->authorRoleLabel())
                        <span>&middot;</span>
                        <span>{{ $message->authorRoleLabel() }}</span>
                    @endif
                    <span>&middot;</span>
                    <span>{{ $message->created_at->format('d/m/Y H:i') }}</span>
                </div>
            </article>
        @empty
            <div class="rounded-2xl border border-dashed border-slate-300 p-5 text-sm text-slate-500">
                Todavia no hay mensajes en este apartado.
            </div>
        @endforelse
    </div>

    <form method="POST" action="{{ $action }}" class="mt-4 space-y-3">
        @csrf
        <input type="hidden" name="section" value="{{ $section }}">
        <div>
            <x-input-label for="message-{{ $section }}" value="Nuevo mensaje" />
            <textarea
                id="message-{{ $section }}"
                name="message"
                rows="3"
                class="mt-1 block w-full rounded-2xl border-slate-300 shadow-sm focus:border-sky-500 focus:ring-sky-500"
                placeholder="Escribe un mensaje para este apartado..."
            >{{ old('section') === $section ? old('message') : '' }}</textarea>
            @if (old('section') === $section)
                <x-input-error class="mt-2" :messages="$errors->get('message')" />
                <x-input-error class="mt-2" :messages="$errors->get('section')" />
            @endif
        </div>
        <button type="submit" class="inline-flex items-center justify-center rounded-full bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800">
            Enviar mensaje
        </button>
    </form>
</div>
