<?php

namespace GsMmh\WebPlugin;

use tobimori\DreamForm\Models\FormPage;

/**
 * Character limit for DreamForm fields: panel fields `maxLength` and
 * `maxLengthErrorMessage`, plus the check via mb_strlen.
 *
 * Used by TextField and TextareaField.
 */
trait MaxLengthValidation
{
    protected static function maxLengthBlueprintField(): array
    {
        return [
            'label' => 'Maximale Zeichen',
            'type' => 'number',
            'min' => 1,
            'width' => '1/2',
        ];
    }

    protected static function maxLengthErrorMessageBlueprintField(): array
    {
        return [
            'label' => 'Fehlermeldung maximale Zeichen',
            'type' => 'text',
            'placeholder' => 'Der Text ist zu lang.',
            'width' => '1/2',
        ];
    }

    public function maxLength(): int
    {
        return $this->block()->maxLength()->toInt();
    }

    /**
     * DreamForm's text and textarea snippets merge htmxAttr() into the input's attributes
     * as the last step. It is the only per-field hook into that markup, so the browser
     * limit `maxlength` goes in here instead of in a copied snippet.
     * The limit itself is enforced on the server in validate().
     */
    public function htmxAttr(FormPage $form): array
    {
        $attributes = parent::htmxAttr($form);

        if ($this->maxLength() > 0) {
            $attributes['maxlength'] = $this->maxLength();
        }

        return $attributes;
    }

    protected function validateMaxLength(string $value): true|string
    {
        $maxLength = $this->maxLength();

        // Browsers send line breaks as \r\n but count them (maxlength, counter) as one character.
        $value = str_replace("\r\n", "\n", $value);

        if ($maxLength > 0 && mb_strlen($value) > $maxLength) {
            return $this->validationMessage('maxLengthErrorMessage', 'Der Text ist zu lang.');
        }

        return true;
    }

    protected function validationMessage(string $field, string $fallback): string
    {
        return $this->block()->{$field}()->isNotEmpty()
            ? $this->block()->{$field}()->value()
            : $fallback;
    }
}
