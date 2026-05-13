<?php

namespace GsMmh\WebPlugin;

use Kirby\Database\Db;
use Kirby\Exception\Exception;
use Kirby\Toolkit\V;

class NewsletterRecipients
{
    public const TABLE = 'newsletter_recipients';

    public static function ensureTable(): void
    {
        Db::execute(
            'CREATE TABLE IF NOT EXISTS `' . self::TABLE . '` (
                `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                `first_name` VARCHAR(190) NOT NULL,
                `last_name` VARCHAR(190) NOT NULL,
                `email` VARCHAR(190) NOT NULL,
                `unsubscribe_token` VARCHAR(64) NULL,
                `created_at` DATETIME NOT NULL,
                `updated_at` DATETIME NOT NULL,
                UNIQUE KEY `newsletter_recipients_email_unique` (`email`),
                UNIQUE KEY `newsletter_recipients_unsubscribe_token_unique` (`unsubscribe_token`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
        );

        try {
            Db::execute(
                'ALTER TABLE `' . self::TABLE . '` ADD COLUMN `unsubscribe_token` VARCHAR(64) NULL AFTER `email`'
            );
        } catch (\Throwable $exception) {
            // Column already exists.
        }

        try {
            Db::execute(
                'ALTER TABLE `' . self::TABLE . '` ADD UNIQUE KEY `newsletter_recipients_unsubscribe_token_unique` (`unsubscribe_token`)'
            );
        } catch (\Throwable $exception) {
            // Index already exists.
        }

        self::ensureTokens();
    }

    public static function all(): array
    {
        self::ensureTable();

        $rows = Db::select(self::TABLE, '*', null, 'last_name ASC, first_name ASC, email ASC');
        $recipients = [];

        foreach ($rows as $row) {
            $recipients[] = self::rowToArray($row);
        }

        return $recipients;
    }

    public static function find(int $id): ?array
    {
        self::ensureTable();

        $row = Db::first(self::TABLE, '*', ['id' => $id]);

        return $row ? self::rowToArray($row) : null;
    }

    public static function findByToken(string $token): ?array
    {
        self::ensureTable();

        $token = trim($token);

        if ($token === '') {
            return null;
        }

        $row = Db::first(self::TABLE, '*', ['unsubscribe_token' => $token]);

        return $row ? self::rowToArray($row) : null;
    }

    public static function create(array $data): int
    {
        self::ensureTable();

        $values = self::validate($data);
        $now = date('Y-m-d H:i:s');

        $values['unsubscribe_token'] = self::newToken();
        $values['created_at'] = $now;
        $values['updated_at'] = $now;

        try {
            return (int) Db::insert(self::TABLE, $values);
        } catch (\Throwable $exception) {
            throw new Exception('Diese E-Mail-Adresse ist bereits eingetragen.');
        }
    }

    public static function update(int $id, array $data): void
    {
        self::ensureTable();

        if (self::find($id) === null) {
            throw new Exception('Empfänger nicht gefunden.');
        }

        $values = self::validate($data);
        $values['updated_at'] = date('Y-m-d H:i:s');

        try {
            Db::update(self::TABLE, $values, ['id' => $id]);
        } catch (\Throwable $exception) {
            throw new Exception('Diese E-Mail-Adresse ist bereits eingetragen.');
        }
    }

    public static function delete(int $id): void
    {
        self::ensureTable();
        Db::delete(self::TABLE, ['id' => $id]);
    }

    public static function deleteByEmail(string $email): void
    {
        self::ensureTable();

        $email = strtolower(trim($email));

        if (V::email($email) !== true) {
            throw new Exception('Bitte gib eine gültige E-Mail-Adresse ein.');
        }

        Db::delete(self::TABLE, ['email' => $email]);
    }

    public static function deleteByToken(string $token): bool
    {
        $recipient = self::findByToken($token);

        if ($recipient === null) {
            return false;
        }

        self::delete($recipient['id']);

        return true;
    }

    public static function unsubscribeUrl(array $recipient): string
    {
        $token = (string) ($recipient['unsubscribe_token'] ?? '');

        if ($token === '') {
            $token = self::refreshToken((int) $recipient['id']);
        }

        return mmhAbsoluteUrl('newsletter-abmelden?token=' . rawurlencode($token));
    }

    private static function validate(array $data): array
    {
        $firstName = trim((string) ($data['first_name'] ?? ''));
        $lastName = trim((string) ($data['last_name'] ?? ''));
        $email = strtolower(trim((string) ($data['email'] ?? '')));

        if ($firstName === '') {
            throw new Exception('Bitte gib einen Vornamen ein.');
        }

        if ($lastName === '') {
            throw new Exception('Bitte gib einen Nachnamen ein.');
        }

        if (V::email($email) !== true) {
            throw new Exception('Bitte gib eine gültige E-Mail-Adresse ein.');
        }

        return [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
        ];
    }

    private static function rowToArray(object $row): array
    {
        return [
            'id' => (int) $row->id(),
            'first_name' => $row->first_name(),
            'last_name' => $row->last_name(),
            'email' => $row->email(),
            'unsubscribe_token' => $row->unsubscribe_token(),
            'created_at' => $row->created_at(),
            'updated_at' => $row->updated_at(),
        ];
    }

    private static function ensureTokens(): void
    {
        try {
            $rows = Db::select(
                self::TABLE,
                ['id'],
                '`unsubscribe_token` IS NULL OR `unsubscribe_token` = ""'
            );
        } catch (\Throwable $exception) {
            return;
        }

        foreach ($rows as $row) {
            self::refreshToken((int) $row->id());
        }
    }

    private static function refreshToken(int $id): string
    {
        $token = self::newToken();

        Db::update(
            self::TABLE,
            [
                'unsubscribe_token' => $token,
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            ['id' => $id]
        );

        return $token;
    }

    private static function newToken(): string
    {
        return bin2hex(random_bytes(24));
    }
}
