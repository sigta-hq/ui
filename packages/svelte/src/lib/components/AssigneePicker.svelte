<script lang="ts">
    /* Assignee picker — the assignee's avatar (or a dashed + circle) opens a
       searchable people list: Unassigned and every member. Choosing one
       calls onselect with its id (null for Unassigned). The panel sits on
       <body> with fixed positioning, or inside the picker with inline (for a
       native <dialog>'s top layer). */
    import type { HTMLButtonAttributes } from 'svelte/elements';
    import { onMount, tick } from 'svelte';
    import { Presence } from '../runtime/presence.svelte.js';
    import Avatar from './Avatar.svelte';
    import Icon from './Icon.svelte';

    interface Person {
        id: string | number;
        name: string;
    }

    interface Props extends Omit<HTMLButtonAttributes, 'onselect'> {
        user?: Person | null;
        members?: Person[];
        size?: 'xs' | 'sm' | 'base' | 'lg';
        showName?: boolean;
        label?: string | null;
        inline?: boolean;
        onselect?: (id: Person['id'] | null) => void;
    }

    let { user = null, members = [], size = 'xs', showName = false, label = null, inline = false, onselect, ...rest }: Props = $props();

    let open = $state(false);
    let q = $state('');
    let top = $state(0);
    let left = $state(0);
    let trigger: HTMLButtonElement | undefined = $state();
    let search: HTMLInputElement | undefined = $state();
    const fade = new Presence(() => open);

    // The panel goes to <body> once mounted (the Livewire render keeps it in
    // a teleport template until Alpine starts).
    let mounted = $state(false);
    onMount(() => { mounted = true; });

    const userName = $derived(user ? String(user.name ?? '') : null);
    const filtered = $derived.by(() => {
        const query = q.trim().toLowerCase();
        return query ? members.filter((m) => String(m.name).toLowerCase().includes(query)) : members;
    });

    async function toggle(event: MouseEvent) {
        event.stopPropagation();
        if (open) { open = false; return; }
        if (!inline && trigger) {
            const r = trigger.getBoundingClientRect();
            top = Math.round(r.bottom + 6);
            left = Math.round(Math.max(12, Math.min(r.left, window.innerWidth - 256 - 12)));
        }
        q = '';
        open = true;
        await tick();
        search?.focus();
    }

    function select(id: Person['id'] | null) {
        onselect?.(id);
        open = false;
    }

    const noDrag = (event: DragEvent) => { event.stopPropagation(); event.preventDefault(); };
    const outside = (event: MouseEvent) => {
        if (open && !fade.node?.contains(event.target as Node | null)) open = false;
    };
    const escape = (event: KeyboardEvent) => { if (event.key === 'Escape') open = false; };

    function portal(node: HTMLElement) {
        document.body.appendChild(node);
        return { destroy: () => node.remove() };
    }
</script>

<svelte:window onkeydown={escape} />
<svelte:document onclick={outside} />

{#snippet content()}
    {#if open}
        <div>
            <input bind:this={search} bind:value={q} type="text" placeholder="Search people" data-scope="assignee-picker" data-part="search" data-test="assignee-search" />
            <div data-scope="assignee-picker" data-part="list">
                <button type="button" onclick={() => select(null)} data-scope="assignee-picker" data-part="unassign">
                    <span aria-hidden="true" data-scope="assignee-picker" data-part="mark"><Icon name="x" size="xs" /></span>
                    Unassigned
                </button>
                {#each filtered as member (member.id)}
                    <button type="button" onclick={() => select(member.id)} data-scope="assignee-picker" data-part="option">
                        <Avatar name={member.name} size="xs" aria-hidden="true" />
                        <span data-scope="assignee-picker" data-part="label">{member.name}</span>
                    </button>
                {/each}
                <p hidden={filtered.length > 0} data-scope="assignee-picker" data-part="empty">No people found.</p>
            </div>
        </div>
    {/if}
{/snippet}

<div data-sigta="assignee-picker">
    <button
        type="button"
        bind:this={trigger}
        draggable="false"
        aria-label={label ?? 'Assignee'}
        {...rest}
        onclick={toggle}
        ondragstart={noDrag}
        data-scope="assignee-picker"
        data-part="trigger"
    >
        {#if user}
            <Avatar name={userName} {size} />
        {:else}
            <span aria-hidden="true" data-scope="assignee-picker" data-part="placeholder" data-size={size}>
                <Icon name="plus" size="xs" />
            </span>
        {/if}
        {#if showName}
            <span data-scope="assignee-picker" data-part="name">{userName ?? 'Assign'}</span>
        {/if}
    </button>

    {#if inline}
        <!-- svelte-ignore a11y_no_static_element_interactions -->
        <div
            bind:this={fade.node}
            hidden={!fade.shown}
            ondragstart={(event) => event.stopPropagation()}
            draggable="false"
            data-scope="assignee-picker"
            data-part="panel"
            data-inline=""
            data-test="assignee-dropdown"
        >{@render content()}</div>
    {:else if mounted}
        <!-- svelte-ignore a11y_no_static_element_interactions -->
        <div
            use:portal
            bind:this={fade.node}
            hidden={!fade.shown}
            ondragstart={(event) => event.stopPropagation()}
            draggable="false"
            style="position: fixed; top: {top}px; left: {left}px;"
            data-scope="assignee-picker"
            data-part="panel"
            data-test="assignee-dropdown"
        >{@render content()}</div>
    {/if}
</div>
