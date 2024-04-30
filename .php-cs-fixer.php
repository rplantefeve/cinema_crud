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
        /* Ne fonctionne qu'à partir de la version 3 */
        // '@PER-CS' => true,
        // '@PHP82Migration' => true,
        // 'statement_indentation' => false,
        '@PSR1' => true,
        '@PSR12' => true,
        'psr_autoloading' => true,
        
        'array_indentation' => true,
        'array_syntax' => ['syntax' => 'short'],
        'binary_operator_spaces' => [
            'operators' => [
                '=>' => 'align_single_space_minimal',
            ],
        ],
    ])
    ->setFinder($finder);
