@props(['work'])

{{-- 実案件とサンプルを見分けるラベル。色だけに頼らず、文字でも区別する --}}
<span {{ $attributes->class(['badge', 'badge--client' => $work->label === 'client', 'badge--demo' => $work->label === 'demo']) }}>{{ $work->labelText() }}</span>
