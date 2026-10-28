<?php

use GsMmh\WebPlugin\FormEntries;
use Kirby\Cms\Page;
use PHPUnit\Framework\TestCase;

final class FormEntriesTest extends TestCase
{
    private static function form(): Page
    {
        $entry = fn (string $slug, string $created, string $template = 'submission') => [
            'slug' => $slug,
            'template' => $template,
            'isDraft' => true,
            'content' => ['created' => $created],
        ];

        return new Page([
            'slug' => 'formular',
            'drafts' => [
                $entry('alt', '2026-01-01 10:00'),
                $entry('neu', '2026-03-01 10:00'),
                $entry('fremd', '2026-09-01 10:00', 'default'),
            ],
        ]);
    }

    public function testTheNewestSubmissionDraftIsPicked(): void
    {
        $this->assertSame('neu', FormEntries::newest(self::form())?->slug());
    }

    public function testFormsWithoutSubmissionsHaveNoNewestEntry(): void
    {
        $this->assertNull(FormEntries::newest(new Page(['slug' => 'leer'])));
    }

    public function testTheHookIgnoresMissingFormsAndEntries(): void
    {
        FormEntries::unlistNewest(null);
        FormEntries::unlistNewest(new Page(['slug' => 'leer']));

        $this->addToAssertionCount(1);
    }
}
