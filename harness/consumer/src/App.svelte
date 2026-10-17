<!-- A consumer app: everything below comes from the installed @sigta-hq packages, nothing from the monorepo sources. -->
<script>
    import { Autocomplete, Badge, Button, Card, Checkbox, Dropdown, Field, Heading, Icon, Input, Kanban, Modal, Section, Text, Toast, sigta } from '@sigta-hq/svelte';

    let name = $state('');
    let owner = $state(null);
    let agreed = $state(false);
    let moves = $state(0);
</script>

<main class="mx-auto max-w-3xl p-8 flex flex-col gap-6">
    <Heading level={1}>Consumer smoke</Heading>
    <Text variant="muted">Installed from the packed tarballs, like a real app.</Text>

    <div class="flex gap-3 items-center">
        <Button variant="primary" onclick={() => sigta.toast('Saved', { variant: 'success' })}><Icon name="check" size="sm" /> Toast</Button>
        <Modal.Trigger name="hello"><Button>Open modal</Button></Modal.Trigger>
        <Badge>v0.1</Badge>
        <Dropdown>
            {#snippet trigger()}<Button variant="ghost">Menu</Button>{/snippet}
            <Dropdown.Item icon="settings">Settings</Dropdown.Item>
        </Dropdown>
    </div>

    <Card>
        <Field label="Name"><Input bind:value={name} placeholder="Your name" /></Field>
        <Autocomplete label="Owner" bind:value={owner} placeholder="Search…">
            <Autocomplete.Option value="ada">Ada Lovelace</Autocomplete.Option>
            <Autocomplete.Option value="grace">Grace Hopper</Autocomplete.Option>
        </Autocomplete>
        <Checkbox label="I agree" bind:checked={agreed} />
        <p data-testid="state">{name}|{owner}|{agreed}</p>
    </Card>

    <Kanban onmove={() => moves++}>
        <Kanban.Column id="todo" label="To do">
            <Kanban.Card id="a">Write the brief</Kanban.Card>
        </Kanban.Column>
        <Kanban.Column id="done" label="Done"></Kanban.Column>
    </Kanban>

    <Section.Faq />
</main>

<Modal name="hello" heading="Hello">Modal body</Modal>
<Toast />
