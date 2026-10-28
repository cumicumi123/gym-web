@props(['name', 'options', 'selected' => null, 'legend' => null])

<div>
    @if ($legend)
        <div class="field-label">{{ $legend }}</div>
    @endif

    <div class="segmented" role="radiogroup" aria-label="{{ $legend ?? $name }}">
        @foreach ($options as $value => $option)
            <div>
                <input
                    type="radio"
                    id="{{ $name }}-{{ $value }}"
                    name="{{ $name }}"
                    value="{{ $value }}"
                    class="sr-only"
                    @checked((string) old($name, $selected) === (string) $value)
                >
                <label for="{{ $name }}-{{ $value }}">
                    <span class="flex items-center gap-2">
                        <x-icon :name="$option['icon'] ?? 'target'" class="h-4 w-4 text-accent-soft" />
                        <strong>{{ $option['label'] }}</strong>
                    </span>
                    <small>{{ $option['hint'] ?? '' }}</small>
                </label>
            </div>
        @endforeach
    </div>

    @error($name)
        <p class="field-error">{{ $message }}</p>
    @enderror
</div>
