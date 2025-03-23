<?php 
    $link = $block->link();
    $buttonSettings = $block->buttontype()->toObject();
?>
<a class="gs-c-btn" data-style="<?= $buttonSettings->style() || "pill" ?>" data-type="<?= $buttonSettings->color() || "primary" ?>"
    href="<?= $link ?>" target="<?= $link->target() ? '_self' : '_blank' ?>"> 
    <?= $block->linktext() ?>
</a>