<?php

use Kirby\Database\Db;
use tobimori\DreamForm\DreamForm;

/**
 * Reads a form page's actions and returns the table name
 * configured in the database-action block (or null if none).
 */
function dfdbTableForForm($formPage): string|null
{
    $actions = $formPage->content()->get('actions')->toBlocks();

    foreach ($actions as $block) {
        if ($block->type() === 'database-action') {
            $name = $block->tablename()->value();

            return $name ?: 'dreamform_submissions';
        }
    }

    return null;
}

/**
 * Returns all forms that have a database-action configured.
 * Each entry: [slug, title, table]
 */
function dfdbDiscoverForms(): array
{
    $formsPage = DreamForm::findPageOrDraftRecursive(
        DreamForm::option('page', 'forms'),
    );

    if (!$formsPage) {
        return [];
    }

    $result = [];

    foreach ($formsPage->children()->listed() as $form) {
        $table = dfdbTableForForm($form);
        if ($table !== null) {
            $result[] = [
                'slug' => $form->slug(),
                'title' => $form->title()->value(),
                'table' => $table,
            ];
        }
    }

    // Also check drafts
    foreach ($formsPage->drafts() as $form) {
        $table = dfdbTableForForm($form);
        if ($table !== null) {
            $result[] = [
                'slug' => $form->slug(),
                'title' => $form->title()->value(),
                'table' => $table,
            ];
        }
    }

    return $result;
}

/**
 * Find a form's table name by slug.
 */
function dfdbTableForSlug(string $slug): string|null
{
    foreach (dfdbDiscoverForms() as $form) {
        if ($form['slug'] === $slug) {
            return $form['table'];
        }
    }

    return null;
}

return function ($kirby) {
    return [
        'label' => 'Formular-Eingänge',
        'icon' => 'email',
        'menu' => true,
        'link' => 'formular-eingaenge',
        'views' => [
            /**
             * Overview: list all forms with submission counts
             */
            [
                'pattern' => 'formular-eingaenge',
                'action' => function () {
                    $discovered = dfdbDiscoverForms();
                    $forms = [];

                    foreach ($discovered as $entry) {
                        try {
                            $count = (int) Db::count($entry['table'], ['form_slug' => $entry['slug']]);
                            $lastRow = Db::first($entry['table'], 'submitted_at', ['form_slug' => $entry['slug']], 'submitted_at DESC');
                            $last = $lastRow ? $lastRow->submitted_at() : null;
                        } catch (Throwable) {
                            $count = 0;
                            $last = null;
                        }

                        $forms[] = [
                            'slug' => $entry['slug'],
                            'title' => $entry['title'],
                            'table' => $entry['table'],
                            'count' => $count,
                            'last' => $last,
                        ];
                    }

                    // Sort by last submission descending
                    usort($forms, fn ($a, $b) => ($b['last'] ?? '') <=> ($a['last'] ?? ''));

                    return [
                        'component' => 'k-dreamform-db-overview',
                        'props' => [
                            'forms' => $forms,
                        ],
                    ];
                },
            ],

            /**
             * Form detail: paginated table of submissions
             */
            [
                'pattern' => 'formular-eingaenge/(:any)',
                'action' => function (string $formSlug) use ($kirby) {
                    $table = dfdbTableForSlug($formSlug);

                    if (!$table) {
                        return [
                            'component' => 'k-dreamform-db-form',
                            'props' => [
                                'formSlug' => $formSlug,
                                'formTitle' => $formSlug,
                                'tableName' => '',
                                'submissions' => [],
                                'columns' => [],
                                'pagination' => ['page' => 1, 'total' => 0, 'limit' => 25, 'pages' => 1],
                            ],
                        ];
                    }

                    $page = max(1, (int) $kirby->request()->get('page', 1));
                    $limit = 25;
                    $offset = ($page - 1) * $limit;

                    // Form title from the discovered forms
                    $formTitle = $formSlug;
                    foreach (dfdbDiscoverForms() as $f) {
                        if ($f['slug'] === $formSlug) {
                            $formTitle = $f['title'];

                            break;
                        }
                    }

                    // Total count
                    $total = (int) Db::count($table, ['form_slug' => $formSlug]);

                    // Paginated rows
                    $rows = Db::select($table, '*', ['form_slug' => $formSlug], 'submitted_at DESC', $offset, $limit);

                    // Build submissions array and discover columns
                    $allKeys = [];
                    $submissions = [];

                    foreach ($rows as $row) {
                        $data = json_decode($row->data(), true) ?? [];
                        foreach (array_keys($data) as $key) {
                            if (!in_array($key, $allKeys)) {
                                $allKeys[] = $key;
                            }
                        }
                        $submissions[] = [
                            'id' => (int) $row->id(),
                            'data' => $data,
                            'submittedAt' => $row->submitted_at(),
                            'referer' => $row->referer(),
                        ];
                    }

                    return [
                        'component' => 'k-dreamform-db-form',
                        'props' => [
                            'formSlug' => $formSlug,
                            'formTitle' => $formTitle,
                            'tableName' => $table,
                            'submissions' => $submissions,
                            'columns' => $allKeys,
                            'pagination' => [
                                'page' => $page,
                                'total' => $total,
                                'limit' => $limit,
                                'pages' => max(1, (int) ceil($total / $limit)),
                            ],
                        ],
                    ];
                },
            ],
        ],

        'dialogs' => [
            /**
             * View submission detail
             * Pattern: dreamform-db/{table}/{id}
             */
            'dreamform-db/(:any)/(:num)' => [
                'load' => function (string $table, int $id) {
                    $row = Db::first($table, '*', ['id' => $id]);

                    if (!$row) {
                        throw new Exception('Eintrag nicht gefunden');
                    }

                    $data = json_decode($row->data(), true) ?? [];
                    $fields = [];

                    foreach ($data as $key => $value) {
                        $displayValue = is_array($value) ? implode(', ', $value) : (string) $value;
                        $fields[$key] = [
                            'label' => ucfirst(str_replace(['-', '_'], ' ', $key)),
                            'type' => 'info',
                            'text' => $displayValue ?: '(leer)',
                        ];
                    }

                    $fields['_divider'] = [
                        'type' => 'line',
                    ];

                    $fields['_submitted_at'] = [
                        'label' => 'Eingegangen am',
                        'type' => 'info',
                        'text' => $row->submitted_at(),
                    ];

                    if ($row->referer()) {
                        $fields['_referer'] = [
                            'label' => 'Seite',
                            'type' => 'info',
                            'text' => $row->referer(),
                        ];
                    }

                    return [
                        'component' => 'k-form-dialog',
                        'props' => [
                            'fields' => $fields,
                            'submitButton' => false,
                        ],
                    ];
                },
            ],

            /**
             * Delete submission confirmation
             * Pattern: dreamform-db/{table}/{id}/delete
             */
            'dreamform-db/(:any)/(:num)/delete' => [
                'load' => function (string $table, int $id) {
                    return [
                        'component' => 'k-text-dialog',
                        'props' => [
                            'text' => 'Soll dieser Eintrag wirklich gelöscht werden? Diese Aktion kann nicht rückgängig gemacht werden.',
                            'submitButton' => [
                                'text' => 'Löschen',
                                'icon' => 'trash',
                                'theme' => 'negative',
                            ],
                        ],
                    ];
                },
                'submit' => function (string $table, int $id) {
                    Db::delete($table, ['id' => $id]);

                    return [
                        'message' => 'Eintrag gelöscht',
                    ];
                },
            ],
        ],
    ];
};
