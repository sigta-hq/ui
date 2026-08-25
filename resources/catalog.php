<?php

/**
 * Sigta UI — component catalog / tier classification.
 *
 * A watered-down atomic-design taxonomy. Tags stay flat (<sigta:button>,
 * <sigta:chart.line>) regardless of tier — this file is the organizational
 * layer that groups the kit and docs, and documents intent.
 *
 *   elements   — one indivisible block (button, input, text)
 *   components — elements composed into a functional unit (chart, card, modal)
 *   layout     — structural scaffolds used to assemble sections (shell, panel)
 *   sections   — full, content-shaped page regions (hero, pricing, FAQ)
 *   pages      — sections composed into a whole (landing, dashboard)
 *
 * Values are the kit-page slugs (the navigable unit). A page may showcase a
 * small family (e.g. `typography` covers heading/text/mark/…; `chart` covers
 * sparkline/line/bars).
 */
return [
    'docs' => [
        'label' => 'Docs',
        'description' => 'Installation, theming, and usage guidance.',
        'items' => [
            'installation', // production install: private VCS repo, auth, fonts, deploy
        ],
    ],

    'elements' => [
        'label' => 'Elements',
        'description' => 'The indivisible blocks. One tag, no meaningful internal parts.',
        'items' => [
            'typography', // heading, text, mark, annot, eyebrow, rule, separator, brand
            'button',
            'badge',      // + pill
            'avatar',
            'icon',
            'input',
            'textarea',
            'checkbox',
            'radio',
            'switch',
            'progress',
            'slider',
            'skeleton',
            'elapsed-clock', // + duration display
        ],
    ],

    'components' => [
        'label' => 'Components',
        'description' => 'Elements wired into a functional unit — internal parts, slots, or state.',
        'items' => [
            'field',
            'select',
            'autocomplete',
            'listbox',
            'pillbox',
            'otp',
            'file-upload',
            'segmented',
            'card',
            'callout',
            'stat',
            'money',          // + money-input
            'profile',
            'assignee-picker',
            'tooltip',
            'dropdown',
            'popover',
            'context-menu',
            'modal',
            'toast',
            'tabs',
            'accordion',
            'breadcrumbs',
            'navbar',
            'pagination',
            'table',
            'timeline',
            'comment',
            'chart',
            'command',
            'calendar',
            'date-picker',
            'time-picker',
            'duration-input',
            'carousel',
            'kanban',
            'composer',
            'timer-bar',
            'editor',
        ],
    ],

    'layout' => [
        'label' => 'Layout',
        'description' => 'Structural scaffolds — the frames you assemble sections inside.',
        'items' => [
            'shell',
            'panel',
            'tile-grid',
            'section-header',
        ],
    ],

    'sections' => [
        'label' => 'Sections',
        'description' => 'Content-shaped page regions, built from components. Tag as <sigta:section.NAME>; each renders complete with default copy and takes prop/slot overrides.',
        'items' => [
            'hero',
            'features',
            'recognition',
            'pricing',
            'about',
            'faq',
            'quote',
            'proof',
            'cta',
            'footer',
        ],
    ],

    'pages' => [
        'label' => 'Pages',
        'description' => 'Whole compositions of sections.',
        'items' => [
            'landing',
            'dashboard',
            'chat',
        ],
    ],
];
