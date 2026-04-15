<?php

// Button mark renders selected text as a button with link functionality
// Mark attributes: href, target, buttonType

$href = $mark['href'] ?? '#';
$target = $mark['target'] ?? '';
$buttonType = $mark['buttonType'] ?? [];

$color = $buttonType['color'] ?? 'primary';
$size = $buttonType['size'] ?? 'normal';
$style = $buttonType['style'] ?? 'pill';
$look = $buttonType['look'] ?? 'fill';

$targetAttr = $target ? ' target="' . esc($target) . '"' : '';
?>
<a 
    href="<?= esc($href) ?>" 
    class="gs-c-btn" 
    data-button="true"
    data-type="<?= esc($color) ?>" 
    data-size="<?= esc($size) ?>" 
    data-style="<?= esc($style) ?>" 
    data-look="<?= esc($look) ?>"
    <?= $targetAttr ?>
>
    <?= $slot ?>
</a>
