<?php

/**
 * DreamForm is not a Composer dependency of this plugin. Locally the field classes load
 * straight from the sibling plugin (classes only, Kirby plugins are never booted);
 * without it (e.g. CI) minimal stand-ins with the same parent behaviour are used.
 */

$dreamFormClasses = dirname(__DIR__, 3) . '/kirby-dreamform/classes';

if (is_dir($dreamFormClasses)) {
    spl_autoload_register(static function (string $class) use ($dreamFormClasses): void {
        $prefix = 'tobimori\\DreamForm\\';

        if (str_starts_with($class, $prefix)) {
            $file = $dreamFormClasses . '/' . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';

            if (is_file($file)) {
                require_once $file;
            }
        }
    });

    return;
}

require __DIR__ . '/dreamform-fields.php';
