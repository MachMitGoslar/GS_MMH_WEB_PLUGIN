<?php

use GsMmh\WebPlugin\LatestUpdate;
use Kirby\Cms\Page;
use PHPUnit\Framework\TestCase;

final class LatestUpdateTest extends TestCase
{
    public function testTimestampValueAcceptsIntsNumericStringsAndFields(): void
    {
        $this->assertSame(1700000000, LatestUpdate::timestampValue(1700000000));
        $this->assertSame(1700000000, LatestUpdate::timestampValue('1700000000'));
        $this->assertSame(42, LatestUpdate::timestampValue(new class () {
            public function toTimestamp(): int
            {
                return 42;
            }
        }));
        $this->assertEqualsWithDelta(time(), LatestUpdate::timestampValue(null), 2);
    }

    public function testTimestampPrefersPublishedOverProjectStart(): void
    {
        $both = new Page([
            'slug' => 'a',
            'content' => ['published' => '2026-01-02 10:00', 'project_start_date' => '2025-01-01'],
        ]);
        $start = new Page(['slug' => 'b', 'content' => ['project_start_date' => '2025-01-01']]);

        $this->assertSame(strtotime('2026-01-02 10:00'), LatestUpdate::timestamp($both));
        $this->assertSame(strtotime('2025-01-01'), LatestUpdate::timestamp($start));
    }

    public function testNothingToReportWithoutUpdate(): void
    {
        $this->assertNull(LatestUpdate::toArray(null));
    }

    public function testNewsletterPayload(): void
    {
        $newsletter = new Page([
            'slug' => 'ausgabe-7',
            'template' => 'newsletter',
            'content' => ['title' => 'Ausgabe 7', 'published' => '2026-03-04 09:30'],
        ]);

        $data = LatestUpdate::toArray($newsletter);

        $this->assertSame('MachMit!Haus', $data['title']);
        $this->assertSame('Ausgabe 7', $data['description'], 'newsletters use their title');
        $this->assertSame($newsletter->url(), $data['call_to_action_url']);
        $this->assertStringEndsWith('/api/newsletter-cover/ausgabe-7.jpg', $data['image_url']);
        $this->assertSame('2026-03-04T09:30', $data['published_at']);
    }

    public function testHighlightsLinkOverridesTheTarget(): void
    {
        $newsletter = new Page(['slug' => 'x', 'template' => 'newsletter', 'content' => ['title' => 'X']]);

        $this->assertStringEndsWith('/api/highlights', LatestUpdate::toArray($newsletter, true)['call_to_action_url']);
    }
}
