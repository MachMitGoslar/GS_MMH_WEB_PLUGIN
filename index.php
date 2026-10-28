<?php

use GsMmh\WebPlugin\FormEntries;
use GsMmh\WebPlugin\NewsletterRecipientAction;
use GsMmh\WebPlugin\NewsletterRecipients;
use GsMmh\WebPlugin\TextField;
use Kirby\Cms\App as Kirby;
use Kirby\Cms\Page as Page;
use tobimori\DreamForm\DreamForm;

// All classes (GsMmh\WebPlugin\…) load on demand from classes/. Kirby loads vendor/autoload.php of a
// plugin if there is one, but the deployed plugins come from git without `composer install`.
spl_autoload_register(static function (string $class): void {
    $prefix = 'GsMmh\\WebPlugin\\';

    if (str_starts_with($class, $prefix)) {
        $file = __DIR__ . '/classes/' . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';

        if (is_file($file)) {
            require_once $file;
        }
    }
});

Kirby::plugin('gs-mmh/gs-mmh-web-plugin', [
    'blueprints' => [
      'blocks/form' => __DIR__ . '/blueprints/blocks/form.yml',

    ],
    'snippets' => [
      'app/horoscope-list' => __DIR__ . '/snippets/app/horoscope-list.php',
      'writer-marks/button' => __DIR__ . '/snippets/writer-marks/button.php',
      'blocks/form' => __DIR__ . '/snippets/blocks/form.php',
      'dreamform/fields/text' => __DIR__ . '/snippets/dreamform/fields/text.php',
      'content-elements/form' => __DIR__ . '/snippets/content-elements/form.php',
    ],
    'sections' => [
      'newsletter-recipients' => require __DIR__ . '/sections/newsletter-recipients.php',
      'seo-preview' => require __DIR__ . '/sections/seo-preview.php',
    ],
    'routes' => [
      // App routes under /app/ (horoscope, request counter, Ferienpass)
      ...require __DIR__ . '/routes/app.php',
    ],
    'hooks' => [
      'dreamform.submitted:after' => function ($submission, $form) {
          FormEntries::unlistNewest($form);
      },
      'system.loadPlugins:after' => function () {
          DreamForm::register(NewsletterRecipientAction::class, TextField::class);
      },
    ],
    'areas' => [
      'formular-eingaenge' => require __DIR__ . '/areas/submissions.php',
    ],
    'api' => [
      'routes' => function () {
          return [
            // App API (covers, latest update, highlights)
            ...require __DIR__ . '/routes/app-api.php',
            [
              'pattern' => 'gs-mmh-web-plugin/forms',
              'method' => 'GET',
              'auth' => true,
              'action' => function () {
                  $formsPage = DreamForm::findPageOrDraftRecursive(
                      DreamForm::option('page', 'page://forms'),
                  );

                  if (!$formsPage) {
                      return [
                        'forms' => [],
                      ];
                  }

                  $forms = $formsPage
                      ->children()
                      ->listed()
                      ->filterBy('intendedTemplate', 'form')
                      ->sortBy('title', 'asc')
                      ->map(fn (Page $form) => [
                        'value' => $form->id(),
                        'text' => $form->title()->value(),
                      ])
                      ->values();

                  return [
                    'forms' => $forms,
                  ];
              },
            ],
            [
              'pattern' => 'gs-mmh-web-plugin/newsletter-recipients',
              'method' => 'GET',
              'auth' => true,
              'action' => function () {
                  return [
                    'recipients' => NewsletterRecipients::all(),
                    'total' => count(NewsletterRecipients::all()),
                  ];
              },
            ],
          ];
      },
    ],
    'assets' => [
      'design-system' => __DIR__ . '/src/design-system.css',
    ],
  ]);
