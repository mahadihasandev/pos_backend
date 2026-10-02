@props([
    'head' => null,
])

<div class="overflow-x-auto border border-slate-800 rounded-lg">
    <table class="w-full text-left border-collapse">
        @if($head)
            <thead class="bg-slate-800/40 text-slate-400 text-xs uppercase font-semibold tracking-wider border-b border-slate-800">
                <tr>
                    {{ $head }}
                </tr>
            </thead>
        @endif
        <tbody class="divide-y divide-slate-800/60 text-sm text-slate-300">
            {{ $slot }}
        </tbody>
    </table>
</div>
