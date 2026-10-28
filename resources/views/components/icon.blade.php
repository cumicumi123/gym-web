@props(['name' => 'sparkle', 'class' => 'h-4 w-4'])

<svg
    {{ $attributes->merge(['class' => $class]) }}
    viewBox="0 0 24 24"
    fill="none"
    stroke="currentColor"
    stroke-width="1.8"
    stroke-linecap="round"
    stroke-linejoin="round"
    aria-hidden="true"
>
    @switch($name)
        @case('dumbbell')
            <path d="M6.5 6.5v11M3.5 8.5v7M17.5 6.5v11M20.5 8.5v7M6.5 12h11" />
            @break
        @case('arrow-right')
            <path d="M4.5 12h15m0 0-6-6m6 6-6 6" />
            @break
        @case('arrow-up-right')
            <path d="M7 17 17 7m0 0H8m9 0v9" />
            @break
        @case('check')
            <path d="m4.5 12.75 6 6 9-13.5" />
            @break
        @case('chart')
            <path d="M3 3v18h18" />
            <path d="M7.5 16v-4M12 16V8M16.5 16v-7" />
            @break
        @case('shield')
            <path d="M12 3 4.5 6v6c0 4.5 3.2 7.6 7.5 9 4.3-1.4 7.5-4.5 7.5-9V6L12 3Z" />
            <path d="m9 12 2 2 4-4" />
            @break
        @case('clock')
            <circle cx="12" cy="12" r="8.5" />
            <path d="M12 7.5V12l3 2" />
            @break
        @case('target')
            <circle cx="12" cy="12" r="8.5" />
            <circle cx="12" cy="12" r="4.5" />
            <circle cx="12" cy="12" r="1" fill="currentColor" stroke="none" />
            @break
        @case('bolt')
            <path d="m13 2-9 12h6l-1 8 9-12h-6l1-8Z" />
            @break
        @case('cpu')
            <rect x="6.5" y="6.5" width="11" height="11" rx="2" />
            <path d="M9.5 2.5v4M14.5 2.5v4M9.5 17.5v4M14.5 17.5v4M2.5 9.5h4M2.5 14.5h4M17.5 9.5h4M17.5 14.5h4" />
            <rect x="10" y="10" width="4" height="4" rx="1" />
            @break
        @case('database')
            <ellipse cx="12" cy="5.5" rx="7.5" ry="3" />
            <path d="M4.5 5.5v6c0 1.7 3.4 3 7.5 3s7.5-1.3 7.5-3v-6" />
            <path d="M4.5 11.5v6c0 1.7 3.4 3 7.5 3s7.5-1.3 7.5-3v-6" />
            @break
        @case('layers')
            <path d="m12 3 9 5-9 5-9-5 9-5Z" />
            <path d="m3 12 9 5 9-5M3 16l9 5 9-5" />
            @break
        @case('info')
            <circle cx="12" cy="12" r="8.5" />
            <path d="M12 11v5" />
            <circle cx="12" cy="8" r="0.9" fill="currentColor" stroke="none" />
            @break
        @case('menu')
            <path d="M4 7h16M4 12h16M4 17h16" />
            @break
        @case('close')
            <path d="m6 6 12 12M18 6 6 18" />
            @break
        @case('sparkle')
            <path d="m12 2 1.9 6.1L20 10l-6.1 1.9L12 18l-1.9-6.1L4 10l6.1-1.9L12 2Z" />
            <path d="m19 16 .7 2.3L22 19l-2.3.7L19 22l-.7-2.3L16 19l2.3-.7L19 16Z" />
            @break
        @case('trending')
            <path d="M3 17 9 11l4 4 8-8" />
            <path d="M15 7h6v6" />
            @break
        @case('flame')
            <path d="M12 2.5c1.5 4 5.5 5.5 5.5 10a5.5 5.5 0 0 1-11 0c0-2.2 1-3.8 2.2-5.2.6 1.7 1.3 2.7 2.4 3.4.1-3.1.3-5.7.9-8.2Z" />
            @break
        @case('refresh')
            <path d="M4.5 10V5.5H9" />
            <path d="M4.5 5.5A8.5 8.5 0 0 1 20 11" />
            <path d="M19.5 14v4.5H15" />
            <path d="M19.5 18.5A8.5 8.5 0 0 1 4 13" />
            @break
        @case('activity')
            <path d="M3 12h4l2.5-6 5 12L17 12h4" />
            @break
        @case('user')
            <circle cx="12" cy="8" r="3.5" />
            <path d="M5 20a7 7 0 0 1 14 0" />
            @break
        @case('scale')
            <rect x="3.5" y="7" width="17" height="12" rx="2" />
            <path d="M8 7V5h8v2M12 11v3" />
            <circle cx="12" cy="15" r="1.2" />
            @break
        @case('ruler')
            <rect x="3.5" y="8.5" width="17" height="7" rx="1.5" />
            <path d="M7 8.5v3M10 8.5v2M13 8.5v3M16 8.5v2M19 8.5v3" />
            @break
        @case('heart')
            <path d="M20.8 5.8a5.2 5.2 0 0 0-7.4 0L12 7.2l-1.4-1.4a5.2 5.2 0 1 0-7.4 7.4L12 21l8.8-7.8a5.2 5.2 0 0 0 0-7.4Z" />
            @break
        @case('alert')
            <path d="M12 3 2.8 19h18.4L12 3Z" />
            <path d="M12 9v4" />
            <circle cx="12" cy="16.5" r="0.9" fill="currentColor" stroke="none" />
            @break
        @case('eye')
            <path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z" />
            <circle cx="12" cy="12" r="2.8" />
            @break
        @case('lock')
            <rect x="5" y="10" width="14" height="10" rx="2" />
            <path d="M8 10V7a4 4 0 0 1 8 0v3" />
            @break
        @case('code')
            <path d="m8 8-4 4 4 4M16 8l4 4-4 4M14 5l-4 14" />
            @break
        @case('server')
            <rect x="3.5" y="4" width="17" height="6" rx="1.5" />
            <rect x="3.5" y="14" width="17" height="6" rx="1.5" />
            <path d="M7 7h.01M7 17h.01" />
            @break
        @case('gauge')
            <path d="M4 14a8 8 0 1 1 16 0" />
            <path d="m12 14 3.5-3.5" />
            <circle cx="12" cy="14" r="1.2" fill="currentColor" stroke="none" />
            @break
        @case('calendar')
            <rect x="3.5" y="5" width="17" height="15" rx="2" />
            <path d="M8 3v4M16 3v4M3.5 10h17" />
            @break
        @case('repeat')
            <path d="m17 2 3 3-3 3" />
            <path d="M4 11V9a4 4 0 0 1 4-4h12" />
            <path d="m7 22-3-3 3-3" />
            <path d="M20 13v2a4 4 0 0 1-4 4H4" />
            @break
        @case('moon')
            <path d="M20 14.5A8.5 8.5 0 0 1 9.5 4 8.5 8.5 0 1 0 20 14.5Z" />
            @break
        @case('play')
            <path d="m9 6 9 6-9 6V6Z" />
            @break
        @default
            <circle cx="12" cy="12" r="8.5" />
            <path d="M12 8v4l2.5 2" />
    @endswitch
</svg>
