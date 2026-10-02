@props([
    'head' => null,
])

<div class="overflow-x-auto border border-slate-200/80 rounded-xl">
    <table class="w-full text-left border-collapse">
        @if($head)
            <thead class="bg-slate-50 text-slate-600 text-xs uppercase font-bold tracking-wider border-b border-slate-200/80">
                <tr>
                    {{ $head }}
                </tr>
            </thead>
        @endif
        <tbody class="divide-y divide-slate-100 text-sm text-slate-700 bg-white">
            {{ $slot }}
        </tbody>
    </table>
</div>
