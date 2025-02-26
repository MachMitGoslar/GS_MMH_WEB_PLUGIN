<?php 
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

Kirby::plugin('gs-mmh/gs-mmh-web-plugin', [
    'blueprints' => [
      'blocks/accordion'   => __DIR__ . '/blueprints/blocks/accordion.yml',
      'blocks/box'         => __DIR__ . '/blueprints/blocks/box.yml',
      'blocks/card'        => __DIR__ . '/blueprints/blocks/card.yml',
      'blocks/cardGroup'        => __DIR__ . '/blueprints/blocks/cardGroup.yml',
      'blocks/faq'         => __DIR__ . '/blueprints/blocks/faq.yml',
      'blocks/faq2'        => __DIR__ . '/blueprints/blocks/faq2.yml',
      'blocks/testimonial' => __DIR__ . '/blueprints/blocks/testimonial.yml',
    ],
    'snippets' => [
      'blocks/accordion'   => __DIR__ . '/snippets/blocks/accordion.php',
      'blocks/box'         => __DIR__ . '/snippets/blocks/box.php',
      'blocks/card'        => __DIR__ . '/snippets/blocks/card.php',
      'blocks/cardGroup'        => __DIR__ . '/blueprints/blocks/cardGroup.php',
      'blocks/faq'         => __DIR__ . '/snippets/blocks/faq.php',
      'blocks/faq2'        => __DIR__ . '/snippets/blocks/faq2.php',
      'blocks/testimonial' => __DIR__ . '/snippets/blocks/testimonial.php',
    ],
    'translations' => [
      'en' => [
        'field.blocks.accordion.name'   => 'Accordion block',
        'field.blocks.box.name'         => 'Textbox block',
        'field.blocks.card.name'        => 'Card',
        'field.blocks.cardGroup.name'        => 'Card Group',
        'field.blocks.faq.name'         => 'FAQ Section Version 1',
        'field.blocks.faq2.name'        => 'FAQ Section Version 2',
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
  ]);

?>