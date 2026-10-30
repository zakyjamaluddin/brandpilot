@props([
    'variant' => 'burgundy', // 'burgundy', 'rosegold', 'emerald', 'gold', 'neutral'
    'pulse' => false,
    'size' => 'md', // 'sm', 'md'
])

@php
    $baseClasses = 'inline-flex items-center rounded-full font-bold shadow-sm transition-colors';

    $sizeClasses = [
        'sm' => 'text-[11px] px-3 py-1 gap-1.5',
        'md' => 'text-xs md:text-sm px-4 py-1.5 gap-2',
    ][$size] ?? 'text-xs px-4 py-1.5 gap-2';

    $variantClasses = [
        'burgundy' => 'bg-burgundy-50 border border-burgundy-200/80 text-burgundy-900',
        'rosegold' => 'bg-rosegold-50 border border-rosegold-200 text-rosegold-900',
        'emerald' => 'bg-emerald-50 border border-emerald-200 text-emerald-800',
        'gold' => 'bg-amber-50 border border-gold-500/40 text-amber-900',
        'neutral' => 'bg-white/80 border border-slate-200 text-slate-700',
    ][$variant] ?? 'bg-burgundy-50 border border-burgundy-200 text-burgundy-900';

    $pulseColor = [
        'burgundy' => 'bg-burgundy-800',
        'rosegold' => 'bg-rosegold-500',
        'emerald' => 'bg-emerald-500',
        'gold' => 'bg-gold-500',
        'neutral' => 'bg-slate-400',
    ][$variant] ?? 'bg-emerald-500';

    $classes = "{$baseClasses} {$sizeClasses} {$variantClasses}";
@endphp

<div {{ $attributes->merge(['class' => $classes]) }}>
    @if($pulse)
        <span class="relative flex h-2 w-2">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full {{ $pulseColor }} opacity-75"></span>
            <span class="relative inline-flex rounded-full h-2 w-2 {{ $pulseColor }}"></span>
        </span>
    @endif
    <span>{{ $slot }}</span>
</div>
