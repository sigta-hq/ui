<x-kit-shell title="Inbox">
    <x-sigta::text variant="muted" class="max-w-[62ch]">Finishing something should feel finished. Tick an item: the box draws its tick, a burst flicks out, the title is crossed through, and the row slides aside while the list closes up. The count drops with a bump, a toast offers Undo, and the last one out gets confetti. This is a real Livewire component, so every step round-trips the server.</x-sigta::text>

    <div class="max-w-2xl">
        <livewire:kit.inbox />
    </div>

    <x-kit-demo label="The whole recipe">
        <pre class="overflow-x-auto rounded-panel bg-inverse p-4 font-mono text-[0.76rem] leading-relaxed text-inverse-fg"><code>&lt;ul data-sigta-motion id="inbox"&gt;          &lt;!-- rows fold out / unfold in --&gt;
    &#64;foreach ($items as $item)
        &lt;li wire:key="item-&#123;&#123; $item->id &#125;&#125;"&gt;
            &lt;sigta:checkbox x-on:change="$sigta.complete($el, () => $wire.complete(&#123;&#123; $item->id &#125;&#125;))" /&gt;
            &lt;span data-sigta-strike&gt;&#123;&#123; $item->title &#125;&#125;&lt;/span&gt;  &lt;!-- crossed out when done --&gt;
        &lt;/li&gt;
    &#64;endforeach
&lt;/ul&gt;
&lt;sigta:count :value="$items->count()" /&gt; open

// in the component
Sigta::toast('Marked done', variant: 'success', action: ['text' => 'Undo', 'event' => 'item-reopen', 'detail' => ['id' => $id]]);
Sigta::celebrate('big', text: 'inbox zero!', target: '#inbox');</code></pre>
    </x-kit-demo>

    <x-sigta::toast />
</x-kit-shell>
