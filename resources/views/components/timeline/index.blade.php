{{-- Vertical timeline: hairline rail on the left, ink dot per entry.
     Usage:
       <x-sigta::timeline>
           <x-sigta::timeline.item heading="Shipped v2" meta="2026-02-14">Body copy.</x-sigta::timeline.item>
       </x-sigta::timeline>
--}}
<ol data-sigta="timeline" {{ $attributes }}>
    {{ $slot }}
</ol>
