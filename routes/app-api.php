<?php

/**
 * Routes of the app API (/api/...): cover images, latest update, highlights.
 * Public (`auth => false`), used by the Goslar app.
 */

use GsMmh\WebPlugin\CoverImages;
use GsMmh\WebPlugin\LatestUpdate;

return [
    /**
     * Create Newsletter JPEG
     */
    [
        'pattern' => 'newsletter-cover/(:any).jpg',
        'method' => 'GET',
        'auth' => false,
        'action' => function ($slug) {
            return CoverImages::coverJpegResponse('newsletter', $slug);
        },
    ],
    [
        'pattern' => 'newsletter-cover/(:any).svg',
        'method' => 'GET',
        'auth' => false,
        'action' => function ($slug) {
            return CoverImages::coverSvgResponse('newsletter', $slug);
        },
    ],
    /**
     * Create Notes Cover
     */
    [
        'pattern' => 'notes-cover/(:any).jpg',
        'method' => 'GET',
        'auth' => false,
        'action' => function ($slug) {
            return CoverImages::coverJpegResponse('notes', $slug);
        },
    ],
    [
        'pattern' => 'notes-cover/(:any).svg',
        'method' => 'GET',
        'auth' => false,
        'action' => function ($slug) {
            return CoverImages::coverSvgResponse('notes', $slug);
        },
    ],
    [
        'pattern' => 'app-cover/(:any).jpg',
        'method' => 'GET',
        'auth' => false,
        'action' => function ($slug) {
            return CoverImages::coverJpegResponse('app', $slug);
        },
    ],
    [
        'pattern' => 'app-cover/(:any).svg',
        'method' => 'GET',
        'auth' => false,
        'action' => function ($slug) {
            return CoverImages::coverSvgResponse('app', $slug);
        },
    ],

    /**
         * Latest Update for Goslar App Kachel
         */
    [
        'pattern' => 'latest-update',
        'method' => 'GET',
        'auth' => false,
        'action' => function () {
            $data = LatestUpdate::data(true);

            if (!$data) {
                return [
                    'status' => 'error',
                    'message' => 'Keine Updates gefunden',
                ];
            }

            return $data;
        },
    ],
    /**
     * Endpoint 2nd view in app
     */
    [
        'pattern' => 'highlights',
        'method' => 'GET',
        'auth' => false,
        'action' => function () {

            // get updates dynamically
            $latestUpdate = LatestUpdate::latest();
            $isLatestNewsletter = $latestUpdate?->intendedTemplate()->name() === 'newsletter';
            $dynamic = LatestUpdate::toArray($latestUpdate);

            if ($dynamic) {
                $dynamic['id'] = 2;
                unset($dynamic['widget_type']);
            }

            $homePage = page('home');
            $projectsPage = page('projects');
            $newsletterPage = page('newsletter');
            $diaryPage = page('notes');
            $aboutPage = page('uber-uns');
            $latestNewsletter = $newsletterPage?->children()->listed()->sortBy('published', 'desc')->first();
            $latestDiary = $diaryPage?->children()->listed()->sortBy('date', 'desc')->first();

            $now = date('Y-m-d\TH:i');

            $coverUrl = function ($p) {
                if (!$p) {
                    return null;
                }
                $coverFile = $p->content()->get('cover')?->toFile();

                return CoverImages::jpegImageUrl($coverFile);
            };

            $highlights = [
                [
                    'id' => 1,
                    'title' => 'Heute im MM!H',
                    'description' => 'Entdecke die heutigen Events im MachMit!Haus oder gelange zur Raumbuchung.',
                    'image_url' => $coverUrl($homePage),
                    'call_to_action_url' => $homePage?->url(),
                    'published_at' => $now,
                ],

                $dynamic, // Slot 2 = Latest Update

                [
                    'id' => 3,
                    'title' => 'Projekte',
                    'description' => 'Alle MachMit!Projekte auf einem Blick',
                    'image_url' => $coverUrl($projectsPage),
                    'call_to_action_url' => $projectsPage?->url(),
                    'published_at' => $now,
                ],
            ];

            if (!$isLatestNewsletter) {
                $highlights[] = [
                    'id' => 4,
                    'title' => 'Newsletter',
                    'description' => 'Entdecke unseren Newsletter.',
                    'image_url' => $latestNewsletter
                        ? CoverImages::coverFileUrl('newsletter', $latestNewsletter->slug())
                        : null,
                    'call_to_action_url' => $newsletterPage?->url(),
                    'published_at' => $now,
                ];
            }

            $highlights[] = [
                'id' => 5,
                'title' => 'Tagebuch',
                'description' => 'Berichte aus unserem Alltag.',
                'image_url' => $latestDiary
                    ? CoverImages::coverFileUrl('notes', $latestDiary->slug())
                    : null,
                'call_to_action_url' => $diaryPage?->url(),
                'published_at' => $now,
            ];
            $highlights[] = [
                'id' => 6,
                'title' => 'Über uns',
                'description' => 'Verschaffe dir einen Überblick!',
                'image_url' => $coverUrl($aboutPage),
                'call_to_action_url' => $aboutPage?->url(),
                'published_at' => $now,
            ];
            $highlights[] = [
                'id' => 7,
                'title' => 'WhatsApp Community',
                'description' => 'Tritt unserer WhatsApp Community bei und bleibe immer auf dem Laufenden!',
                'image_url' => CoverImages::coverFileUrl('app', 'whatsapp-community'),
                'call_to_action_url' => 'https://chat.whatsapp.com/IxjUee7gVOY3KfQhvdUsA3?mode=gi_t',
            ];

            return $highlights;
        },
    ],
];
