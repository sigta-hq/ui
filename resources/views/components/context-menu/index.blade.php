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
    {{ $attributes }}
    data-sigta="context-menu"
>
    {{ $slot }}

    {{-- The static position is where Alpine's binding starts (0, 0), so the server
         render and the Svelte twin agree before the menu is ever opened. --}}
    <div
        x-cloak
        x-show="open"
        x-on:click.outside="close()"
        x-on:keydown.down.prevent="move(1)"
        x-on:keydown.up.prevent="move(-1)"
        x-on:keydown.home.prevent="focusItem(0)"
        x-on:keydown.end.prevent="focusItem(-1)"
        style="left:0px;top:0px"
        x-bind:style="`left:${x}px;top:${y}px`"
        role="menu"
        data-scope="context-menu"
        data-part="menu"
    >
        {{ $menu ?? '' }}
    </div>
</div>
