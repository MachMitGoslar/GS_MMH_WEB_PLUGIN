<?php $cards = $block->cards()->toBlocks(); ?>
<div class="c-card-group grid">
  <?php if($faqItems->isNotEmpty()): ?>
    <h2 class="font-headline mb-2"><?= $block->heading() ?></h2>
    <?= $cards ?>
  <?php endif; ?>
</div>