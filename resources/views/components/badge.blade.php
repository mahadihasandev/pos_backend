@props([
    'variant' => 'default',
])

@php
$classes = match($variant) {
    'success' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
    'warning' => 'bg-amber-500/10 text-amber-400 border-amber-500/20',
    'danger' => 'bg-rose-500/10 text-rose-400 border-rose-500/20',
    'info' => 'bg-sky-500/10 text-sky-400 border-sky-500/20',
    default => 'bg-slate-800 text-slate-300 border-slate-700',
};
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {$classes}"]) }}>
    {{ $slot }}
</span>
