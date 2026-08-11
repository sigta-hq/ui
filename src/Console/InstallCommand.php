<?php

namespace Sigta\Ui\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

/**
 * sigta:install — the one command every consumer app runs.
 *
 * Idempotent by design: re-running is always safe and brings the consumer
 * project fully in sync with the installed package version. It mirrors fonts
 * (copy new, overwrite changed, delete stale), force-writes the LLM context
 * files (these are package-owned), and ensures AGENTS.md carries a single
 * reference line to them. Designed to run both manually and as a composer
 * post-autoload-dump hook, so every composer install/update re-syncs.
 */
class InstallCommand extends Command
{
    protected $signature = 'sigta:install
                            {--only= : Run a single phase (fonts|agents)}
                            {--force : Deprecated — the command is already idempotent}';

    protected $description = 'Publish & sync Sigta UI: fonts + LLM agent context. Safe to re-run.';

    private const PHASE_FONTS = 'fonts';
    private const PHASE_AGENTS = 'agents';

    /** Marker embedded in the injected AGENTS.md line (used to detect prior injection). */
    private const AGENTS_MARKER = 'sigta-ui-context';

    /** The single line injected into the consumer's AGENTS.md, if absent. */
    private const AGENTS_LINE = '- [Sigta UI](./sigta-ui.md) — this project uses the Sigta UI component library. Read `sigta-ui.md` (rules) and `sigta-components.md` (API reference) before writing Blade. Always prefer `<sigta:…>` components over raw HTML. <!-- sigta-ui-context -->';

    public function handle(Filesystem $files): int
    {
        $only = $this->option('only');

        $this->info('Sigta UI — install & sync');
        $this->newLine();

        $run = static function (string $phase) use ($only): bool {
            return $only === null || $only === $phase;
        };

        $firstRun = ! $files->isDirectory(public_path('vendor/sigta/fonts'))
            || ! $files->exists(base_path('sigta-ui.md'));

        if ($run(self::PHASE_FONTS)) {
            $this->publishFonts($files);
        }

        if ($run(self::PHASE_AGENTS)) {
            $this->publishAgentContext($files);
        }

        $this->newLine();
        $this->info('Done.');

        if ($firstRun) {
            $this->nextSteps();
        }

        return self::SUCCESS;
    }

    /**
     * Mirror resources/fonts → public/vendor/sigta/fonts: copy every source
     * file (overwriting), then delete target files that no longer exist in the
     * source. A true mirror, not the copy-or-skip that vendor:publish gives.
     */
    protected function publishFonts(Filesystem $files): void
    {
        $this->components->info('Fonts');

        $source = __DIR__.'/../../resources/fonts';
        $target = public_path('vendor/sigta/fonts');

        if (! $files->isDirectory($source)) {
            $this->components->warn("Font source not found at {$source} — skipping.");

            return;
        }

        if (! $files->exists($target)) {
            $files->makeDirectory($target, 0755, true);
        }

        $sourceFiles = [];
        $copied = 0;
        foreach (Finder::create()->files()->in($source) as $file) {
            $relative = $file->getRelativePathname();
            $sourceFiles[$relative] = true;
            $files->copy($file->getRealPath(), $target.'/'.$relative);
            $copied++;
        }

        // Prune: remove target files not present in the source.
        $pruned = 0;
        foreach (Finder::create()->files()->in($target) as $file) {
            $relative = $file->getRelativePathname();
            if (! isset($sourceFiles[$relative])) {
                $files->delete($file->getRealPath());
                $pruned++;
            }
        }

        $this->components->twoColumnDetail('Mirrored', "{$copied} file(s) → public/vendor/sigta/fonts");
        if ($pruned > 0) {
            $this->components->twoColumnDetail('Pruned (stale)', "{$pruned} file(s)");
        }
    }

    /**
     * Force-write sigta-ui.md and sigta-components.md from the package stubs,
     * then ensure AGENTS.md references them. The .md files are package-owned
     * and always overwritten — never hand-edit them in a consumer project.
     */
    protected function publishAgentContext(Filesystem $files): void
    {
        $this->components->info('LLM agent context');

        $stubs = __DIR__.'/../../stubs';
        $map = [
            'sigta-ui.md' => base_path('sigta-ui.md'),
            'sigta-components.md' => base_path('sigta-components.md'),
        ];

        foreach ($map as $stub => $dest) {
            $source = $stubs.'/'.$stub;
            if (! $files->exists($source)) {
                $this->components->warn("Stub not found: {$stub} — skipping.");

                continue;
            }
            $files->put($dest, $files->get($source));
            $this->components->twoColumnDetail('Wrote', $stub);
        }

        $this->ensureAgentsReference($files);
    }

    /**
     * Guarantee AGENTS.md at the project root contains the marker line, once.
     * Creates the file if absent; appends if it exists but lacks the marker;
     * leaves an existing AGENTS.md otherwise untouched.
     */
    protected function ensureAgentsReference(Filesystem $files): void
    {
        $path = base_path('AGENTS.md');

        if ($files->exists($path) && str_contains($files->get($path), self::AGENTS_MARKER)) {
            $this->components->twoColumnDetail('AGENTS.md', 'reference present');

            return;
        }

        if (! $files->exists($path)) {
            // New file: a short header plus the reference line.
            $content = "# Project instructions\n\n".self::AGENTS_LINE."\n";
            $files->put($path, $content);
            $this->components->twoColumnDetail('AGENTS.md', 'created (with Sigta reference)');

            return;
        }

        // Existing file without the marker: append a blank line + the reference.
        $existing = rtrim($files->get($path))."\n\n".self::AGENTS_LINE."\n";
        $files->put($path, $existing);
        $this->components->twoColumnDetail('AGENTS.md', 'added Sigta reference');
    }

    /**
     * First-run guidance: the things sigta:install can't do for you, because
     * they touch the consumer app's CSS and layout, which the package owns
     * no part of.
     */
    protected function nextSteps(): void
    {
        $this->newLine();
        $this->components->info('Next steps — one-time wiring');
        $this->line('  1. Add to your CSS entry (e.g. resources/css/app.css):');
        $this->line('     <fg=gray>@import "tailwindcss";</>');
        $this->line('     <fg=gray>@import "../../vendor/sigta/ui/resources/css/sigta.css";</>');
        $this->line('     <fg=gray>@source "../../vendor/sigta/ui/resources/views";</>');
        $this->newLine();
        $this->line('  2. Add <fg=cyan>@sigtaScripts</> to your layout <head> (after Livewire styles).');
        $this->newLine();
        $this->line('  3. <fg=cyan>npm run build</> to compile your CSS.');
        $this->newLine();
        $this->line('  To auto-sync on every <options=bold>composer update</>, add to composer.json:');
        $this->line('     <fg=gray>"post-autoload-dump": ["@php artisan sigta:install --ansi"]</>');
    }
}
