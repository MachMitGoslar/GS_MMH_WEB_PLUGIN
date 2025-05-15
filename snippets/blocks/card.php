<?php
$cardType = $block->cardType()->value();
$page     = $cardType === 'page' ? $block->page()->toPage() : null;
$link     = $page ? $page->url() : ($cardType === 'manual' ? $block->link()->value() : null);
$image    = $cardType === 'page' && $page ? $page->cover() : $block->image()->toFile();
$text     = $cardType === 'manual' ? $block->description_content(): ($page ? $page->text() : '');

?>
<?php if($block->isNotEmpty()): ?>
  <?php if(!empty($link)): ?>
    <a href="<?= $link ?>">
  <?php endif; ?>
    <?php if($image): ?>
      <figure>
        <img class="hero" src="<?= $image->crop(1500,1500)->url() ?>" alt="<?= $image->alt() ?>" />
      </figure>
    <?php endif ?>
    <div class="content">
      <div class="heading">
        <h3 class="font-headline font-line-height-narrow mb-2"><?= $block->headline() ?></h3>
        <?php if ($block->subheadline()->isNotEmpty()):?> <h4 class="font-subheadline font-line-height-narrow mb-2"><?= $block->subheadline()?></h4> <?php endif ?>
      </div>
      <div class="body">
        <?php if($text->type() == "text"): ?>
          <?= $text->kirbytext() ?>
        <?php else: ?>
          <?php foreach ($text->toBlocks() as $block): ?>
            <?= $block ?>
          <?php endforeach ?>
        <?php endif ?>
      </div>

  <?php if(!empty($link)): ?>
  </a>
  <?php endif; ?>
  </div>
<?php endif; ?>
