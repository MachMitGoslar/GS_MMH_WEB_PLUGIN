<?php

declare(strict_types=1);

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$finder = Finder::create()
    ->in([
        __DIR__ . '/areas',
        __DIR__ . '/controllers',
        __DIR__ . '/snippets',
        __DIR__ . '/templates',
    ])
    ->append([
        __DIR__ . '/index.php',
        __DIR__ . '/DatabaseAction.php',
    ])
    ->name('*.php')
    ->exclude([
        'vendor',
        'node_modules',
    ]);

return (new Config())
    ->setRiskyAllowed(true)
    ->setRules([
        // Base standard
        '@PSR12' => true,

        // Modern PHP syntax
        'array_syntax' => ['syntax' => 'short'],
        'list_syntax' => ['syntax' => 'short'],
        'short_scalar_cast' => true,
        'modernize_types_casting' => true,

        // Imports
        'ordered_imports' => ['sort_algorithm' => 'alpha'],
        'no_unused_imports' => true,
        'no_leading_import_slash' => true,
        'global_namespace_import' => ['import_classes' => false, 'import_constants' => false, 'import_functions' => false],

        // Spacing & formatting
        'binary_operator_spaces' => ['default' => 'single_space'],
        'unary_operator_spaces' => true,
        'cast_spaces' => ['space' => 'single'],
        'concat_space' => ['spacing' => 'one'],
        'trailing_comma_in_multiline' => ['elements' => ['arrays', 'arguments', 'parameters']],
        'no_extra_blank_lines' => ['tokens' => ['extra', 'throw', 'use']],
        'no_whitespace_in_blank_line' => true,

        // Blank lines
        'blank_line_before_statement' => [
            'statements' => ['break', 'continue', 'declare', 'return', 'throw', 'try'],
        ],
        'blank_line_after_namespace' => true,

        // Strings
        'single_quote' => ['strings_containing_single_quote_chars' => false],

        // PHPDoc
        'phpdoc_scalar' => true,
        'phpdoc_single_line_var_spacing' => true,
        'phpdoc_var_without_name' => true,
        'phpdoc_trim' => true,
        'no_empty_phpdoc' => true,
    ])
    ->setFinder($finder);