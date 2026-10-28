@props(['question', 'answer', 'open' => false])

<details class="faq-item" @if ($open) open @endif>
    <summary>{{ $question }}</summary>
    <p>{{ $answer }}</p>
</details>
