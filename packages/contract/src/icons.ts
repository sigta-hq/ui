/* The pinned Lucide set, as inner SVG markup keyed by icon name. Both
   runtimes render from it, so an icon can never differ between them. */
import data from '../icons/lucide.json' with { type: 'json' };

export const iconSource: string = data.source;
export const icons: Record<string, string> = data.icons;

export function iconMarkup(name: string): string | null {
    return icons[name] ?? null;
}
