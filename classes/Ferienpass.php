<?php

declare(strict_types=1);

namespace GsMmh\WebPlugin;

use Kirby\Http\Remote;

/**
 * Ferienpass events (goslar.feripro.de) as app cards: one random event for
 * the dashboard and the list of all events. Moved from the site's snippets
 * content-types/ferienpass/. Errors are reported as `['error' => …]` with
 * HTTP 200, as before.
 */
final class Ferienpass
{
    public const DEFAULT_PROGRAM = 74;

    /** The program id from `?data=`, 74 by default. */
    public static function programId(): int
    {
        return (int) kirby()->request()->query()->get('data') ?: self::DEFAULT_PROGRAM;
    }

    /**
     * The events of a program, sorted by start, or an error payload.
     *
     * @return array{events?: array<int, array>, error?: string}
     */
    public static function fetch(int $programId): array
    {
        try {
            $json = Remote::get('https://goslar.feripro.de/api/programs/' . $programId . '/events/')->json();
        } catch (\Exception $e) {
            error_log('Error fetching events: ' . $e->getMessage());

            return ['error' => 'Error fetching events'];
        }

        if (!is_array($json) || !isset($json[0]['start'])) {
            error_log('Invalid JSON response from API');

            return ['error' => 'Invalid response from API'];
        }

        // TODO: Exclude events that are already gone
        usort($json, fn ($a, $b) => strnatcmp($a['start'], $b['start']));

        return ['events' => $json];
    }

    /**
     * One event as the dashboard card; `$index` picks it (random by default).
     *
     * @param array<int, array> $events sorted events
     */
    public static function randomEvent(array $events, int $programId, ?int $index = null): array
    {
        $event = $events[$index ?? random_int(0, count($events) - 1)];

        return [
            'title' => $event['name'],
            'description' => '<strong>' . $event['name'] . '</strong> - ' . $event['description'],
            'published_at' => $event['start'],
            'image_url' => 'https://jugend.goslar.de/fileadmin/user_upload/website/jugendpflege/goslar_app/anriss.png',
            'call_to_action_url' => 'https://mmh.goslar.de/app/ferienpass_index.json?data=' . $programId,
        ];
    }

    /**
     * All events in the index format.
     *
     * @param array<int, array> $events sorted events
     * @return list<array<string, mixed>>
     */
    public static function eventList(array $events, int $programId): array
    {
        $list = [];

        foreach (array_values($events) as $id => $event) {
            $list[] = [
                'id' => $id,
                'title' => $event['name'],
                'description' => $event['duration_summary'] . ' ' . $event['description'],
                'published_at' => $event['start'],
                'image_url' => $event['cover_photo']
                    ? $event['cover_photo']['thumbnail']
                    : 'https://jugend.goslar.de/fileadmin/_processed_/3/1/csm_Post_a87fefbd79.png',
                'call_to_action_url' => 'https://goslar.feripro.de/anmeldung/' . $programId . '/veranstaltungen/' . $event['event_id'],
            ];
        }

        return $list;
    }
}
