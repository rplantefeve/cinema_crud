<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__ . '/src')
    ->in(__DIR__ . '/tests')
    ->name('*.php')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

return (new PhpCsFixer\Config())
    ->setUsingCache(false)
    ->setRiskyAllowed(true)
    ->setRules([
        '@PER-CS' => true,
        '@PHP82Migration' => true,
        '@PSR1' => true,
        '@PSR12' => true,
        'psr_autoloading' => true,
        'statement_indentation' => false,
        'array_indentation' => true,
        'array_syntax' => ['syntax' => 'short'],
        'binary_operator_spaces' => [
            'operators' => [
                '=>' => 'align_single_space_minimal',
            ],
        ],
    ])
    ->setFinder($finder);
