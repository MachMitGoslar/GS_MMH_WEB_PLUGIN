<?php

use Kirby\Database\Db;
use Kirby\Toolkit\A;
use tobimori\DreamForm\Actions\Action;

class DatabaseAction extends Action
{
    public static function blueprint(): array
    {
        return [
            'title' => 'Datenbank speichern',
            'preview' => 'fields',
            'wysiwyg' => true,
            'icon' => 'database',
            'fields' => [
                'tableName' => [
                    'label' => 'Tabellenname',
                    'type' => 'text',
                    'placeholder' => 'dreamform_submissions',
                    'width' => '1/2',
                    'help' => 'Name der Datenbanktabelle (Standard: dreamform_submissions)',
                ],
            ],
        ];
    }

    public static function type(): string
    {
        return 'database';
    }

    public function run(): void
    {
        $table = $this->block()->tableName()->or('dreamform_submissions')->value();

        $this->ensureTable($table);

        $values = [];
        foreach ($this->submission()->values()->toArray() as $key => $field) {
            $values[$key] = is_object($field) ? $field->value() : $field;
        }

        try {
            Db::insert($table, [
                'form_slug'    => $this->form()->slug(),
                'form_title'   => $this->form()->title()->value(),
                'data'         => json_encode($values, JSON_UNESCAPED_UNICODE),
                'submitted_at' => date('Y-m-d H:i:s'),
                'referer'      => $this->submission()->referer() ?? '',
            ]);
        } catch (Throwable $e) {
            $this->cancel('Datenbankfehler: ' . $e->getMessage());
        }
    }

    private function ensureTable(string $table): void
    {
        Db::execute("
            CREATE TABLE IF NOT EXISTS `{$table}` (
                `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                `form_slug` VARCHAR(255) NOT NULL,
                `form_title` VARCHAR(255) NOT NULL,
                `data` LONGTEXT NOT NULL,
                `submitted_at` DATETIME NOT NULL,
                `referer` VARCHAR(500) DEFAULT ''
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }
}
