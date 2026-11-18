<?php

use GsMmh\WebPlugin\TextareaField;
use Kirby\Cms\Block;
use Kirby\Content\Field as ContentField;
use PHPUnit\Framework\TestCase;

final class TextareaFieldTest extends TestCase
{
    private static function field(?string $value, array $content = []): TextareaField
    {
        $block = new Block([
            'id' => 'gruss',
            'type' => 'textarea-field',
            'content' => ['key' => 'gruss', 'errorMessage' => 'Bitte ausfüllen.', ...$content],
        ]);

        return new TextareaField($block, new ContentField(null, 'gruss', $value));
    }

    public function testItIsRegisteredAsTheDreamFormTextarea(): void
    {
        $this->assertSame('textarea', TextareaField::type());
        $this->assertInstanceOf(tobimori\DreamForm\Fields\TextareaField::class, self::field(null));
    }

    public function testTheBlueprintOffersALimitAndAnErrorMessage(): void
    {
        $fields = TextareaField::blueprint()['tabs']['validation']['fields'];

        $this->assertSame('number', $fields['maxLength']['type']);
        $this->assertSame(1, $fields['maxLength']['min']);
        $this->assertSame('text', $fields['maxLengthErrorMessage']['type']);
        $this->assertArrayHasKey('required', $fields);
    }

    public function testTextUpToTheLimitIsValid(): void
    {
        $this->assertTrue(self::field(str_repeat('a', 140), ['maxLength' => 140])->validate());
    }

    public function testMultibyteCharactersCountOnce(): void
    {
        $this->assertTrue(self::field(str_repeat('ä', 140), ['maxLength' => 140])->validate());
        $this->assertTrue(self::field(str_repeat('🎄', 140), ['maxLength' => 140])->validate());
    }

    public function testLineBreaksCountAsOneCharacter(): void
    {
        $this->assertTrue(self::field("Frohe\r\nWeihnachten", ['maxLength' => 17])->validate());
    }

    public function testSurroundingWhitespaceIsIgnored(): void
    {
        $this->assertTrue(self::field("  Frohes Fest  \n", ['maxLength' => 11])->validate());
    }

    public function testTextOverTheLimitFailsWithTheFallbackMessage(): void
    {
        $this->assertSame(
            'Der Text ist zu lang.',
            self::field(str_repeat('a', 141), ['maxLength' => 140])->validate(),
        );
    }

    public function testTextOverTheLimitFailsWithTheConfiguredMessage(): void
    {
        $this->assertSame(
            'Maximal 140 Zeichen.',
            self::field(str_repeat('a', 141), [
                'maxLength' => 140,
                'maxLengthErrorMessage' => 'Maximal 140 Zeichen.',
            ])->validate(),
        );
    }

    public function testWithoutALimitAnyLengthIsValid(): void
    {
        $this->assertTrue(self::field(str_repeat('a', 5000))->validate());
    }

    public function testAnEmptyRequiredFieldStillFails(): void
    {
        $this->assertSame('Bitte ausfüllen.', self::field('', ['required' => 'true', 'maxLength' => 140])->validate());
    }

    public function testAnEmptyOptionalFieldIsValid(): void
    {
        $this->assertTrue(self::field('', ['maxLength' => 140])->validate());
    }
}
