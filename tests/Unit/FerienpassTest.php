<?php

use GsMmh\WebPlugin\Ferienpass;
use PHPUnit\Framework\TestCase;

final class FerienpassTest extends TestCase
{
    private static function events(): array
    {
        return [
            [
                'name' => 'Basteln',
                'description' => 'Mit Papier',
                'duration_summary' => '2 Std.',
                'start' => '2026-07-02 10:00',
                'event_id' => 11,
                'cover_photo' => ['thumbnail' => 'https://x.test/t.jpg'],
            ],
            [
                'name' => 'Wandern',
                'description' => 'Im Harz',
                'duration_summary' => '4 Std.',
                'start' => '2026-07-01 09:00',
                'event_id' => 12,
                'cover_photo' => null,
            ],
        ];
    }

    public function testTheListUsesRunningIdsAndFeriproLinks(): void
    {
        $list = Ferienpass::eventList(self::events(), 74);

        $this->assertSame([0, 1], array_column($list, 'id'));
        $this->assertSame('Basteln', $list[0]['title']);
        $this->assertSame('2 Std. Mit Papier', $list[0]['description']);
        $this->assertSame('https://goslar.feripro.de/anmeldung/74/veranstaltungen/11', $list[0]['call_to_action_url']);
        $this->assertSame('https://x.test/t.jpg', $list[0]['image_url']);
        $this->assertStringContainsString('csm_Post_', $list[1]['image_url'], 'fallback picture without cover photo');
    }

    public function testTheRandomEventPointsToTheList(): void
    {
        $card = Ferienpass::randomEvent(self::events(), 12, 1);

        $this->assertSame('Wandern', $card['title']);
        $this->assertSame('<strong>Wandern</strong> - Im Harz', $card['description']);
        $this->assertSame('2026-07-01 09:00', $card['published_at']);
        $this->assertSame('https://mmh.goslar.de/app/ferienpass_index.json?data=12', $card['call_to_action_url']);
    }

    public function testARandomEventIsOneOfTheGivenEvents(): void
    {
        $titles = array_column(self::events(), 'name');

        for ($i = 0; $i < 20; $i++) {
            $this->assertContains(Ferienpass::randomEvent(self::events(), 74)['title'], $titles);
        }
    }

    public function testTheDefaultProgramIs74(): void
    {
        $this->assertSame(74, Ferienpass::programId());
    }
}
