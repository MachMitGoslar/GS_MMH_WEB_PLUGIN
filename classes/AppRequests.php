<?php

declare(strict_types=1);

namespace GsMmh\WebPlugin;

use Kirby\Database\Db;

/**
 * Counts requests to /app/* per URL and day (table `app_requests`). Best
 * effort: a database error must never break an endpoint the app depends on.
 */
final class AppRequests
{
    public static function count(string $url): void
    {
        try {
            Db::execute('CREATE TABLE IF NOT EXISTS `app_requests` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `url` varchar(255) NOT NULL,
                `day` date NOT NULL,
                `requests` int(11) NOT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');

            $data = [
                'url' => $url,
                'day' => date('Y-m-d'),
            ];

            if ($request = Db::first('app_requests', '*', $data)) {
                $data['requests'] = $request->requests() + 1;
                Db::update('app_requests', $data, [
                    'url' => $data['url'],
                    'day' => $data['day'],
                ]);
            } else {
                $data['requests'] = 1;
                Db::insert('app_requests', $data);
            }
        } catch (\Throwable) {
            // tracking is best-effort; never break the actual endpoint
        }
    }
}
