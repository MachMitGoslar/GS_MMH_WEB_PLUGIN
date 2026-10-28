<?php

use PHPUnit\Framework\TestCase;

/** The plugin must not call functions of the website (the website may not be there). */
final class IndependenceFromSiteTest extends TestCase
{
    public function testThePluginDoesNotCallSiteFunctions(): void
    {
        $files = glob(dirname(__DIR__, 2) . '/{classes,areas,routes}/*.php', GLOB_BRACE);

        $this->assertNotEmpty($files);

        foreach ($files as $file) {
            $this->assertDoesNotMatchRegularExpression(
                '/\bmmh(?:AbsoluteUrl|NewsletterMobileHtml|Version|BackLink|ColorContrast)\(/',
                (string) file_get_contents($file),
                basename($file) . ' calls a function of the website',
            );
        }
    }
}
