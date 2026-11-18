<?php

namespace GsMmh\WebPlugin;

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
