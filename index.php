<?php

use GsMmh\WebPlugin\DatabaseAction;
use Kirby\Cms\App as Kirby;
use Kirby\Cms\Page as Page;
use Kirby\Cms\Response as Response;
use Kirby\Database\Db as Db;

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

@include_once __DIR__ . '/DatabaseAction.php';
DreamForm::register(DatabaseAction::class);

Kirby::plugin('gs-mmh/gs-mmh-web-plugin', [
    'options' => [
      'panel.viewButtons.page' => [
        'pdf' => function (Page $page) {
            if ($page->intendedTemplate()->name() !== 'newsletter') {
                return null;
            }

            return [
              'icon' => 'download',
              'text' => 'PDF',
              'title' => 'PDF herunterladen',
              'link' => $page->url() . '?pdf=1',
              'responsive' => false,
              'target' => '_blank',
            ];
        },
      ],
    ],
    'blueprints' => [
      'blocks/accordion' => __DIR__ . '/blueprints/blocks/accordion.yml',
      'blocks/card' => __DIR__ . '/blueprints/blocks/card.yml',
      'blocks/testimonial' => __DIR__ . '/blueprints/blocks/testimonial.yml',
      'blocks/cta' => __DIR__ . '/blueprints/blocks/cta.yml',
      'blocks/button' => __DIR__ . '/blueprints/blocks/button.yml',
      'blocks/download' => __DIR__ . '/blueprints/blocks/download.yml',
      'blocks/faq' => __DIR__ . '/blueprints/blocks/faq2.yml',
      'fields/buttonType' => __DIR__ . '/blueprints/fields/buttonType.yml',
      'blocks/text' => __DIR__ . '/blueprints/blocks/text.yml',
      'blocks/timeline' => __DIR__ . '/blueprints/blocks/timeline.yml',
      'blocks/form' => __DIR__ . '/blueprints/blocks/form.yml',

    ],
    'snippets' => [
      'blocks/accordion' => __DIR__ . '/snippets/blocks/accordion.php',
      'blocks/card' => __DIR__ . '/snippets/blocks/card.php',
      'blocks/testimonial' => __DIR__ . '/snippets/blocks/testimonial.php',
      'blocks/cta' => __DIR__ . '/snippets/blocks/cta.php',
      'blocks/button' => __DIR__ . '/snippets/blocks/button.php',
      'blocks/download' => __DIR__ . '/snippets/blocks/download.php',
      'blocks/faq' => __DIR__ . '/snippets/blocks/faq2.php',
      'writer-marks/button' => __DIR__ . '/snippets/writer-marks/button.php',
      'blocks/timeline' => __DIR__ . '/snippets/blocks/timeline.php',
      'blocks/form' => __DIR__ . '/snippets/blocks/form.php',
    ],
    'blockMethods' => [
      'scheduleLabel' => function ($block) {
          $publish = $block->publish_date()->isNotEmpty()
              ? $block->publish_date()->toDate('d.m.Y H:i')
              : null;
          $end = $block->end_date()->isNotEmpty()
              ? $block->end_date()->toDate('d.m.Y H:i')
              : null;

        if ($publish && $end) {
            return "🕒 {$publish} → {$end}";
        }

        if ($publish) {
            return "🕒 ab {$publish}";
        }

        if ($end) {
            return "🕒 bis {$end}";
        }

          return null;
      },
    ],
    'translations' => [
      'en' => [
        'field.blocks.accordion.name' => 'Accordion block',
        'field.blocks.box.name' => 'Textbox block',
        'field.blocks.card.name' => 'Card',
        'field.blocks.card.fields.cardType.options.page' => 'Create card from page',
        'field.blocks.card.fields.cardType.options.manual' => 'Create manual card',
        'field.blocks.faq.name' => 'FAQ Section',
        'field.blocks.testimonial.name' => 'Testimonial',
      ],
      'de' => [
        'field.blocks.accordion.name' => 'Akkordion Block',
        'field.blocks.box.name' => 'Textbox',
        'field.blocks.card.name' => 'Karte',
        'field.blocks.faq.name' => 'FAQ Sektion',
        'field.blocks.card.fields.cardType.options.page' => 'Aus Seite erstellen',
        'field.blocks.card.fields.cardType.options.manual' => 'Direkt erstellen',
        'field.blocks.card.fields.cardType.label' => 'Kartentyp',
        'field.blocks.testimonial.name' => 'Testimonial',

      ],
    ],
    'routes' => [
      [
        'pattern' => 'newsletter.xml',
        'action' => function () {
            $pages = site()->page('newsletter')->children()->listed();
            $parent = site()->page(path: 'newsletter');

            $content = snippet('components/newsletter/rss_feed', compact('pages', 'parent'), true);

            // Return response with correct header type
            return new Response($content, 'application/xml');
        },
      ],
      [
        'pattern' => '/app/(:any)',
        'action' => function ($any) {

            Db::execute('CREATE TABLE IF NOT EXISTS `app_requests` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `url` varchar(255) NOT NULL,
                    `day` date NOT NULL,
                    `requests` int(11) NOT NULL,
                    PRIMARY KEY (`id`)
                    ) 
                ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;');

            $data['url'] = $any;
            $data['day'] = date('Y-m-d');

            if ($app_request = Db::first('app_requests', '*', ['url' => $data['url'], 'day' => $data['day']])) {
                $data['requests'] = $app_request->requests();

                Db::update('app_requests', $data, ['url' => $data['url'], 'day' => $data['day']]);
                $this->next();
            } else {
                $data['requests'] = 1;
                Db::insert('app_requests', $data);
                $this->next();
            }
        },
      ],
      [
        'pattern' => '/app/ferienpass.json',
        'action' => function () {

            $content = snippet('content-types/ferienpass/event_random', ['id' => 74], true);

            return new Response($content, 'application/json');
        },
      ],
      [
        'pattern' => '/app/ferienpass_index.json',
        'action' => function () {

            $content = snippet('content-types/ferienpass/events', [], true);

            return new Response($content, 'application/json');
        },
      ],
    ],
    'hooks' => [
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
        if ($newPage->intendedTemplate()->name() === 'notes') {
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
      'rooms-booking' => require __DIR__ . '/areas/rooms-booking.php',
    ],
    'api' => [
      'routes' => function () {
          return [
            [
              'pattern' => 'gs-mmh-web-plugin/forms',
              'method' => 'GET',
              'auth' => true,
              'action' => function () {
                  $formsPage = DreamForm::findPageOrDraftRecursive(
                      DreamForm::option('page', 'page://forms')
                  );

                if (!$formsPage) {
                    return [
                      'forms' => [],
                    ];
                }

                  $forms = $formsPage
                      ->childrenAndDrafts()
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
          ];
      },
    ],
    'assets' => [
      'design-system' => __DIR__ . '/src/design-system.css',
    ],
  ]);
function getColor($status): string
{
    switch ($status) {
        case 'in Planung':
            return 'planning';
        case 'in Vorbereitung':
            return 'preparing';
        case 'aktiv':
            return 'active';
        case 'in Auswertung':
            return 'review';
        case 'abgeschlossen':
            return 'done';
        default:
            return 'false';
    }
}
