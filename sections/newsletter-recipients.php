<?php

use GsMmh\WebPlugin\NewsletterRecipients;

return [
    'computed' => [
        'recipients' => function (): array {
            return NewsletterRecipients::all();
        },
        'total' => function (): int {
            return count(NewsletterRecipients::all());
        },
    ],
];
