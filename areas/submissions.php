<?php

use GsMmh\WebPlugin\NewsletterRecipients;
use Kirby\Cms\Page;
use tobimori\DreamForm\DreamForm;

const DFDB_NEWSLETTER_FORM_SLUG = 'newsletter-anmeldung';

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
        $count = $submissions->count();
        $last = $lastSubmission ? dfdbSubmissionDate($lastSubmission) : null;

        if ($form->slug() === DFDB_NEWSLETTER_FORM_SLUG) {
            $recipients = NewsletterRecipients::all();
            $recipientLast = dfdbNewsletterLastDate($recipients);
            $count = max($count, count($recipients));
            $last = max($last ?? '', $recipientLast ?? '') ?: null;
        }

        $result[] = [
            'slug' => $form->slug(),
            'title' => $form->title()->value(),
            'count' => $count,
            'last' => $last,
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

function dfdbNewsletterLastDate(array $recipients): ?string
{
    $dates = array_filter(array_map(
        fn (array $recipient) => $recipient['created_at'] ?? null,
        $recipients
    ));

    rsort($dates);

    return $dates[0] ?? null;
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

                    if ($form->slug() === DFDB_NEWSLETTER_FORM_SLUG) {
                        $recipients = NewsletterRecipients::all();
                        $total = count($recipients);
                        $rows = array_slice($recipients, $offset, $limit);
                        $submissions = [];

                        foreach ($rows as $recipient) {
                            $submissions[] = [
                                'id' => 'newsletter-recipient-' . $recipient['id'],
                                'data' => [
                                    'first_name' => $recipient['first_name'],
                                    'last_name' => $recipient['last_name'],
                                    'email' => $recipient['email'],
                                ],
                                'submittedAt' => $recipient['created_at'],
                                'referer' => '',
                            ];
                        }

                        return [
                            'component' => 'k-dreamform-db-form',
                            'props' => [
                                'formSlug' => $formSlug,
                                'formTitle' => $form->title()->value(),
                                'resourceKey' => $formSlug,
                                'submissions' => $submissions,
                                'columns' => ['first_name', 'last_name', 'email'],
                                'pagination' => [
                                    'page' => $page,
                                    'total' => $total,
                                    'limit' => $limit,
                                    'pages' => max(1, (int) ceil($total / $limit)),
                                ],
                            ],
                        ];
                    }

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
                    $isNewsletterRecipient = $formSlug === DFDB_NEWSLETTER_FORM_SLUG
                        && preg_match('/^newsletter-recipient-(\d+)$/', $submissionId, $match);

                    if ($isNewsletterRecipient) {
                        $recipient = NewsletterRecipients::find((int) $match[1]);

                        if (!$recipient) {
                            throw new Exception('Eintrag nicht gefunden');
                        }

                        return [
                            'component' => 'k-form-dialog',
                            'props' => [
                                'fields' => [
                                    'first_name' => [
                                        'label' => 'Vorname',
                                        'type' => 'info',
                                        'text' => $recipient['first_name'],
                                    ],
                                    'last_name' => [
                                        'label' => 'Nachname',
                                        'type' => 'info',
                                        'text' => $recipient['last_name'],
                                    ],
                                    'email' => [
                                        'label' => 'E-Mail',
                                        'type' => 'info',
                                        'text' => $recipient['email'],
                                    ],
                                    'created_at' => [
                                        'label' => 'Eingegangen am',
                                        'type' => 'info',
                                        'text' => $recipient['created_at'],
                                    ],
                                ],
                                'submitButton' => false,
                            ],
                        ];
                    }

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
                            'text' => 'Soll dieser Eintrag wirklich gelöscht werden?'
                                . ' Diese Aktion kann nicht rückgängig gemacht werden.',
                            'submitButton' => [
                                'text' => 'Löschen',
                                'icon' => 'trash',
                                'theme' => 'negative',
                            ],
                        ],
                    ];
                },
                'submit' => function (string $formSlug, string $submissionId) {
                    $isNewsletterRecipient = $formSlug === DFDB_NEWSLETTER_FORM_SLUG
                        && preg_match('/^newsletter-recipient-(\d+)$/', $submissionId, $match);

                    if ($isNewsletterRecipient) {
                        NewsletterRecipients::delete((int) $match[1]);

                        return [
                            'message' => 'Eintrag gelöscht',
                        ];
                    }

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
