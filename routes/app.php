<?php

/**
 * Routes of the app under /app/: horoscope card and list page, request
 * counter, Ferienpass events. (The JSON endpoints under /api/ are in
 * app-api.php.)
 *
 * The counter hands off to the next matching route via NextRouteException,
 * so it only sees requests that no route above it has answered: the
 * horoscope routes sit before it and are not counted. Kept as it was.
 */

use GsMmh\WebPlugin\AppRequests;
use GsMmh\WebPlugin\Ferienpass;
use GsMmh\WebPlugin\Horoscope;
use Kirby\Cms\Response;
use Kirby\Http\Exceptions\NextRouteException;

$json = static fn (array $payload): Response => new Response(
    json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
    'application/json',
);

return [
    /**
     * Horoscope Card API
     * Returns the daily Goslarer Horoskope as a JSON app-card payload.
     */
    [
        'pattern' => '/app/horoskop_card',
        'action' => fn () => $json(Horoscope::card()),
    ],

    /**
     * Horoskope List Page
     * Renders the daily Goslarer Horoskope as an HTML list with
     * collapsible texts per zodiac sign.
     */
    [
        'pattern' => '/app/horoskope',
        'action' => fn () => new Response(snippet('app/horoscope-list', [], true), 'text/html'),
    ],

    /**
     * App Request Tracking
     * Increments a per-URL/per-day counter for any /app/* request, then
     * hands the request off to the next matching route.
     */
    [
        'pattern' => '/app/(:any)',
        'action' => function ($any) {
            AppRequests::count($any);

            throw new NextRouteException();
        },
    ],

    /**
     * Ferienpass Events API - Random Event
     */
    [
        'pattern' => '/app/ferienpass.json',
        'action' => function () use ($json) {
            $programId = Ferienpass::programId();
            $result = Ferienpass::fetch($programId);

            return $json(isset($result['error'])
                ? $result
                : Ferienpass::randomEvent($result['events'], $programId));
        },
    ],

    /**
     * Ferienpass Events API - All Events
     */
    [
        'pattern' => '/app/ferienpass_index.json',
        'action' => function () use ($json) {
            $programId = Ferienpass::programId();
            $result = Ferienpass::fetch($programId);

            return $json(isset($result['error'])
                ? $result
                : Ferienpass::eventList($result['events'], $programId));
        },
    ],
];
