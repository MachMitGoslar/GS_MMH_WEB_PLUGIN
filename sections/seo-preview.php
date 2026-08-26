<?php

/**
 * Live preview of the link card that networks will render for this page.
 */
return [
    'computed' => [
        'preview' => function (): array {
            return $this->model()->seo()->toArray();
        },
    ],
];
