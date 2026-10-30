@props([
    'level' => 'h2', // 'h1', 'h2', 'h3'
    'accent' => null,
    'align' => 'left',
])

@php
    $alignClasses = [
        'left' => 'text-left',
        'center' => 'text-center mx-auto',
        'right' => 'text-right ml-auto',
    ][$align] ?? 'text-left';

    $levelClasses = [
        'h1' => 'text-4xl md:text-6xl lg:text-7xl font-extrabold tracking-tight text-slate-900 leading-[1.15]',
        'h2' => 'text-3xl md:text-5xl font-extrabold tracking-tight text-slate-900 leading-tight',
        'h3' => 'text-2xl md:text-3xl font-bold tracking-tight text-slate-900',
    ][$level] ?? 'text-3xl md:text-5xl font-extrabold tracking-tight text-slate-900';
@endphp

@if($level === 'h1')
    <h1 {{ $attributes->merge(['class' => "{$levelClasses} {$alignClasses}"]) }}>
        {{ $slot }}
        @if($accent)
            <span class="gradient-text font-serif italic font-normal">{{ $accent }}</span>
        @endif
    </h1>
@elseif($level === 'h3')
    <h3 {{ $attributes->merge(['class' => "{$levelClasses} {$alignClasses}"]) }}>
        {{ $slot }}
        @if($accent)
            <span class="gradient-text font-serif italic font-normal">{{ $accent }}</span>
        @endif
    </h3>
@else
    <h2 {{ $attributes->merge(['class' => "{$levelClasses} {$alignClasses}"]) }}>
        {{ $slot }}
        @if($accent)
            <span class="gradient-text font-serif italic font-normal">{{ $accent }}</span>
        @endif
    </h2>
@endif
