/* Sections — what the Blade sections test with @if ($x) / ! empty($x): PHP's
   truthiness, so a section shows or skips a piece for the same values in
   both runtimes (null, false, '', '0', 0 and an empty list are nothing). */
export function truthy(value: unknown): boolean {
    if (value === null || value === undefined || value === false || value === '' || value === '0' || value === 0) return false;
    return !(Array.isArray(value) && value.length === 0);
}
