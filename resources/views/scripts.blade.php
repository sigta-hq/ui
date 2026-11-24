{{-- The runtime is served as is, so the words it writes by itself are translated here
     and read from this tag. Keep the keys in step with trans() calls in resources/js/sigta.js. --}}
<script src="{{ $url }}" data-sigta-lang="{{ json_encode([
    'Done' => __('Done'),
    'nice!' => __('nice!'),
    'well done!' => __('well done!'),
]) }}" defer></script>
