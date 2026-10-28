@props(['gid' => 'db'])

<svg {{ $attributes->merge(['class' => 'dumbbell-svg', 'viewBox' => '0 0 280 100', 'fill' => 'none']) }} xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
    <defs>
        <linearGradient id="{{ $gid }}-plate" x1="0" y1="0" x2="1" y2="0">
            <stop offset="0" stop-color="#f1f4f9" />
            <stop offset="0.16" stop-color="#ffffff" />
            <stop offset="0.4" stop-color="#b3bbc8" />
            <stop offset="0.62" stop-color="#59616e" />
            <stop offset="0.84" stop-color="#cfd6e0" />
            <stop offset="1" stop-color="#767f8d" />
        </linearGradient>
        <linearGradient id="{{ $gid }}-plate-inner" x1="0" y1="0" x2="1" y2="0">
            <stop offset="0" stop-color="#f8fafc" />
            <stop offset="0.22" stop-color="#ffffff" />
            <stop offset="0.5" stop-color="#9ea7b5" />
            <stop offset="0.72" stop-color="#4b535f" />
            <stop offset="0.9" stop-color="#c3cbd6" />
            <stop offset="1" stop-color="#7d8694" />
        </linearGradient>
        <linearGradient id="{{ $gid }}-bar" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0" stop-color="#fbfcfe" />
            <stop offset="0.28" stop-color="#dfe4eb" />
            <stop offset="0.52" stop-color="#8f97a5" />
            <stop offset="0.74" stop-color="#4d5561" />
            <stop offset="1" stop-color="#aab2bf" />
        </linearGradient>
        <linearGradient id="{{ $gid }}-hub" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0" stop-color="#ffffff" />
            <stop offset="0.45" stop-color="#c5ccd7" />
            <stop offset="0.7" stop-color="#68717f" />
            <stop offset="1" stop-color="#c8cfd9" />
        </linearGradient>
    </defs>

    <rect x="80" y="43" width="120" height="14" rx="7" fill="url(#{{ $gid }}-bar)" />
    <rect x="86" y="45.5" width="108" height="2" rx="1" fill="#ffffff" opacity="0.55" />

    <rect x="66" y="8" width="24" height="84" rx="11" fill="url(#{{ $gid }}-plate-inner)" stroke="#0A0A0A" stroke-opacity="0.22" />
    <rect x="190" y="8" width="24" height="84" rx="11" fill="url(#{{ $gid }}-plate-inner)" stroke="#0A0A0A" stroke-opacity="0.22" />

    <rect x="46" y="14" width="22" height="72" rx="10" fill="url(#{{ $gid }}-plate)" stroke="#0A0A0A" stroke-opacity="0.22" />
    <rect x="212" y="14" width="22" height="72" rx="10" fill="url(#{{ $gid }}-plate)" stroke="#0A0A0A" stroke-opacity="0.22" />

    <rect x="32" y="36" width="16" height="28" rx="8" fill="url(#{{ $gid }}-hub)" stroke="#0A0A0A" stroke-opacity="0.22" />
    <rect x="232" y="36" width="16" height="28" rx="8" fill="url(#{{ $gid }}-hub)" stroke="#0A0A0A" stroke-opacity="0.22" />

    <rect x="68" y="10" width="1.6" height="80" rx="0.8" fill="#272d36" opacity="0.55" />
    <rect x="190.4" y="10" width="1.6" height="80" rx="0.8" fill="#272d36" opacity="0.55" />

    <rect x="75" y="16" width="3" height="68" rx="1.5" fill="#ffffff" opacity="0.62" />
    <rect x="201" y="16" width="3" height="68" rx="1.5" fill="#ffffff" opacity="0.62" />

    <rect x="50" y="20" width="3.5" height="60" rx="1.75" fill="#ffffff" opacity="0.55" />
    <rect x="216.5" y="20" width="3.5" height="60" rx="1.75" fill="#ffffff" opacity="0.55" />
</svg>
