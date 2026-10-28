<?php

declare(strict_types=1);

namespace GsMmh\WebPlugin;

use Kirby\Cms\Pages;
use Kirby\Toolkit\Str;

/**
 * "Latest update" of the site for the app API: the newest newsletter or
 * project step, as the widget/highlight payload. Moved here from the site's
 * controllers/latest-update.php; the routes live in the site's api config.
 */
final class LatestUpdate
{
    /**
     * Alle neuesten Updates sammeln (Newsletter + Projekt-Steps)
     */
    public static function all(): Pages
    {
        // Newsletter sammeln
        $newsletters = page('newsletter')
            ?->children()
            ->listed()
            ?? new Pages([]);

        // Projekt-Steps sammeln
        $projektSteps = new Pages([]);
        $projects = page('projects')?->children()->listed() ?? new Pages([]);

        foreach ($projects as $project) {
            $steps = $project->children()->listed() ?? new Pages([]);

            $projektSteps = $projektSteps->merge($steps);
        }

        // Steps nach project_start_date absteigend sortieren
        $projektSteps = $projektSteps->sortBy(
            fn ($step) => $step->project_start_date()->isNotEmpty()
                ? strtotime($step->project_start_date()->value())
                : 0,
            'desc',
        );

        // Newsletter + ProjektSteps zusammenführen
        $all = $newsletters->merge($projektSteps);

        return $all;
    }

    /**
     * Timestamp für Sortierung bestimmen
     */
    public static function timestamp($p): int
    {
        if ($p->published()->isNotEmpty()) {
            $ts = strtotime($p->published()->value());

            return $ts;
        }

        if ($p->project_start_date()->isNotEmpty()) {
            $ts = strtotime($p->project_start_date()->value());

            return $ts;
        }

        return self::timestampValue($p->modified());
    }

    public static function timestampValue($value): int
    {
        if (is_int($value)) {
            return $value;
        }

        if (is_numeric($value)) {
            return (int) $value;
        }

        if (is_object($value) && method_exists($value, 'toTimestamp')) {
            return (int) $value->toTimestamp();
        }

        return time();
    }

    /**
     * Das neueste Update ermitteln
     */
    public static function latest()
    {
        $all = self::all();

        if ($all->isEmpty()) {
            return null;
        }

        $sorted = $all->sortBy(fn ($p) => self::timestamp($p), 'desc');

        return $sorted->first();
    }

    /**
     * Ein Update in API-Response-Format umwandeln
     */
    public static function toArray($update, bool $for_highlights_link = false): ?array
    {
        if (!$update) {
            return null;
        }

        $isNewsletter = $update->intendedTemplate()->name() === 'newsletter';
        $timestamp = self::timestamp($update);

        // Bild bestimmen
        if ($isNewsletter) {
            $image_url = CoverImages::coverFileUrl('newsletter', $update->slug());
        } else {
            $project = $update->parent();
            $coverFile = $project->content()->get('cover')?->toFile();
            $image_url = CoverImages::jpegImageUrl($coverFile);
        }

        // 🔹 URL bestimmen
        if ($for_highlights_link) {
            $call_to_action_url = url('api/highlights'); // Link auf Highlights
        } else {
            $call_to_action_url = $isNewsletter
                ? $update->url()
                : $update->parent()->url();
        }

        $description = $update->description()->isNotEmpty()
            ? $update->description()->value()
            : $update->text()->excerpt(160)->value();

        //Remove remaining html tags from description string
        $description = Str::unhtml($description);

        $title = $update->title()->value();

        if ($isNewsletter) {
            $description = $title;
        }

        return [
            'title' => 'MachMit!Haus',
            'description' => $description,
            'image_url' => $image_url,
            'call_to_action_url' => $call_to_action_url,
            'published_at' => date('Y-m-d\TH:i', $timestamp),
            'widget_type' => null,
        ];
    }

    /**
     * Komfortfunktion:
     * Liefert direkt das neueste Update als Array
     */
    public static function data(bool $forHighlightsLink = false): ?array
    {
        return self::toArray(self::latest(), $forHighlightsLink);
    }
}
