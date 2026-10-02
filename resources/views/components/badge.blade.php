@props([
    'variant' => 'default',
])

@php
$classes = match($variant) {
    'success' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
    'warning' => 'bg-amber-50 text-amber-700 border-amber-200',
    'danger' => 'bg-rose-50 text-rose-700 border-rose-200',
    'info' => 'bg-sky-50 text-sky-700 border-sky-200',
    default => 'bg-slate-100 text-slate-700 border-slate-200',
};
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border {$classes}"]) }}>
    {{ $slot }}
</span>
