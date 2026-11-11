@props(['level' => 'work'])

<span class="inline-flex items-center gap-1.5 rounded bg-paper px-2.5 py-1 text-[13px]">
    {{ $slot }}
    @if ($level === 'work')
        <span class="rounded-[3px] bg-accent px-1.5 text-[11px] font-bold text-white">実務</span>
    @else
        <span class="rounded-[3px] border border-accent px-[5px] text-[11px] font-bold text-accent">個人</span>
    @endif
</span>
