<?php

use Kirby\Cms\Page;
use tobimori\DreamForm\DreamForm;

function dfdbFormsPage(): ?Page
{
    return DreamForm::findPageOrDraftRecursive(
        DreamForm::option('page', 'page://forms'),
    );
}

function dfdbDiscoverForms(): array
{
    $formsPage = dfdbFormsPage();

    if (!$formsPage) {
        return [];
    }

    $result = [];
    $forms = $formsPage
        ->childrenAndDrafts()
        ->filterBy('intendedTemplate', 'form')
        ->sortBy('title', 'asc');

    foreach ($forms as $form) {
        $submissions = dfdbSubmissionPages($form);
        $lastSubmission = $submissions->first();

        $result[] = [
            'slug' => $form->slug(),
            'title' => $form->title()->value(),
            'count' => $submissions->count(),
            'last' => $lastSubmission ? dfdbSubmissionDate($lastSubmission) : null,
        ];
    }

    return $result;
}

function dfdbFormBySlug(string $slug): ?Page
{
    $formsPage = dfdbFormsPage();

    if (!$formsPage) {
        return null;
    }

    return $formsPage
        ->childrenAndDrafts()
        ->filterBy('intendedTemplate', 'form')
        ->findBy('slug', $slug);
}

function dfdbSubmissionPages(Page $form)
{
    return $form
        ->childrenAndDrafts()
        ->filterBy('intendedTemplate', 'submission')
        ->sortBy(fn (Page $submission) => dfdbSubmissionDate($submission), 'desc');
}

function dfdbSubmissionDate(Page $submission): string
{
    return $submission->content()->get('dreamform-submitted')->value() ?: $submission->modified('c');
}

function dfdbSubmissionPayload(Page $submission): array
{
    $data = [];

    foreach ($submission->content()->toArray() as $key => $value) {
        $normalizedKey = strtolower((string) $key);

        if ($normalizedKey === 'uuid' || str_starts_with($normalizedKey, 'dreamform-')) {
            continue;
        }

        $data[$normalizedKey] = $value;
    }

    return $data;
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
                    $forms = $discovered;

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
                    $form = dfdbFormBySlug($formSlug);

                    if (!$form) {
                        return [
                            'component' => 'k-dreamform-db-form',
                            'props' => [
                                'formSlug' => $formSlug,
                                'formTitle' => $formSlug,
                                'resourceKey' => '',
                                'submissions' => [],
                                'columns' => [],
                                'pagination' => ['page' => 1, 'total' => 0, 'limit' => 25, 'pages' => 1],
                            ],
                        ];
                    }

                    $page = max(1, (int) $kirby->request()->get('page', 1));
                    $limit = 25;
                    $offset = ($page - 1) * $limit;
                    $allSubmissions = dfdbSubmissionPages($form);
                    $total = $allSubmissions->count();
                    $rows = $allSubmissions->slice($offset, $limit);

                    $allKeys = [];
                    $submissions = [];

                    foreach ($rows as $submissionPage) {
                        $data = dfdbSubmissionPayload($submissionPage);

                        foreach (array_keys($data) as $key) {
                            if (!in_array($key, $allKeys)) {
                                $allKeys[] = $key;
                            }
                        }

                        $submissions[] = [
                            'id' => $submissionPage->id(),
                            'data' => $data,
                            'submittedAt' => dfdbSubmissionDate($submissionPage),
                            'referer' => $submissionPage->content()->get('dreamform-referer')->value(),
                        ];
                    }

                    return [
                        'component' => 'k-dreamform-db-form',
                        'props' => [
                            'formSlug' => $formSlug,
                            'formTitle' => $form->title()->value(),
                            'resourceKey' => $formSlug,
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
             * Pattern: dreamform-db/{formSlug}/{submissionId}
             */
            'dreamform-db/(:any)/(:all)' => [
                'load' => function (string $formSlug, string $submissionId) {
                    $submission = page($submissionId);

                    if (!$submission || $submission->intendedTemplate()->name() !== 'submission') {
                        throw new Exception('Eintrag nicht gefunden');
                    }

                    $data = dfdbSubmissionPayload($submission);
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
                        'text' => dfdbSubmissionDate($submission),
                    ];

                    $referer = $submission->content()->get('dreamform-referer')->value();

                    if ($referer) {
                        $fields['_referer'] = [
                            'label' => 'Seite',
                            'type' => 'info',
                            'text' => $referer,
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
             * Pattern: dreamform-db/{formSlug}/{submissionId}/delete
             */
            'dreamform-db/(:any)/(:all)/delete' => [
                'load' => function (string $formSlug, string $submissionId) {
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
                'submit' => function (string $formSlug, string $submissionId) {
                    $submission = page($submissionId);

                    if (!$submission || $submission->intendedTemplate()->name() !== 'submission') {
                        throw new Exception('Eintrag nicht gefunden');
                    }

                    $submission->delete();

                    return [
                        'message' => 'Eintrag gelöscht',
                    ];
                },
            ],
        ],
    ];
};
