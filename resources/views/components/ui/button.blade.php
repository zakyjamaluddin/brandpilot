@props([
    'variant' => 'primary', // 'primary', 'rosegold', 'secondary', 'outline', 'ghost'
    'size' => 'md',        // 'sm', 'md', 'lg'
    'href' => null,
    'icon' => null,
    'iconPosition' => 'right',
])

@php
    $baseClasses = 'inline-flex items-center justify-center font-bold rounded-full transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-offset-2';

    $sizeClasses = [
        'sm' => 'text-xs px-4 py-2 gap-1.5',
        'md' => 'text-sm px-6 py-2.5 gap-2',
        'lg' => 'text-base px-8 py-4 gap-2.5',
    ][$size] ?? 'text-sm px-6 py-2.5 gap-2';

    $variantClasses = [
        'primary' => 'btn-burgundy text-white focus:ring-burgundy-700',
        'rosegold' => 'btn-rosegold text-white focus:ring-rosegold-400',
        'secondary' => 'bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 hover:border-rosegold-300 shadow-sm focus:ring-slate-300',
        'outline' => 'bg-transparent text-burgundy-900 border border-burgundy-800/30 hover:bg-burgundy-50 focus:ring-burgundy-700',
        'ghost' => 'bg-transparent text-burgundy-800 hover:bg-burgundy-50/50 hover:text-burgundy-950 focus:ring-transparent',
    ][$variant] ?? 'btn-burgundy text-white';

    $classes = "{$baseClasses} {$sizeClasses} {$variantClasses}";
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if($icon && $iconPosition === 'left')
            <span class="inline-flex shrink-0">{!! $icon !!}</span>
        @endif
        <span>{{ $slot }}</span>
        @if($icon && $iconPosition === 'right')
            <span class="inline-flex shrink-0">{!! $icon !!}</span>
        @endif
    </a>
@else
    <button {{ $attributes->merge(['type' => 'button', 'class' => $classes]) }}>
        @if($icon && $iconPosition === 'left')
            <span class="inline-flex shrink-0">{!! $icon !!}</span>
        @endif
        <span>{{ $slot }}</span>
        @if($icon && $iconPosition === 'right')
            <span class="inline-flex shrink-0">{!! $icon !!}</span>
        @endif
    </button>
@endif
