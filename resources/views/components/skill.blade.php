@props(['level' => 'work'])

{{-- スキルの丸いタグ（モックの .skills span）。実務か個人かを小さく添える --}}
<span class="skill">
    {{ $slot }}
    <span @class(['skill-level', 'skill-level--work' => $level === 'work'])>{{ $level === 'work' ? '実務' : '個人' }}</span>
</span>
