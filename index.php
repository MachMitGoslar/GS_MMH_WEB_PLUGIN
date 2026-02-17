<?php

use Kirby\Cms\App as Kirby;
use Kirby\Cms\Page as Page;
use Kirby\Cms\Response as Response;
use Kirby\Database\Db as Db;

Kirby::plugin('gs-mmh/gs-mmh-web-plugin', [
    'blueprints' => [
      'blocks/accordion' => __DIR__ . '/blueprints/blocks/accordion.yml',
      'blocks/card' => __DIR__ . '/blueprints/blocks/card.yml',
      'blocks/testimonial' => __DIR__ . '/blueprints/blocks/testimonial.yml',
      'blocks/cta' => __DIR__ . '/blueprints/blocks/cta.yml',
      'blocks/button' => __DIR__ . '/blueprints/blocks/button.yml',
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
      'writer-marks/button' => __DIR__ . '/snippets/writer-marks/button.php',
      'blocks/timeline' => __DIR__ . '/snippets/blocks/timeline.php',
      'blocks/form' => __DIR__ . '/snippets/blocks/form.php',

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
            $pages = site()->page("newsletter")->children()->listed();
            $parent = site()->page(path: "newsletter");

            $content = snippet('components/newsletter/rss_feed', compact('pages', 'parent'), true);

            // Return response with correct header type
            return new Response($content, 'application/xml');
        },
      ],
      [
        'pattern' => '/app/(:any)',
        'action' => function ($any) {

            $data['url'] = $any;
            $data['day'] = date("Y-m-d");

            if ($app_request = Db::first('app_requests', '*', ['url' => $data['url'], 'day' => $data['day']])) {
                $data['requests'] = $app_request->requests();

                return Db::update('app_requests', $data, ['url' => $data['url'], 'day' => $data['day']]);
            } else {
                $data['requests'] = 1;

                return Db::insert('app_requests', $data);
            }

        },
      ],
      [
        'pattern' => '/app/ferienpass.json',
        'action' => function () {
            $content = snippet('components/ferienpass/event_random', [], true);

            return new Response($content, 'application/json');
        },
      ],
      [
        'pattern' => '/app/ferienpass_index.json',
        'action' => function () {
            $content = snippet('components/ferienpass/events', [], true);

            return new Response($content, 'application/json');
        },
      ],
    ],
    'hooks' => [
      'page.update:after' => function (Page $newPage, Page $oldPage) {
          if ($oldPage->intendedTemplate()->name() == 'project_step') {
              if ($newPage->project_status_to()->isNotEmpty() && ($newPage->project_status_to() != $newPage->parent()->project_status())) {
                  $newPage->parent()->update([
                      "project_status" => $newPage->project_status_to(),
                  ]);
              }
          }
      },
      'page.changeStatus:after' => function (Page $newPage, Page $oldPage) {
          // Auto-set publish date for newsletters when published for the first time
          if ($newPage->intendedTemplate()->name() === 'newsletter') {
              // Check if page is being published (listed) and doesn't have a publish date yet
              if ($newPage->status() === 'listed' &&
                  $oldPage->status() !== 'listed' &&
                  $newPage->published()->isEmpty()) {
                  $newPage->update([
                      'published' => date('Y-m-d'),
                  ]);
              }
          }
          if ($newPage->intendedTemplate()->name() === 'notes') {
              // Check if page is being published (listed) and doesn't have a publish date yet
              if ($newPage->status() === 'listed' &&
                  $oldPage->status() !== 'listed' &&
                  $newPage->published()->isEmpty()) {
                  $newPage->update([
                      'published' => date('Y-m-d'),
                  ]);
              }
          }
      },
    ],
    'assets' => [
      'design-system' => __DIR__ . "/src/design-system.css",
    ],
  ]);
function getColor($status): string
{
    switch ($status) {
        case "in Planung":
            return "planning";
        case "in Vorbereitung":
            return "preparing";
        case "aktiv":
            return "active";
        case "in Auswertung":
            return "review";
        case "abgeschlossen":
            return "done";
        default:
            return "false";
    }
}
