@php
    $messages = $messages ?? collect();
    $unreadMessageIds = collect($unreadMessageIds ?? []);
@endphp

<div id="section-chat-{{ $section }}" class="mt-6 border-t border-slate-200 pt-5">
    <div class="flex items-center justify-between gap-3">
        <h4 class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-500">{{ __('ui.internal_chat') }}</h4>
        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">{{ $messages->count() }}</span>
    </div>

    <div class="mt-4 space-y-3">
        @forelse ($messages as $message)
            <article class="rounded-2xl border p-4 {{ $unreadMessageIds->contains($message->id) ? 'border-sky-300 bg-sky-50/60 ring-1 ring-sky-200' : 'border-slate-200 bg-white' }}">
                <div class="space-y-3 text-sm leading-6 text-slate-700 [&_p]:my-0 [&_strong]:font-semibold [&_strong]:text-slate-900 [&_ul]:list-disc [&_ul]:pl-5 [&_ol]:list-decimal [&_ol]:pl-5 [&_code]:rounded-md [&_code]:bg-slate-100 [&_code]:px-1.5 [&_code]:py-0.5 [&_code]:text-[0.95em] [&_a]:font-medium [&_a]:text-sky-700 [&_a]:underline">
                    {!! \Illuminate\Support\Str::markdown($message->message, [
                        'html_input' => 'strip',
                        'allow_unsafe_links' => false,
                    ]) !!}
                </div>
                <div class="mt-3 flex flex-wrap items-center gap-x-2 gap-y-1 text-xs font-semibold uppercase tracking-[0.16em]" style="color: {{ $message->accentColor() }}">
                    <span>{{ $message->authorDisplayName() }}</span>
                    @if ($message->authorRoleLabel())
                        <span>&middot;</span>
                        <span>{{ $message->authorRoleLabel() }}</span>
                    @endif
                    <span>&middot;</span>
                    <span>{{ $message->created_at->format('d/m/Y H:i') }}</span>
                    @if ($unreadMessageIds->contains($message->id))
                        <span>&middot;</span>
                        <span class="rounded-full bg-sky-100 px-2 py-0.5 text-[10px] font-bold tracking-[0.18em] text-sky-700">{{ __('ui.new') }}</span>
                    @endif
                </div>
            </article>
        @empty
            <div class="rounded-2xl border border-dashed border-slate-300 p-5 text-sm text-slate-500">
                {{ __('ui.no_messages_in_section_yet') }}
            </div>
        @endforelse
    </div>

    <form method="POST" action="{{ $action }}" class="mt-4 space-y-3" x-data="showControlEmojiPicker('message-{{ $section }}')">
        @csrf
        <input type="hidden" name="section" value="{{ $section }}">
        <div>
            <div class="flex items-center justify-between gap-3">
                <x-input-label for="message-{{ $section }}" :value="__('ui.new_message')" />
                <div class="relative">
                    <button
                        type="button"
                        @click="open = !open"
                        class="inline-flex items-center justify-center rounded-full border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-600 transition hover:border-sky-300 hover:text-slate-900"
                    >
                        {{ __('ui.emoji') }}
                    </button>
                    <div
                        x-show="open"
                        x-transition
                        @click.outside="open = false"
                        class="absolute right-0 z-20 mt-2 w-56 rounded-2xl border border-slate-200 bg-white p-3 shadow-xl"
                        style="display: none;"
                    >
                        <div class="grid grid-cols-4 gap-2">
                            <template x-for="emoji in emojis" :key="emoji">
                                <button
                                    type="button"
                                    @click="insert(emoji)"
                                    class="inline-flex h-10 items-center justify-center rounded-2xl bg-slate-50 text-lg transition hover:bg-sky-50"
                                    x-text="emoji"
                                ></button>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
            <textarea
                id="message-{{ $section }}"
                name="message"
                rows="3"
                class="mt-1 block w-full rounded-2xl border-slate-300 shadow-sm focus:border-sky-500 focus:ring-sky-500"
                placeholder="{{ __('ui.section_message_placeholder') }}"
            >{{ old('section') === $section ? old('message') : '' }}</textarea>
            @if (old('section') === $section)
                <x-input-error class="mt-2" :messages="$errors->get('message')" />
                <x-input-error class="mt-2" :messages="$errors->get('section')" />
            @endif
        </div>
        <button type="submit" class="inline-flex items-center justify-center rounded-full bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800">
            {{ __('ui.send_message') }}
        </button>
    </form>
</div>
