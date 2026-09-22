<?php $form = $block->form()->toPage(); ?>
<?php if ($form) : ?>
  <h2 class="dreamform-title"><?= $form->title()->html() ?></h2>
<?php endif ?>
<?php snippet('content-elements/form', ['form' => $form]); ?>
