@props([
    'label' => null,
    'name' => null,
    'type' => 'text',
    'placeholder' => '',
    'error' => null,
    'icon' => null,
    'hint' => null,
    'required' => false,
])

<div class="w-full space-y-1.5">
    @if($label)
        <label for="{{ $name }}" class="block text-xs font-bold text-slate-700 tracking-wide uppercase">
            {{ $label }}
            @if($required)
                <span class="text-burgundy-800">*</span>
            @endif
        </label>
    @endif

    <div class="relative rounded-xl shadow-sm">
        @if($icon)
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                {!! $icon !!}
            </div>
        @endif

        <input
            type="{{ $type }}"
            name="{{ $name }}"
            id="{{ $name }}"
            placeholder="{{ $placeholder }}"
            {{ $required ? 'required' : '' }}
            {{ $attributes->merge([
                'class' => 'w-full rounded-xl border bg-white py-3 ' .
                ($icon ? 'pl-11 pr-4' : 'px-4') .
                ' text-sm text-slate-900 placeholder:text-slate-400 ' .
                ($error ? 'border-red-400 focus:border-red-500 focus:ring-red-200' : 'border-slate-200 focus:border-burgundy-800 focus:ring-burgundy-800/10') .
                ' focus:outline-none focus:ring-4 transition duration-200'
            ]) }}
        />
    </div>

    @if($error)
        <p class="text-xs text-red-600 font-medium mt-1">{{ $error }}</p>
    @elseif($hint)
        <p class="text-xs text-slate-500 mt-1">{{ $hint }}</p>
    @endif
</div>
