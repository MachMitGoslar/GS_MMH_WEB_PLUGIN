<?php

use PHPUnit\Framework\TestCase;

final class AppApiRoutesTest extends TestCase
{
    private static function routes(): array
    {
        return require dirname(__DIR__, 2) . '/routes/app-api.php';
    }

    public function testRoutesAreTheKnownAppEndpoints(): void
    {
        $patterns = array_column(self::routes(), 'pattern');

        $this->assertEqualsCanonicalizing([
            'newsletter-cover/(:any).jpg',
            'newsletter-cover/(:any).svg',
            'notes-cover/(:any).jpg',
            'notes-cover/(:any).svg',
            'app-cover/(:any).jpg',
            'app-cover/(:any).svg',
            'latest-update',
            'highlights',
            'projects/index.json',
            'projects/(:any)/card.json',
            'projects/(:any)/index.json',
        ], $patterns);
        $this->assertSame($patterns, array_values(array_unique($patterns)), 'each pattern once');
    }

    public function testAppRoutesArePublicReadOnly(): void
    {
        foreach (self::routes() as $route) {
            $this->assertSame('GET', $route['method'], $route['pattern']);
            $this->assertFalse($route['auth'], $route['pattern'] . ' must stay public for the app');
            $this->assertIsCallable($route['action']);
        }
    }

    public function testTheCounterComesFirstSoItSeesEveryAppRequest(): void
    {
        $routes = require dirname(__DIR__, 2) . '/routes/app.php';
        $patterns = array_column($routes, 'pattern');

        // Routes are tried in order; the counter hands off via NextRouteException.
        $this->assertSame([
            '/app/(:any)',
            '/app/horoskop_card',
            '/app/horoskope',
            '/app/ferienpass.json',
            '/app/ferienpass_index.json',
        ], $patterns);
    }
}
