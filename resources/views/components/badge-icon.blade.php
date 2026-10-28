@props(['name' => 'sparkle', 'color' => 'purple', 'class' => ''])

<span
    {{ $attributes->merge(['class' => 'icon-badge '.$class]) }}
    style="--badge-color: var(--ic-{{ $color }});"
    aria-hidden="true"
>
    <x-icon :name="$name" class="h-5 w-5" />
</span>
