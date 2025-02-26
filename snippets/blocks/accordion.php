
<?php if($block->summary()->isNotEmpty()): ?>
  <details class="accordion-details">
    <summary class="accordion-summary"><?= $block->summary() ?></summary>
    <p class="accordion-text font-caption"><?= $block->details() ?></p>
  </details>
<?php endif; ?>