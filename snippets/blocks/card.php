<?php

/** @var \Kirby\Cms\Block $block */

use Kirby\Cms\File;

$cardType = $block->cardType()->value();
$page = $cardType === 'page' ? $block->page()->toPage() : null;

// Link target: page cards link to the selected page, manual cards to the link field
$link = $page ? $page->url() : $block->link()->toUrl();
$linkMode = $block->linkMode()->or('none')->value();

if (empty($link)) {
    $linkMode = 'none';
}

// Cover image: page models may return a File, a Field or nothing at all
if ($page) {
    $cover = $page->cover();

    if ($cover instanceof File) {
        $image = $cover;
    } elseif (is_object($cover) === true && method_exists($cover, 'toFile') === true) {
        $image = $cover->toFile();
    } else {
        $image = null;
    }

    $image ??= $page->image();
} else {
    $image = $block->image()->toFile();
}

$headline = $page ? $page->headline()->or($page->title()) : $block->headline();
$subheadline = $page ? $page->subheadline() : $block->subheadline();
$text = $cardType === 'manual' ? $block->description_content() : ($page ? $page->text() : null);

// Footer button styling, same structure as the button block
$buttonType = $block->buttontype()->toObject();
?>
<?php if ($block->isNotEmpty()) : ?>
    <?php if ($image) : ?>
      <figure>
        <img class="hero" src="<?= $image->crop(1500, 1500)->url() ?>" alt="<?= $image->alt() ?>" />
      </figure>
    <?php endif ?>
    <div class="content">
      <div class="heading">
        <h3 class="font-headline font-line-height-narrow mb-2">
            <?php if ($linkMode === 'header') : ?>
              <a href="<?= esc($link) ?>"><?= $headline ?></a>
            <?php else : ?>
                <?= $headline ?>
            <?php endif ?>
        </h3>
        <?php if ($subheadline->isNotEmpty()) : ?>
          <h4 class="font-subheadline font-line-height-narrow mb-2"><?= $subheadline ?></h4>
        <?php endif ?>
      </div>
      <div class="body">
        <?php if ($text) : ?>
            <?php foreach ($text->toBlocks() as $contentBlock) : ?>
                <?= $contentBlock ?>
            <?php endforeach ?>
        <?php endif ?>
      </div>
        <?php if ($linkMode === 'button') : ?>
        <footer class="card-footer">
          <a
            href="<?= esc($link) ?>"
            class="gs-c-btn"
            data-button="true"
            data-type="<?= esc($buttonType->color()->or('primary')) ?>"
            data-size="<?= esc($buttonType->size()->or('regular')) ?>"
            data-style="<?= esc($buttonType->style()->or('pill')) ?>"
          >
              <?= esc($block->linktext()->or('Mehr erfahren')) ?>
          </a>
        </footer>
        <?php endif ?>
    </div>
<?php endif ?>
