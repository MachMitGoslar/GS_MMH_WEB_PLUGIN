<?php 


Kirby::plugin('gs-mmh/gs-mmh-web-plugin', [
    'blueprints' => [
      'blocks/accordion'   => __DIR__ . '/blueprints/blocks/accordion.yml',
      'blocks/box'         => __DIR__ . '/blueprints/blocks/box.yml',
      'blocks/card'        => __DIR__ . '/blueprints/blocks/card.yml',
      'blocks/faq'        => __DIR__ . '/blueprints/blocks/faq2.yml',
      'blocks/testimonial' => __DIR__ . '/blueprints/blocks/testimonial.yml',
      'blocks/cta' => __DIR__ . '/blueprints/blocks/cta.yml',
      'blocks/button' => __DIR__ . '/blueprints/blocks/button.yml',
      'fields/buttonType' => __DIR__ . '/blueprints/fields/buttonType.yml',

    ],
    'snippets' => [
      'blocks/accordion'   => __DIR__ . '/snippets/blocks/accordion.php',
      'blocks/box'         => __DIR__ . '/snippets/blocks/box.php',
      'blocks/card'        => __DIR__ . '/snippets/blocks/card.php',
      'blocks/faq'         => __DIR__ . '/snippets/blocks/faq2.php',
      'blocks/testimonial' => __DIR__ . '/snippets/blocks/testimonial.php',
      'blocks/cta' => __DIR__ . '/snippets/blocks/cta.php',
      'blocks/button' => __DIR__ . '/snippets/blocks/button.php',

    ],
    'translations' => [
      'en' => [
        'field.blocks.accordion.name'   => 'Accordion block',
        'field.blocks.box.name'         => 'Textbox block',
        'field.blocks.card.name'        => 'Card',
        'field.blocks.card.fields.cardType.options.page'        => 'Create card from page',
        'field.blocks.card.fields.cardType.options.manual'        => 'Create manual card',
        'field.blocks.faq.name'        => 'FAQ Section',
        'field.blocks.testimonial.name' => 'Testimonial',
      ],
      'de' => [
        'field.blocks.accordion.name'   => 'Akkordion Block',
        'field.blocks.box.name'         => 'Textbox',
        'field.blocks.card.name'        => 'Karte',
        'field.blocks.faq.name'        => 'FAQ Sektion',
        'field.blocks.card.fields.cardType.options.page'        => 'Aus Seite erstellen',
        'field.blocks.card.fields.cardType.options.manual'        => 'Direkt erstellen',
        'field.blocks.card.fields.cardType.label'        => 'Kartentyp',
        'field.blocks.testimonial.name' => 'Testimonial',

        
      ]
    ],
    'routes' => [
      [
        'pattern' => 'newsletter.xml',
        'action'  => function() {
          $pages = site()->page("newsletter")->children()->listed();
          $parent = site()->page(path: "newsletter");
  
          $content = snippet('components/newsletter/rss_feed', compact('pages', 'parent'), true);
  
          // Return response with correct header type
          return new Kirby\Cms\Response($content, 'application/xml');
        }
      ]
    ],
    'hooks' => [
      'page.update:after' => function (Kirby\Cms\Page $newPage, Kirby\Cms\Page $oldPage) {
          if($oldPage->intendedTemplate()->name() == 'project_step') {
              print($oldPage->template());
              if($newPage->project_status_to()->isNotEmpty() && ($newPage->project_status_to() != $newPage->parent()->project_status())) {
                  $newPage->parent()->update([
                      "project_status" => $newPage->project_status_to()
                  ]);
              }
          }
      }
    ],
    'assets' => [
      'colors.css' => __DIR__ . "assets/colors.css",
      'fonts.css' => __DIR__ . "assets/fonts.css"
    ]
  ]);
  function getColor($status): string {
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
?>