<?php

declare(strict_types=1);

namespace GsMmh\WebPlugin;

use Kirby\Cms\Page;
use Kirby\Cms\Pages;
use Kirby\Toolkit\Str;

/**
 * Project updates for the dashboard widget of the Goslar app.
 *
 * A project that has the toggle `app_widget` switched on offers two JSON
 * documents in the app card format (docs/APP-API.md):
 *
 * - card: the "entry view", a single card for the dashboard. Its
 *   `call_to_action_url` points to the project's index.
 * - index: the "index view", the project's published updates (project_step
 *   pages), newest first. Each entry opens the project page in the webview.
 *
 * `projects()` lists the released projects in the same index format.
 */
final class ProjectWidget
{
    /** Length of the description excerpt, in characters. */
    private const EXCERPT = 160;

    /**
     * The project behind a slug, but only when it is released for the app.
     */
    public static function project(string $slug): ?Page
    {
        $page = page('projects/' . $slug);

        return $page !== null && self::isReleased($page) ? $page : null;
    }

    public static function isReleased(Page $project): bool
    {
        return $project->intendedTemplate()->name() === 'project'
            && $project->content()->get('app_widget')->toBool();
    }

    /**
     * All released projects, newest update first.
     */
    public static function releasedProjects(): Pages
    {
        $projects = page('projects')?->children()->listed() ?? new Pages([]);

        return $projects
            ->filter(fn ($project) => self::isReleased($project))
            ->sortBy(fn ($project) => self::latestTimestamp($project), 'desc');
    }

    /**
     * Published updates of a project, oldest first.
     */
    public static function steps(Page $project): Pages
    {
        return $project->children()->listed()->sortBy(
            fn ($step) => self::stepTimestamp($step),
            'asc',
        );
    }

    /**
     * The dashboard card ("entry view").
     */
    public static function card(Page $project): array
    {
        $latest = self::steps($project)->last();
        $description = $latest !== null
            ? self::stepDescription($latest)
            : self::text($project->content()->get('subheadline'));

        return [
            'title' => $project->title()->value(),
            'description' => $description,
            'image_url' => self::coverUrl($project),
            'call_to_action_url' => url('api/projects/' . $project->slug() . '/index.json'),
            'published_at' => self::format(self::latestTimestamp($project)),
            'widget_type' => null,
        ];
    }

    /**
     * The updates of a project ("index view"), newest first.
     *
     * @return list<array<string, mixed>>
     */
    public static function index(Page $project): array
    {
        $entries = [];

        foreach (self::steps($project)->flip() as $step) {
            $entries[] = [
                'id' => self::id($step),
                'title' => self::text($step->content()->get('headline')) ?: $step->title()->value(),
                'description' => self::stepDescription($step),
                'image_url' => self::stepImageUrl($step) ?? self::coverUrl($project),
                'call_to_action_url' => $project->url(),
                'published_at' => self::format(self::stepTimestamp($step) ?: (int) $step->modified()),
            ];
        }

        return $entries;
    }

    /**
     * The released projects in the index format, for a project overview.
     *
     * @return list<array<string, mixed>>
     */
    public static function projects(): array
    {
        $entries = [];

        foreach (self::releasedProjects() as $project) {
            $entries[] = [
                'id' => self::id($project),
                'title' => $project->title()->value(),
                'description' => self::text($project->content()->get('subheadline')),
                'image_url' => self::coverUrl($project),
                'call_to_action_url' => $project->url(),
                'published_at' => self::format(self::latestTimestamp($project)),
            ];
        }

        return $entries;
    }

    /**
     * Start of an update: date plus optional time. 0 without a date.
     */
    public static function stepTimestamp(Page $step): int
    {
        $date = trim((string) $step->content()->get('project_start_date')->value());

        if ($date === '') {
            return 0;
        }

        $time = trim((string) $step->content()->get('project_start_time')->value()) ?: '00:00';

        return strtotime($date . ' ' . $time) ?: 0;
    }

    /**
     * Newest update of a project, or the last change of the project itself
     * when it has none.
     */
    public static function latestTimestamp(Page $project): int
    {
        $latest = self::steps($project)->last();

        return $latest !== null ? self::stepTimestamp($latest) : (int) $project->modified();
    }

    /**
     * A stable positive integer per page. Entries keep their id when other
     * updates are added or removed (a position in the list would not).
     */
    public static function id(Page $page): int
    {
        $uuid = (string) $page->content()->get('uuid')->value();

        return crc32($uuid !== '' ? $uuid : $page->id()) & 0x7FFFFFFF;
    }

    private static function stepDescription(Page $step): string
    {
        $text = self::text($step->content()->get('description'));

        return $text !== '' ? $text : self::text($step->content()->get('headline'));
    }

    /**
     * Plain text of a field, shortened to the excerpt length.
     */
    private static function text($field): string
    {
        // Block ends and line breaks become spaces, so paragraphs do not run together.
        $html = (string) preg_replace('~</(?:p|div|li|h[1-6])>|<br\s*/?>~i', ' ', (string) $field->value());
        $text = trim(Str::unhtml($html));
        $text = (string) preg_replace('/\s+/u', ' ', $text);

        return Str::short($text, self::EXCERPT, '…');
    }

    private static function coverUrl(Page $project): ?string
    {
        return CoverImages::jpegImageUrl($project->content()->get('cover')->toFile());
    }

    private static function stepImageUrl(Page $step): ?string
    {
        return CoverImages::jpegImageUrl($step->content()->get('image')->toFile());
    }

    private static function format(int $timestamp): string
    {
        return date('Y-m-d\TH:i', $timestamp);
    }
}
