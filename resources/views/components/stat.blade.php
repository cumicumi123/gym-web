@props(['value', 'label', 'suffix' => null])

<div class="stat-cell">
    <div class="stat-value">
        {{ $value }}@if ($suffix)<span class="text-[13px] text-muted">{{ $suffix }}</span>@endif
    </div>
    <div class="stat-label">{{ $label }}</div>
</div>
