<?php

namespace GsMmh\WebPlugin;

use Kirby\Exception\Exception;
use tobimori\DreamForm\Actions\Action;

class NewsletterRecipientAction extends Action
{
    public static function blueprint(): array
    {
        return [
            'name' => 'Newsletter-Empfänger speichern',
            'preview' => 'fields',
            'wysiwyg' => false,
            'icon' => 'email',
        ];
    }

    public static function type(): string
    {
        return 'newsletter-recipient';
    }

    public function run(): void
    {
        $values = $this->submission()->values();
        $data = [
            'first_name' => $values->get('firstname')->value(),
            'last_name' => $values->get('lastname')->value(),
            'email' => $values->get('email')->value(),

        ];

        try {
            if (NewsletterRecipients::findByEmail((string) $data['email']) !== null) {
                $this->log([
                    'text' => 'E-Mail-Adresse ist bereits als Newsletter-Empfänger eingetragen.',
                ], type: 'info', icon: 'email', title: 'Newsletter-Empfänger');
                $this->cancel("E-Mail-Adresse ist bereits als Newsletter-Empfänger eingetragen.", true);
                return;
            }

            $data['unsubscribe_token'] = bin2hex(random_bytes(16));
            NewsletterRecipients::create($data);
            $this->log([
                'text' => 'Newsletter-Empfänger wurde gespeichert.',
            ], type: 'success', icon: 'email', title: 'Newsletter-Empfänger');
        } catch (Exception $exception) {
            $this->cancel($exception->getMessage(), public: true);
        }
    }
}
