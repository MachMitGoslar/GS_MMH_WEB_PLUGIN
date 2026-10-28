<?php

declare(strict_types=1);

namespace GsMmh\WebPlugin;

use Kirby\Cms\Page;

/**
 * Entries of DreamForm forms: DreamForm stores each new submission as a draft
 * below the form page; the site hides it right after (status "unlisted"), so
 * the panel area for submissions shows it and the public site does not.
 */
final class FormEntries
{
    /**
     * The newest submission draft of a form, or null.
     */
    public static function newest(Page $form): ?Page
    {
        return $form->drafts()
            ->filterBy('intendedTemplate', 'submission')
            ->sortBy('created', 'desc')
            ->first();
    }

    /**
     * Hook `dreamform.submitted:after`: draft entries become unlisted.
     */
    public static function unlistNewest(?Page $form): void
    {
        if (!$form) {
            return;
        }

        $entry = self::newest($form);

        if ($entry && $entry->status() === 'draft') {
            $entry->changeStatus('unlisted');
        }
    }
}
