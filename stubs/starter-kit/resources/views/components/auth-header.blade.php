@props([
    'title',
    'description',
])

<div class="flex w-full flex-col gap-2 text-center">
    <sigta:heading level="1" size="lg">{{ $title }}</sigta:heading>
    <sigta:text variant="muted">{{ $description }}</sigta:text>
</div>
