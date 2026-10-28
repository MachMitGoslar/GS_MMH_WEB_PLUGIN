<?php

use GsMmh\WebPlugin\FormEntries;
use GsMmh\WebPlugin\NewsletterRecipientAction;
use GsMmh\WebPlugin\NewsletterRecipients;
use GsMmh\WebPlugin\TextField;
use Kirby\Cms\App as Kirby;
use Kirby\Cms\Page as Page;
use Kirby\Cms\Response as Response;
use Kirby\Toolkit\Escape;

function cleanupProjectGhost(string $root): void
{
    if (!$root || !is_dir($root)) {
        return;
    }

    $entries = array_values(array_filter(scandir($root) ?: [], function ($entry) {
        return $entry !== '.' && $entry !== '..';
    }));

    $nonChanges = array_values(array_filter($entries, function ($entry) {
        return $entry !== '_changes';
    }));

    if (count($nonChanges) !== 1 || $nonChanges[0] !== 'project.txt') {
        return;
    }

    $projectFile = $root . DIRECTORY_SEPARATOR . 'project.txt';
    $content = is_file($projectFile) ? trim((string) file_get_contents($projectFile)) : '';
    if (!preg_match('/^Uuid:\\s*\\S+$/', $content)) {
        return;
    }

    @unlink($projectFile);
    if (is_dir($root . DIRECTORY_SEPARATOR . '_changes')) {
        $changesEntries = array_values(array_filter(scandir($root . DIRECTORY_SEPARATOR . '_changes') ?: [], function ($entry) {
            return $entry !== '.' && $entry !== '..';
        }));
        if (count($changesEntries) === 0) {
            @rmdir($root . DIRECTORY_SEPARATOR . '_changes');
        }
    }
    @rmdir($root);
}

function cleanupProjectGhostDelayed(string $root): void
{
    register_shutdown_function(function () use ($root) {
        cleanupProjectGhost($root);
    });
}

function resolveProjectArchiveStatus(Page $page): string
{
    return trim((string) $page->project_status()->value());
}

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
      'page.update:after' => function (Page $newPage, Page $oldPage) {
          static $isMovingProject = false;
          if ($oldPage->intendedTemplate()->name() == 'project_step') {
              if ($newPage->project_status_to()->isNotEmpty() && ($newPage->project_status_to() != $newPage->parent()->project_status())) {
                  $newPage->parent()->update([
                      'project_status' => $newPage->project_status_to(),
                  ]);
              }
          }

          if ($newPage->intendedTemplate()->name() === 'project') {
              if ($isMovingProject) {
                  return;
              }
              $site = $newPage->site();
              $projectsRoot = $site->find('projects');
              $archiveRoot = $site->find('project-archive');
              $oldRoot = $oldPage->root();

              if ($projectsRoot && $archiveRoot) {
                  $effectiveStatus = resolveProjectArchiveStatus($newPage);

                  if ($effectiveStatus !== '' && $effectiveStatus !== $newPage->project_status()->value()) {
                      Kirby::instance()->impersonate('kirby', function () use ($newPage, $effectiveStatus) {
                          $newPage->update([
                              'project_status' => $effectiveStatus,
                          ]);
                      });
                      $newPage = $newPage->clone([
                          'content' => array_replace($newPage->content()->toArray(), [
                              'project_status' => $effectiveStatus,
                          ]),
                      ]);
                  }

                  $shouldArchive = $effectiveStatus === 'abgeschlossen';
                  $isInArchive = $newPage->parent()->id() === $archiveRoot->id();
                  $isInProjects = $newPage->parent()->id() === $projectsRoot->id();

                  if ($shouldArchive && $isInProjects) {
                      $isMovingProject = true;
                      Kirby::instance()->impersonate('kirby', function () use ($newPage, $archiveRoot, $oldRoot) {
                          $newPage->move($archiveRoot);
                          cleanupProjectGhostDelayed($oldRoot);
                      });
                      $isMovingProject = false;
                  } elseif (!$shouldArchive && $isInArchive) {
                      $isMovingProject = true;
                      Kirby::instance()->impersonate('kirby', function () use ($newPage, $projectsRoot, $oldRoot) {
                          $newPage->move($projectsRoot);
                          cleanupProjectGhostDelayed($oldRoot);
                      });
                      $isMovingProject = false;
                  }
              }
          }
      },
      'page.changeStatus:after' => function (Page $newPage, Page $oldPage) {
          // Auto-set publish date for newsletters when published for the first time
          if ($newPage->intendedTemplate()->name() === 'newsletter') {
              // Check if page is being published (listed) and doesn't have a publish date yet
              if (
                  $newPage->status() === 'listed' &&
                  $oldPage->status() !== 'listed' &&
                  $newPage->published()->isEmpty()
              ) {
                  $newPage->update([
                      'published' => date('Y-m-d'),
                  ]);
              }
          }
          if ($newPage->intendedTemplate()->name() === 'note') {
              // Check if page is being published (listed) and doesn't have a publish date yet
              if (
                  $newPage->status() === 'listed' &&
                  $oldPage->status() !== 'listed' &&
                  $newPage->published()->isEmpty()
              ) {
                  $newPage->update([
                      'published' => date('Y-m-d'),
                  ]);
              }
          }
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
