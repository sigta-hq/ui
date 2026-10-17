/* ---------------------------------------------------------------------------
   What a command palette shares with its items: closing it once an item
   ran (the Livewire runtime's items call the palette's hide()).
--------------------------------------------------------------------------- */
import { getContext, setContext } from 'svelte';

export interface CommandPalette {
    hide(): void;
}

const KEY = Symbol('sigta-command');

export const providePalette = (palette: CommandPalette): CommandPalette => setContext(KEY, palette);

export const usePalette = (): CommandPalette | undefined => getContext(KEY);
