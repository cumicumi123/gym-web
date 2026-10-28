@props(['name', 'options', 'selected' => null, 'legend' => null])

<div>
    @if ($legend)
        <div class="field-label">{{ $legend }}</div>
    @endif

    <div @class(['choice-group', 'is-invalid' => $errors->has($name)]) role="radiogroup" aria-label="{{ $legend ?? $name }}">
        @foreach ($options as $value => $option)
            <div class="choice">
                <input
                    type="radio"
                    id="{{ $name }}-{{ $value ?: 'auto' }}"
                    name="{{ $name }}"
                    value="{{ $value }}"
                    class="sr-only"
                    @checked((string) old($name, $selected) === (string) $value)
                >
                <label for="{{ $name }}-{{ $value ?: 'auto' }}">
                    @if (! empty($option['icon']))
                        <x-icon :name="$option['icon']" class="h-3.5 w-3.5 text-accent-soft" />
                    @endif
                    <span>{{ $option['label'] }}</span>
                </label>
            </div>
        @endforeach
    </div>

    @error($name)
        <p class="field-error">{{ $message }}</p>
    @enderror
</div>
