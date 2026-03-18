@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center rounded-full bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition'
            : 'inline-flex items-center rounded-full px-4 py-2 text-sm font-semibold text-slate-500 transition hover:bg-slate-100 hover:text-slate-900';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
