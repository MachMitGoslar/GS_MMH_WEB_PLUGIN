<?php

/**
 * Copy of kirby-dreamform/snippets/fields/textarea.php with `maxlength` and a character counter.
 *
 * @var \tobimori\DreamForm\Models\Submission|null $submission
 * @var \Kirby\Cms\Block $block
 * @var \GsMmh\WebPlugin\TextareaField $field
 * @var \tobimori\DreamForm\Models\FormPage $form
 * @var array $attr
 */

use Kirby\Toolkit\A;

$maxLength = $block->maxLength()->toInt();
$textareaId = $form->elementId($block->id());
$counterId = $form->elementId("{$block->id()}/counter");
$value = (string) $form->valueFor($block->key());

$attr = A::merge($attr, $attr['textarea'] ?? []);
snippet('dreamform/fields/partials/wrapper', $arguments = compact('block', 'field', 'form', 'attr'), slots: true);
snippet('dreamform/fields/partials/label', $arguments); ?>

<textarea <?= attr(A::merge(
    $attr['input'] ?? [],
    [
        'id' => $textareaId,
        'name' => $block->key(),
        'placeholder' => $block->placeholder()->or(' '),
        'required' => $block->required()->toBool() ?? null,
        'maxlength' => $maxLength > 0 ? $maxLength : null,
        'aria-invalid' => ($error = $submission?->errorFor($block->key(), $form)) ? true : null,
        'aria-describedby' => $error
            ? $form->elementId("{$block->id()}/error")
            : ($maxLength > 0 ? $counterId : null),
    ],
    $field->htmxAttr($form),
)) ?>><?= $form->valueFor($block->key())?->escape() ?></textarea>

<?php if ($maxLength > 0) : ?>
<p class="dreamform-counter" id="<?= esc($counterId, 'attr') ?>" aria-live="polite">
    <span data-counter-value><?= mb_strlen($value) ?></span> / <?= $maxLength ?>
</p>
<script>
(() => {
    const textarea = document.getElementById(<?= json_encode($textareaId) ?>);
    const counter = document.getElementById(<?= json_encode($counterId) ?>);
    if (!textarea || !counter) return;
    const output = counter.querySelector('[data-counter-value]');
    const update = () => { output.textContent = [...textarea.value].length; };
    textarea.addEventListener('input', update);
    update();
})();
</script>
<?php endif ?>

<?php snippet('dreamform/fields/partials/error', $arguments);
endsnippet() ?>
