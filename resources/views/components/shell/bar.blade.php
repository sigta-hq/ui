{{-- Shell top bar (brand .app-bar): heading + mono sub on the left,
     `actions` slot (segmented, pills, buttons) on the right. --}}
@props([
    'heading' => null,
    'sub' => null,
])

<header data-sigta="shell.bar" {{ $attributes }}>
    <div data-scope="shell.bar" data-part="start">
        @if ($heading)
            <h3 data-scope="shell.bar" data-part="heading">{{ $heading }}</h3>
        @endif
        @if ($sub)
            <span data-scope="shell.bar" data-part="sub">{{ $sub }}</span>
        @endif
    </div>

    @isset($actions)
        <div data-scope="shell.bar" data-part="actions">{{ $actions }}</div>
    @endisset
</header>
