<?php

namespace GsMmh\WebPlugin;

use tobimori\DreamForm\Fields\TextareaField as DreamFormTextareaField;

/** DreamForm textarea with an optional character limit (e.g. 140 characters for greetings). */
class TextareaField extends DreamFormTextareaField
{
    use MaxLengthValidation;

    public static function blueprint(): array
    {
        $blueprint = parent::blueprint();

        $blueprint['tabs']['validation']['fields']['maxLength'] = static::maxLengthBlueprintField();
        $blueprint['tabs']['validation']['fields']['maxLengthErrorMessage'] =
            static::maxLengthErrorMessageBlueprintField();

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

        return $this->validateMaxLength(trim((string) $this->value()->value()));
    }
}
