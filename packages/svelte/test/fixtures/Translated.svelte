<script lang="ts">
    /* A page in Danish: the root hands its messages to every Sigta component below. */
    import { untrack } from 'svelte';
    import { setMessages } from '../../src/lib/runtime/messages.js';
    import MoneyInput from '../../src/lib/components/MoneyInput.svelte';
    import AssigneePicker from '../../src/lib/components/AssigneePicker.svelte';

    let { messages = {} }: { messages?: Record<string, string> } = $props();
    // Context is set once, at init: this fixture's messages never change.
    setMessages(untrack(() => messages));
</script>

<MoneyInput label="Pris" />
<AssigneePicker showName members={[{ id: 1, name: 'Ada Lovelace' }]} />
