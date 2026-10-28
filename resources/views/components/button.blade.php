@props([
    'href' => null,
    'variant' => 'primary',
    'size' => null,
    'type' => 'button',
    'icon' => null,
])

@php
    $classes = trim('btn btn-'.$variant.($size ? ' btn-'.$size : ''));
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
        @if ($icon)
            <x-icon :name="$icon" class="h-4 w-4" />
        @endif
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
        @if ($icon)
            <x-icon :name="$icon" class="h-4 w-4" />
        @endif
    </button>
@endif
