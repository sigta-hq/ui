{{-- Context menu: right-click any surface, get a dropdown-styled menu at the
     pointer. Default slot = the surface; named `menu` slot = the items
     (reuse <x-sigta::dropdown.item> — its close(true) scope-chains here).
     Usage:
       <x-sigta::context-menu>
           <div>Right-click me</div>
           <x-slot:menu>
               <x-sigta::dropdown.item icon="pencil">Rename</x-sigta::dropdown.item>
           </x-slot:menu>
       </x-sigta::context-menu> --}}

<div
    x-data="{
        open: false,
        x: 0,
        y: 0,
        openAt(event) {
            this.x = Math.max(8, Math.min(event.clientX, window.innerWidth - 208));
            this.y = Math.max(8, Math.min(event.clientY, window.innerHeight - 248));
            this.open = true;
            this.$nextTick(() => this.focusItem(0));
        },
        close(focus = false) {
            this.open = false;
        },
        items() {
            return Array.from(this.$el.querySelectorAll('[role=menuitem]:not([aria-disabled=true])'));
        },
        focusItem(index) {
            const items = this.items();
            if (items.length) items[((index % items.length) + items.length) % items.length].focus();
        },
        move(delta) {
            const current = this.items().indexOf(document.activeElement);
            this.focusItem(current === -1 ? (delta > 0 ? 0 : -1) : current + delta);
        },
    }"
    x-on:contextmenu.prevent="openAt($event)"
    x-on:keydown.escape.prevent.stop="close(true)"
    {{ $attributes->merge(['class' => 'relative']) }}
    data-sigta="context-menu"
>
    {{ $slot }}

    <div
        x-cloak
        x-show="open"
        x-on:click.outside="close()"
        x-on:keydown.down.prevent="move(1)"
        x-on:keydown.up.prevent="move(-1)"
        x-on:keydown.home.prevent="focusItem(0)"
        x-on:keydown.end.prevent="focusItem(-1)"
        x-bind:style="`left:${x}px;top:${y}px`"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 -translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0"
        role="menu"
        class="fixed z-50 min-w-48 bg-surface-raised border border-line rounded-panel shadow-paper py-1.5"
    >
        {{ $menu ?? '' }}
    </div>
</div>
