@props([
    'variant' => 'glass', // 'glass', 'solid', 'featured', 'burgundy'
    'hover' => true,
])

@php
    $baseClasses = 'rounded-2xl transition-all duration-300 relative overflow-hidden';
    
    $hoverClasses = $hover ? 'hover:-translate-y-1 hover:shadow-xl hover:shadow-burgundy-950/5' : '';

    $variantClasses = [
        'glass' => 'card-brandpilot p-6 md:p-8',
        'solid' => 'bg-white border border-slate-200/80 shadow-sm p-6 md:p-8',
        'featured' => 'bg-gradient-to-b from-white via-rosegold-50/20 to-white border-2 border-rosegold-400/40 shadow-xl shadow-rosegold-500/10 p-6 md:p-8',
        'burgundy' => 'bg-gradient-to-br from-[#4A0E17] via-burgundy-800 to-burgundy-950 text-white border border-rosegold-400/30 shadow-2xl shadow-burgundy-950/25 p-6 md:p-8',
    ][$variant] ?? 'card-brandpilot p-6 md:p-8';

    $classes = "{$baseClasses} {$hoverClasses} {$variantClasses}";
@endphp

<div {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</div>
