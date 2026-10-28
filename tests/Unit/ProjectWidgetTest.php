<?php

use GsMmh\WebPlugin\ProjectWidget;
use Kirby\Cms\App;
use PHPUnit\Framework\TestCase;

final class ProjectWidgetTest extends TestCase
{
    protected function setUp(): void
    {
        $step = fn (string $slug, string $title, string $date, array $more = []) => [
            'slug' => $slug,
            'template' => 'project_step',
            'num' => 1,
            'content' => [
                'title' => $title,
                'project_start_date' => $date,
                'description' => '<p>Text <strong>zu</strong> ' . $title . '</p>',
                'uuid' => 'uuid-' . $slug,
            ] + $more,
        ];

        new App([
            'roots' => [
                'index' => sys_get_temp_dir() . '/mmh-widget-phpunit',
                'base' => sys_get_temp_dir() . '/mmh-widget-phpunit',
                'content' => sys_get_temp_dir() . '/mmh-widget-phpunit/content',
            ],
            'urls' => ['index' => 'https://mmh.test'],
            'site' => [
                'children' => [
                    [
                        'slug' => 'projects',
                        'num' => 1,
                        'children' => [
                            [
                                'slug' => 'wald',
                                'template' => 'project',
                                'num' => 1,
                                'content' => ['title' => 'Wald', 'subheadline' => 'Baeume', 'app_widget' => 'true'],
                                'children' => [
                                    $step('zweiter', 'Zweiter Schritt', '2026-05-02', ['project_start_time' => '14:30']),
                                    $step('erster', 'Erster Schritt', '2026-04-01'),
                                    array_merge($step('entwurf', 'Entwurf', '2026-06-01'), ['num' => null, 'draft' => true]),
                                ],
                            ],
                            [
                                'slug' => 'privat',
                                'template' => 'project',
                                'num' => 2,
                                'content' => ['title' => 'Privat', 'app_widget' => 'false'],
                            ],
                            [
                                'slug' => 'leer',
                                'template' => 'project',
                                'num' => 3,
                                'content' => ['title' => 'Leer', 'app_widget' => 'true'],
                            ],
                        ],
                    ],
                ],
            ],
        ]);
    }

    public function testOnlyReleasedProjectsAreAvailable(): void
    {
        $this->assertNotNull(ProjectWidget::project('wald'));
        $this->assertNull(ProjectWidget::project('privat'), 'toggle off');
        $this->assertNull(ProjectWidget::project('gibt-es-nicht'));
    }

    public function testStepsAreChronologicalAndSkipDrafts(): void
    {
        $steps = ProjectWidget::steps(ProjectWidget::project('wald'));

        $this->assertSame(['erster', 'zweiter'], $steps->pluck('slug'));
    }

    public function testCardDescribesTheLatestUpdateAndPointsToTheIndex(): void
    {
        $card = ProjectWidget::card(ProjectWidget::project('wald'));

        $this->assertSame('Wald', $card['title']);
        $this->assertSame('Text zu Zweiter Schritt', $card['description']);
        $this->assertSame('https://mmh.test/api/projects/wald/index.json', $card['call_to_action_url']);
        $this->assertSame('2026-05-02T14:30', $card['published_at']);
        $this->assertNull($card['widget_type']);
        $this->assertNull($card['image_url'], 'no cover in the fixture');
    }

    public function testParagraphsDoNotRunTogetherInDescriptions(): void
    {
        $page = new Kirby\Cms\Page([
            'slug' => 'mehrere-absaetze',
            'content' => ['description' => '<p>Erster Absatz.</p><p>Zweiter<br>Absatz.</p>', 'uuid' => 'x'],
        ]);
        $method = new ReflectionMethod(ProjectWidget::class, 'stepDescription');

        $this->assertSame('Erster Absatz. Zweiter Absatz.', $method->invoke(null, $page));
    }

    public function testCardOfAProjectWithoutUpdatesUsesTheSubheadline(): void
    {
        $card = ProjectWidget::card(ProjectWidget::project('leer'));

        $this->assertSame('Leer', $card['title']);
        $this->assertSame('', $card['description']);
    }

    public function testIndexListsUpdatesNewestFirstInTheAppFormat(): void
    {
        $project = ProjectWidget::project('wald');
        $index = ProjectWidget::index($project);

        $this->assertSame(['Zweiter Schritt', 'Erster Schritt'], array_column($index, 'title'));
        $this->assertSame($project->url(), $index[0]['call_to_action_url']);
        $this->assertSame('2026-04-01T00:00', $index[1]['published_at']);

        foreach ($index as $entry) {
            $this->assertSame(
                ['id', 'title', 'description', 'image_url', 'call_to_action_url', 'published_at'],
                array_keys($entry),
            );
            $this->assertIsInt($entry['id']);
            $this->assertGreaterThan(0, $entry['id']);
        }
    }

    public function testIdsAreStableAndDistinct(): void
    {
        $project = ProjectWidget::project('wald');
        $ids = array_column(ProjectWidget::index($project), 'id');

        $this->assertCount(2, array_unique($ids));
        $this->assertSame($ids, array_column(ProjectWidget::index($project), 'id'));
        $this->assertSame(crc32('uuid-zweiter') & 0x7FFFFFFF, $ids[0]);
    }

    public function testProjectOverviewListsOnlyReleasedProjects(): void
    {
        $slugs = array_map(
            fn ($entry) => basename($entry['call_to_action_url']),
            ProjectWidget::projects(),
        );

        // Sorted by the newest update; a project without update counts by its last change.
        $this->assertEqualsCanonicalizing(['wald', 'leer'], $slugs);
        $this->assertNotContains('privat', $slugs);
    }
}
