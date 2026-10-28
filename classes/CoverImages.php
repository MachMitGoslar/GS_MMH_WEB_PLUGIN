<?php

declare(strict_types=1);

namespace GsMmh\WebPlugin;

use Kirby\Cms\Response;

/**
 * Cover images for the app API (newsletter, notes, app teasers): SVG and a
 * cached JPEG rendered from it. Moved here from the site's
 * controllers/api-images.php; the routes live in the site's api config.
 *
 * Reads the logo from the site's `assets/svg/` (index root) and caches the
 * JPEGs in `media/api-covers/`.
 */
final class CoverImages
{
    public static function jpegImageUrl($file): ?string
    {
        if (!$file) {
            return null;
        }

        try {
            return $file->thumb([
                'width' => 1200,
                'height' => 630,
                'crop' => true,
                'format' => 'jpg',
                'quality' => 88,
            ])->url();
        } catch (\Throwable) {
            $extension = strtolower($file->extension());

            return in_array($extension, ['jpg', 'jpeg'], true) ? $file->url() : null;
        }
    }

    public static function coverMeta(string $kind, string $slug): ?array
    {
        if ($kind === 'newsletter') {
            $page = page('newsletter/' . $slug);

            return $page ? [
                'kind' => $kind,
                'slug' => $slug,
                'label' => 'Newsletter',
                'title' => $page->title()->value(),
                'colors' => ['#5d4e37', '#6b5b47', '#4a3c28'],
            ] : null;
        }

        if ($kind === 'notes') {
            $page = page('notes/' . $slug);

            return $page ? [
                'kind' => $kind,
                'slug' => $slug,
                'label' => 'Tagebuch',
                'title' => $page->title()->value(),
                'colors' => ['#39556b', '#46677f', '#2d475a'],
            ] : null;
        }

        if ($kind === 'app' && $slug === 'whatsapp-community') {
            return [
                'kind' => $kind,
                'slug' => $slug,
                'label' => 'WhatsApp Community',
                'title' => 'Tritt unserer WhatsApp Community bei.',
                'colors' => ['#245f53', '#1f514d', '#183d4f'],
            ];
        }

        return null;
    }

    public static function coverFileUrl(string $kind, string $slug): string
    {
        $routes = [
            'newsletter' => 'newsletter-cover',
            'notes' => 'notes-cover',
            'app' => 'app-cover',
        ];

        return url('api/' . ($routes[$kind] ?? $kind . '-cover') . '/' . $slug . '.jpg');
    }

    public static function xmlEscape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_XML1, 'UTF-8');
    }

    public static function wrapSvgText(string $text, int $maxLength = 42): array
    {
        $words = preg_split('/\s+/', trim($text)) ?: [];
        $lines = [];
        $line = '';

        foreach ($words as $word) {
            $test = trim($line . ' ' . $word);

            if ($line !== '' && mb_strlen($test) > $maxLength) {
                $lines[] = $line;
                $line = $word;
            } else {
                $line = $test;
            }
        }

        if ($line !== '') {
            $lines[] = $line;
        }

        return array_slice($lines, 0, 2);
    }

    public static function coverFileSlug(string $kind, string $slug): string
    {
        return preg_replace('/[^a-zA-Z0-9._-]+/', '-', $kind . '-' . $slug);
    }

    public static function coverJpegPath(string $kind, string $slug): string
    {
        return \kirby()->root('media') . '/api-covers/' . self::coverFileSlug($kind, $slug) . '.jpg';
    }

    public static function coverJpegHashPath(string $kind, string $slug): string
    {
        return \kirby()->root('media') . '/api-covers/' . self::coverFileSlug($kind, $slug) . '.sha256';
    }

    public static function coverLogoSvg(): ?string
    {
        $logo = \kirby()->root('index') . '/assets/svg/RZ-RGB_MM!2_iv.svg';

        if (!is_file($logo)) {
            return null;
        }

        $document = new \DOMDocument();

        if (!$document->load($logo)) {
            return null;
        }

        $logoSvg = $document->documentElement;

        if (!$logoSvg || strtolower($logoSvg->tagName) !== 'svg') {
            return null;
        }

        $viewBox = preg_split('/[\s,]+/', trim($logoSvg->getAttribute('viewBox'))) ?: [];

        if (count($viewBox) !== 4) {
            return null;
        }

        [, , $viewBoxWidth, $viewBoxHeight] = array_map('floatval', $viewBox);

        if ($viewBoxWidth <= 0 || $viewBoxHeight <= 0) {
            return null;
        }

        $scale = min(280 / $viewBoxWidth, 256 / $viewBoxHeight);
        $x = 460 + ((280 - ($viewBoxWidth * $scale)) / 2);
        $y = 105 + ((256 - ($viewBoxHeight * $scale)) / 2);

        foreach ($document->getElementsByTagName('*') as $element) {
            if (!$element instanceof DOMElement) {
                continue;
            }

            $class = $element->getAttribute('class');

            if ($class === 'st0') {
                $element->setAttribute('fill', '#ffffff');
            } elseif ($class === 'st1') {
                $element->setAttribute('fill', '#b09c40');
            }

            $element->removeAttribute('class');
        }

        $logoContent = '';

        foreach ($logoSvg->childNodes as $child) {
            if ($child instanceof DOMElement && strtolower($child->tagName) === 'defs') {
                continue;
            }

            $logoContent .= $document->saveXML($child);
        }

        return sprintf(
            '<g transform="translate(%s %s) scale(%s)">%s</g>',
            rtrim(rtrim(sprintf('%.4F', $x), '0'), '.'),
            rtrim(rtrim(sprintf('%.4F', $y), '0'), '.'),
            rtrim(rtrim(sprintf('%.6F', $scale), '0'), '.'),
            $logoContent,
        );
    }

    public static function coverJpegHash(string $svg): string
    {
        return hash('sha256', $svg . '|imagick-composited-gradient-v1');
    }

    public static function coverSvg(string $kind, string $slug, bool $includeBackground = true): ?string
    {
        $meta = self::coverMeta($kind, $slug);
        $logo = self::coverLogoSvg();

        if (!$meta || !$logo) {
            return null;
        }

        $label = self::xmlEscape($meta['label']);
        $titleLines = self::wrapSvgText($meta['title']);
        $titleSvg = '';
        $titleY = 500;

        foreach ($titleLines as $line) {
            $titleSvg .= '<text x="600" y="' . $titleY . '" text-anchor="middle"'
                . ' font-family="Arial, Helvetica, sans-serif" font-size="28" font-weight="700"'
                . ' fill="#f4efe8">'
                . self::xmlEscape($line)
                . '</text>';
            $titleY += 38;
        }

        $colors = array_map([self::class, 'xmlEscape'], $meta['colors']);
        $background = $includeBackground ? <<<SVG
  <defs>
    <linearGradient id="background" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0%" stop-color="{$colors[0]}"/>
      <stop offset="50%" stop-color="{$colors[1]}"/>
      <stop offset="100%" stop-color="{$colors[2]}"/>
    </linearGradient>
  </defs>
  <rect width="1200" height="630" fill="url(#background)"/>
SVG : '';

        $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="1200" height="630" viewBox="0 0 1200 630"
  role="img" aria-label="{$label}">
  {$background}
  {$logo}
  <text x="600" y="441" text-anchor="middle" font-family="Arial, Helvetica, sans-serif"
    font-size="60" font-weight="700" fill="#ffffff">{$label}</text>
  {$titleSvg}
</svg>
SVG;

        return $svg;
    }

    public static function hexToRgb(string $hex): ?array
    {
        $hex = ltrim($hex, '#');

        if (!preg_match('/^[a-fA-F0-9]{6}$/', $hex)) {
            return null;
        }

        return [
            hexdec(substr($hex, 0, 2)),
            hexdec(substr($hex, 2, 2)),
            hexdec(substr($hex, 4, 2)),
        ];
    }

    public static function mixRgb(array $from, array $to, float $amount): array
    {
        $amount = max(0, min(1, $amount));

        return [
            (int) round($from[0] + (($to[0] - $from[0]) * $amount)),
            (int) round($from[1] + (($to[1] - $from[1]) * $amount)),
            (int) round($from[2] + (($to[2] - $from[2]) * $amount)),
        ];
    }

    public static function rgbColor(array $rgb): string
    {
        return sprintf('rgb(%d,%d,%d)', $rgb[0], $rgb[1], $rgb[2]);
    }

    public static function coverGradientImage(array $colors): ?\Imagick
    {
        $start = self::hexToRgb((string) ($colors[0] ?? ''));
        $middle = self::hexToRgb((string) ($colors[1] ?? ''));
        $end = self::hexToRgb((string) ($colors[2] ?? ''));

        if (!$start || !$middle || !$end) {
            return null;
        }

        $image = new \Imagick();
        $image->newImage(1200, 630, new \ImagickPixel('#000000'), 'png');
        $image->setImageColorspace(\Imagick::COLORSPACE_SRGB);

        $iterator = $image->getPixelIterator();

        foreach ($iterator as $y => $pixels) {
            foreach ($pixels as $x => $pixel) {
                $offset = (($x / 1199) + ($y / 629)) / 2;
                $rgb = $offset <= 0.5
                    ? self::mixRgb($start, $middle, $offset * 2)
                    : self::mixRgb($middle, $end, ($offset - 0.5) * 2);

                $pixel->setColor(self::rgbColor($rgb));
            }

            $iterator->syncIterator();
        }

        return $image;
    }

    public static function coverSvgResponse(string $kind, string $slug): Response
    {
        $svg = self::coverSvg($kind, $slug);

        if (!$svg) {
            return new Response('Not found', 'text/plain', 404);
        }

        return new Response([
            'body' => $svg,
            'type' => 'image/svg+xml',
            'headers' => [
                'Cache-Control' => 'public, max-age=3600',
            ],
            'charset' => 'utf-8',
        ]);
    }

    public static function svgToJpeg(string $svg, array $colors): ?string
    {
        if (!class_exists('Imagick')) {
            return null;
        }

        try {
            $image = self::coverGradientImage($colors);

            if (!$image) {
                return null;
            }

            $overlay = new \Imagick();
            $overlay->setResolution(144, 144);
            $overlay->setBackgroundColor(new \ImagickPixel('transparent'));
            $overlay->readImageBlob($svg);
            $overlay->setImageBackgroundColor(new \ImagickPixel('transparent'));
            $overlay = $overlay->mergeImageLayers(\Imagick::LAYERMETHOD_MERGE);

            if ($overlay->getImageWidth() !== 1200 || $overlay->getImageHeight() !== 630) {
                $overlay->resizeImage(1200, 630, \Imagick::FILTER_LANCZOS, 1, true);
                $overlay->extentImage(1200, 630, 0, 0);
            }

            $image->compositeImage($overlay, \Imagick::COMPOSITE_OVER, 0, 0);
            $image->setImageFormat('jpeg');
            $image->setImageCompressionQuality(88);

            return $image->getImagesBlob();
        } catch (\Throwable) {
            return null;
        } finally {
            if (isset($overlay) && $overlay instanceof \Imagick) {
                $overlay->clear();
                $overlay->destroy();
            }

            if (isset($image) && $image instanceof \Imagick) {
                $image->clear();
                $image->destroy();
            }
        }
    }

    public static function generateCoverJpeg(string $kind, string $slug, string $svg): bool
    {
        $meta = self::coverMeta($kind, $slug);
        $foregroundSvg = self::coverSvg($kind, $slug, false);

        if (!$meta || !$foregroundSvg) {
            return false;
        }

        $target = self::coverJpegPath($kind, $slug);
        $directory = dirname($target);

        if (!is_dir($directory) && mkdir($directory, 0775, true) === false) {
            return false;
        }

        $temporaryTarget = tempnam($directory, 'cover-');

        if ($temporaryTarget === false) {
            return false;
        }

        $jpeg = self::svgToJpeg($foregroundSvg, $meta['colors']);

        if (!is_string($jpeg) || substr($jpeg, 0, 3) !== "\xff\xd8\xff") {
            @unlink($temporaryTarget);

            return false;
        }

        if (file_put_contents($temporaryTarget, $jpeg, LOCK_EX) === false) {
            @unlink($temporaryTarget);

            return false;
        }

        if (!rename($temporaryTarget, $target)) {
            @unlink($temporaryTarget);

            return false;
        }

        file_put_contents(self::coverJpegHashPath($kind, $slug), self::coverJpegHash($svg), LOCK_EX);

        return true;
    }

    public static function coverJpegResponse(string $kind, string $slug): Response
    {
        $svg = self::coverSvg($kind, $slug);

        if (!$svg) {
            return new Response('Not found', 'text/plain', 404);
        }

        $file = self::coverJpegPath($kind, $slug);
        $hashFile = self::coverJpegHashPath($kind, $slug);
        $hash = self::coverJpegHash($svg);
        $cached = is_file($file)
            && is_file($hashFile)
            && trim((string) file_get_contents($hashFile)) === $hash;

        if (!$cached && !self::generateCoverJpeg($kind, $slug, $svg)) {
            return new Response(
                'Cover could not be rasterized. Check that the \Imagick PHP extension is installed.',
                'text/plain',
                500,
            );
        }

        $jpeg = file_get_contents($file);

        if (ob_get_level() > 0) {
            ob_clean();
        }

        return new Response([
            'body' => $jpeg,
            'type' => 'image/jpeg',
            'headers' => [
                'Content-Length' => strlen($jpeg),
                'Cache-Control' => 'public, max-age=3600',
            ],
            'charset' => '',
        ]);
    }
}
