<?php

namespace GsMmh\WebPlugin;

use tobimori\DreamForm\Fields\TextField as DreamFormTextField;

class TextField extends DreamFormTextField
{
    use MaxLengthValidation;

    public static function blueprint(): array
    {
        $blueprint = parent::blueprint();

        $blueprint['tabs']['validation']['fields']['maxLength'] = static::maxLengthBlueprintField();

        $blueprint['tabs']['validation']['fields']['allowedCharacters'] = [
            'label' => 'Erlaubte Zeichen',
            'type' => 'select',
            'default' => 'all',
            'width' => '1/2',
            'options' => [
                'all' => 'Alle Zeichen',
                'letters' => 'Nur Buchstaben und Leerzeichen',
                'name' => 'Namen: Buchstaben, Leerzeichen, Apostroph, Bindestrich, Punkt',
                'alnum' => 'Buchstaben, Zahlen und Leerzeichen',
            ],
        ];

        $blueprint['tabs']['validation']['fields']['maxLengthErrorMessage'] =
            static::maxLengthErrorMessageBlueprintField();

        $blueprint['tabs']['validation']['fields']['charactersErrorMessage'] = [
            'label' => 'Fehlermeldung erlaubte Zeichen',
            'type' => 'text',
            'placeholder' => 'Der Text enthält nicht erlaubte Zeichen.',
            'width' => '1/2',
            'when' => [
                'allowedCharacters' => ['letters', 'name', 'alnum'],
            ],
        ];

        return $blueprint;
    }

    public function validate(): true|string
    {
        $requiredValidation = parent::validate();

        if ($requiredValidation !== true) {
            return $requiredValidation;
        }

        if ($this->value()->isEmpty()) {
            return true;
        }

        $value = trim((string) $this->value()->value());
        $maxLengthValidation = $this->validateMaxLength($value);

        if ($maxLengthValidation !== true) {
            return $maxLengthValidation;
        }

        return $this->validateAllowedCharacters($value);
    }

    private function validateAllowedCharacters(string $value): true|string
    {
        $mode = $this->block()->allowedCharacters()->or('all')->value();

        $patterns = [
            'letters' => '/^[\p{L}\p{M}\s]+$/u',
            'name' => '/^[\p{L}\p{M}][\p{L}\p{M}\s\'\-.]*$/u',
            'alnum' => '/^[\p{L}\p{M}\p{N}\s]+$/u',
        ];

        if (!isset($patterns[$mode]) || preg_match($patterns[$mode], $value) === 1) {
            return true;
        }

        return $this->validationMessage('charactersErrorMessage', 'Der Text enthält nicht erlaubte Zeichen.');
    }
}
