<?php

// Stand-ins for tobimori\DreamForm\Fields (see dreamform.php), reduced to what the plugin's fields use.

namespace tobimori\DreamForm\Fields;

use Kirby\Cms\Block;
use Kirby\Content\Field as ContentField;
use tobimori\DreamForm\Models\FormPage;

abstract class Field
{
    public const TYPE = 'field';

    public function __construct(protected Block $block, protected ?ContentField $value = null)
    {
    }

    public static function type(): string
    {
        return static::TYPE;
    }

    public function block(): Block
    {
        return $this->block;
    }

    public function value(): ContentField
    {
        return $this->value ?? new ContentField(null, 'value', null);
    }

    public function errorMessage(): string
    {
        return $this->block()->errorMessage()->or('Pflichtfeld')->value();
    }

    public function validate(): true|string
    {
        if ($this->block()->required()->toBool() && $this->value()->isEmpty()) {
            return $this->errorMessage();
        }

        return true;
    }

    /** Without htmx (the default) DreamForm adds no attributes. */
    public function htmxAttr(FormPage $form): array
    {
        return [];
    }

    public static function blueprint(): array
    {
        return [
            'tabs' => [
                'field' => ['fields' => []],
                'validation' => [
                    'fields' => [
                        'required' => 'dreamform/fields/required',
                        'errorMessage' => 'dreamform/fields/error-message',
                    ],
                ],
            ],
        ];
    }
}

class TextField extends Field
{
    public const TYPE = 'text';
}

class TextareaField extends Field
{
    public const TYPE = 'textarea';
}
