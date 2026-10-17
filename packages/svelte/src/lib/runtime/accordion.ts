/* Accordion — the group's name reaches its items through context, so every
   <details> shares it and the browser keeps one open (Blade: @aware). */
import { getContext, setContext } from 'svelte';

export interface AccordionContext {
    readonly name: string | null;
}

const KEY = Symbol('sigta-accordion');

export const setAccordion = (group: AccordionContext): AccordionContext => setContext(KEY, group);
export const useAccordion = (): AccordionContext | undefined => getContext(KEY);
