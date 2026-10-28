@props(['padding' => true, 'class' => ''])

<div {{ $attributes->merge(['class' => 'card '.($padding ? 'card-pad ' : '').$class]) }}>
    {{ $slot }}
</div>
