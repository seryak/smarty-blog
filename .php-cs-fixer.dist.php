<?php

declare(strict_types=1);

$finder = (new PhpCsFixer\Finder())
    ->in([
        __DIR__ . '/app',
        __DIR__ . '/public',
        __DIR__ . '/tests',
    ]);

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setRules([
        '@PSR12' => true,
        'declare_strict_types' => true,
        'array_syntax' => ['syntax' => 'short'],
        'single_quote' => true,
        'no_unused_imports' => true,
        'ordered_imports' => ['sort_algorithm' => 'alpha'],
        'trailing_comma_in_multiline' => true,
        'no_trailing_whitespace' => true,
        'blank_line_after_opening_tag' => true,
        'blank_line_after_namespace' => true,
        'no_extra_blank_lines' => true,
        'single_blank_line_at_eof' => true,
        'method_argument_space' => ['on_multiline' => 'ensure_fully_multiline'],
        'binary_operator_spaces' => ['default' => 'single_space'],
        'concat_space' => ['spacing' => 'one'],
        'not_operator_with_successor_space' => false,
        'phpdoc_align' => ['align' => 'left'],
        'no_empty_phpdoc' => true,
        'no_superfluous_phpdoc_tags' => true,
    ])
    ->setFinder($finder);
