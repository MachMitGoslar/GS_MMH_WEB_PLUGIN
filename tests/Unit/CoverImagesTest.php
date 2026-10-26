<?php

use GsMmh\WebPlugin\CoverImages;
use PHPUnit\Framework\TestCase;

final class CoverImagesTest extends TestCase
{
    public function testHexToRgb(): void
    {
        $this->assertSame([27, 77, 137], CoverImages::hexToRgb('#1b4d89'));
        $this->assertSame([255, 255, 255], CoverImages::hexToRgb('FFFFFF'));
        $this->assertNull(CoverImages::hexToRgb('#fff'));
        $this->assertNull(CoverImages::hexToRgb('#12345g'));
    }

    public function testMixRgbInterpolatesAndClampsTheAmount(): void
    {
        $this->assertSame([50, 100, 150], CoverImages::mixRgb([0, 0, 0], [100, 200, 300], 0.5));
        $this->assertSame([0, 0, 0], CoverImages::mixRgb([0, 0, 0], [255, 255, 255], -3));
        $this->assertSame([255, 255, 255], CoverImages::mixRgb([0, 0, 0], [255, 255, 255], 7));
    }

    public function testRgbColor(): void
    {
        $this->assertSame('rgb(1,2,3)', CoverImages::rgbColor([1, 2, 3]));
    }

    public function testXmlEscape(): void
    {
        $this->assertSame('a &amp; b &lt;c&gt; &quot;d&quot; &apos;e&apos;', CoverImages::xmlEscape('a & b <c> "d" \'e\''));
    }

    public function testWrapSvgTextBreaksAtWordBoundariesAndKeepsTwoLines(): void
    {
        $this->assertSame(['Kurzer Titel'], CoverImages::wrapSvgText('  Kurzer   Titel '));
        $this->assertSame(
            ['Ein sehr langer Titel der umbrochen', 'werden muss'],
            CoverImages::wrapSvgText('Ein sehr langer Titel der umbrochen werden muss', 36),
        );
        $this->assertCount(2, CoverImages::wrapSvgText(str_repeat('wort ', 60), 20), 'never more than two lines');
        $this->assertSame([], CoverImages::wrapSvgText('   '));
    }

    public function testCoverFileSlugOnlyKeepsSafeCharacters(): void
    {
        $this->assertSame('note-dein-slug', CoverImages::coverFileSlug('note', 'dein-slug'));
        $this->assertMatchesRegularExpression('/^[a-zA-Z0-9._-]+$/', CoverImages::coverFileSlug('note', 'ä ö/ü?x=1'));
        $this->assertStringNotContainsString('/', CoverImages::coverFileSlug('note', '../../etc/passwd'));
    }

    public function testSvgResponseForTheAppTeaser(): void
    {
        // The cover embeds the site's logo, taken from the index root.
        $logo = kirby()->root('index') . '/assets/svg/RZ-RGB_MM!2_iv.svg';
        is_dir(dirname($logo)) || mkdir(dirname($logo), 0777, true);
        file_put_contents(
            $logo,
            '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 10 10"><path d="M0 0h10v10z"/></svg>',
        );

        $response = CoverImages::coverSvgResponse('app', 'whatsapp-community');

        $this->assertSame(200, $response->code());
        $this->assertStringContainsString('svg', $response->type() . $response->body());
        $this->assertStringContainsString('WhatsApp Community', $response->body());
    }

    public function testUnknownCoverIsA404(): void
    {
        $this->assertSame(404, CoverImages::coverSvgResponse('app', 'gibt-es-nicht')->code());
        $this->assertSame(404, CoverImages::coverJpegResponse('nope', 'x')->code());
    }
}
