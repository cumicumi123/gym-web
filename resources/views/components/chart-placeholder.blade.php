@props(['id' => 'chart-placeholder'])

<div {{ $attributes->merge(['class' => 'chart-skeleton']) }} aria-hidden="true">
    <svg class="chart-skeleton-line" viewBox="0 0 600 240" preserveAspectRatio="none" fill="none" xmlns="http://www.w3.org/2000/svg">
        <defs>
            <linearGradient id="{{ $id }}-fill" x1="0" y1="0" x2="0" y2="1">
                <stop offset="0%" stop-color="rgba(99, 102, 241, 0.26)" />
                <stop offset="100%" stop-color="rgba(99, 102, 241, 0)" />
            </linearGradient>
        </defs>

        <line x1="0" y1="48" x2="600" y2="48" stroke="rgba(255, 255, 255, 0.05)" stroke-dasharray="4 7" />
        <line x1="0" y1="96" x2="600" y2="96" stroke="rgba(255, 255, 255, 0.05)" stroke-dasharray="4 7" />
        <line x1="0" y1="144" x2="600" y2="144" stroke="rgba(255, 255, 255, 0.05)" stroke-dasharray="4 7" />
        <line x1="0" y1="192" x2="600" y2="192" stroke="rgba(255, 255, 255, 0.05)" stroke-dasharray="4 7" />

        <path d="M0 190 L100 150 L200 168 L300 104 L400 132 L500 62 L600 92 L600 240 L0 240 Z" fill="url(#{{ $id }}-fill)" />
        <path d="M0 190 L100 150 L200 168 L300 104 L400 132 L500 62 L600 92" stroke="rgba(143, 180, 255, 0.55)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
    </svg>
</div>
