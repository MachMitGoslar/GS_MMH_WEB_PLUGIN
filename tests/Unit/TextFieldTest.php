<?php

use GsMmh\WebPlugin\TextField;
use Kirby\Cms\Block;
use Kirby\Content\Field as ContentField;
use PHPUnit\Framework\TestCase;

/** TextField after the move of the maxLength logic into the MaxLengthValidation trait. */
final class TextFieldTest extends TestCase
{
    private static function field(?string $value, array $content = []): TextField
    {
        $block = new Block([
            'id' => 'name',
            'type' => 'text-field',
            'content' => ['key' => 'name', ...$content],
        ]);

        return new TextField($block, new ContentField(null, 'name', $value));
    }

    public function testTheBlueprintKeepsItsFieldOrder(): void
    {
        $fields = array_keys(TextField::blueprint()['tabs']['validation']['fields']);

        $this->assertSame(
            ['maxLength', 'allowedCharacters', 'maxLengthErrorMessage', 'charactersErrorMessage'],
            array_slice($fields, -4),
        );
    }

    public function testTheLimitIsChecked(): void
    {
        $this->assertTrue(self::field('Anna', ['maxLength' => 4])->validate());
        $this->assertSame('Der Text ist zu lang.', self::field('Annika', ['maxLength' => 4])->validate());
    }

    public function testTheLimitIsCheckedBeforeTheAllowedCharacters(): void
    {
        $this->assertSame(
            'Zu lang.',
            self::field('Anna1', [
                'maxLength' => 4,
                'maxLengthErrorMessage' => 'Zu lang.',
                'allowedCharacters' => 'letters',
            ])->validate(),
        );
    }

    public function testAllowedCharactersStillWork(): void
    {
        $this->assertTrue(self::field("Anne-Marie O'Neill", ['allowedCharacters' => 'name'])->validate());
        $this->assertSame(
            'Der Text enthält nicht erlaubte Zeichen.',
            self::field('Anna1', ['allowedCharacters' => 'letters'])->validate(),
        );
    }
}
