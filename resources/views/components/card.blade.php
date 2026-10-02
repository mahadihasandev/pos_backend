@props([
    'title' => null,
    'subtitle' => null,
])

<div {{ $attributes->merge(['class' => 'bg-white border border-slate-200/80 rounded-2xl shadow-sm overflow-hidden']) }}>
    @if($title || isset($actions))
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between gap-4 bg-slate-50/40">
            <div>
                @if($title)
                    <h3 class="text-sm font-bold text-slate-900 tracking-tight">{{ $title }}</h3>
                @endif
                @if($subtitle)
                    <p class="text-xs text-slate-500 mt-0.5">{{ $subtitle }}</p>
                @endif
            </div>

            @if(isset($actions))
                <div class="flex items-center gap-2">
                    {{ $actions }}
                </div>
            @endif
        </div>
    @endif

    <div class="p-6">
        {{ $slot }}
    </div>
</div>
