/* ---------------------------------------------------------------------------
   Picked files as the file upload lists them. Pure and SSR-safe.
--------------------------------------------------------------------------- */

const UNITS = ['B', 'KB', 'MB', 'GB', 'TB'];

/** A byte count in binary units: "512 B", "1.5 KB", "12 MB". */
export function formatBytes(bytes: number): string {
    let n = bytes;
    let i = 0;
    while (n >= 1024 && i < UNITS.length - 1) {
        n /= 1024;
        i++;
    }
    return (i === 0 ? n : n.toFixed(n >= 10 ? 0 : 1)) + ' ' + UNITS[i];
}

export interface FileEntry {
    name: string;
    size: string;
}

/** The name and formatted size of each file, in order. */
export function fileEntries(files: ArrayLike<{ name: string; size: number }> | Iterable<{ name: string; size: number }> | null | undefined): FileEntry[] {
    return Array.from(files ?? []).map((file) => ({ name: file.name, size: formatBytes(file.size) }));
}
