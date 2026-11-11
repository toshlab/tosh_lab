@props(['work'])

@php
    $classes = match ($work->label) {
        'client' => 'bg-accent text-white',
        'demo' => 'bg-accent-soft text-accent-dark',
        default => 'bg-sample-soft text-sample',
    };
@endphp

<span {{ $attributes->class(['rounded px-2.5 py-0.5 text-xs font-bold', $classes]) }}>{{ $work->labelText() }}</span>
