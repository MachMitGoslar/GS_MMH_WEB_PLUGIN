<?php

declare(strict_types=1);

namespace GsMmh\WebPlugin;

/**
 * The daily Goslarer Horoskope for the app: the dashboard card and the helpers
 * of the list page (snippets/app/horoscope-list.php). Moved from the site's
 * helpers.php and snippets/content-types/horoskope/.
 */
final class Horoscope
{
    /** Pictures of the twelve Goslar signs (public/assets/pngs/hk_<name>.png of the site). */
    private const CARD_SIGNS = [
        'bergknappe',
        'erzgaenger',
        'fernhaendlerin',
        'haendler',
        'kaiser',
        'kloserschuelerin',
        'muellerin',
        'rammelberg',
        'ratsherr',
        'teichgraefin',
        'weberin',
        'zinngiesser',
    ];

    /**
     * The card for the dashboard: a random sign picture, links to the list page.
     *
     * @return array<string, string>
     */
    public static function card(?int $signIndex = null): array
    {
        $sign = self::CARD_SIGNS[$signIndex ?? random_int(0, count(self::CARD_SIGNS) - 1)];

        return [
            'title' => 'Goslarer Horoskope',
            'description' => 'Deine Sterne über dem Rammelsberg – die tagesaktuellen Horoskope für alle zwölf Goslarer Sternzeichen.',
            'published_at' => date('Y-m-d H:i:s'),
            'image_url' => url('assets/pngs/hk_' . $sign . '.png'),
            'call_to_action_url' => url('app/horoskope'),
        ];
    }

    /**
     * Sorts the signs of the horoscope API by their `order` field
     * (aries → pisces). Since the API started to deliver the five attributes
     * in `order`, an array there counts as 0 and keeps the API's own order.
     *
     * @param array<int, array> $signs
     * @return array<int, array>
     */
    public static function sortSigns(array $signs): array
    {
        $key = static fn (array $sign): int => is_numeric($sign['order'] ?? null) ? (int) $sign['order'] : 0;
        usort($signs, static fn (array $a, array $b): int => $key($a) <=> $key($b));

        return $signs;
    }

    /**
     * The attribute ratings of one sign, in the order of `$labels`, clamped to
     * 0..$max. Read from `attributes`, or from `order` while the API still
     * delivers them there. Attributes the API did not send are left out.
     *
     * @param array<string, string> $labels API field name => label
     * @return array<string, array{label: string, value: int}>
     */
    public static function attributes(array $sign, array $labels, int $max = 8): array
    {
        $source = $sign['attributes'] ?? (is_array($sign['order'] ?? null) ? $sign['order'] : []);
        $attributes = [];

        foreach ($labels as $key => $label) {
            if (!isset($source[$key])) {
                continue;
            }

            $attributes[$key] = ['label' => $label, 'value' => max(0, min($max, (int) $source[$key]))];
        }

        return $attributes;
    }
}
