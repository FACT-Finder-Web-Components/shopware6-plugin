<?php

declare(strict_types=1);

use PhpCsFixer\Config;

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__ . '/src')
    ->name('*.php')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

return (new Config())
    ->setRiskyAllowed(true)
    ->setHideProgress(true)
    ->setUsingCache(false)
    ->setRules([
        '@PSR2'                  => true,
        '@Symfony'               => true,
        'array_syntax'           => ['syntax' => 'short'],
        'binary_operator_spaces' => [
            'operators' => ['=>' => 'align', '==' => 'align'],
        ],
//        'binary_operator_spaces' => ['align_double_arrow' => true, 'align_equals' => true],
        'blank_line_after_opening_tag' => true,
        'blank_line_before_statement'  => ['statements' => ['break', 'continue', 'declare', 'throw', 'try']],
        'concat_space'                 => ['spacing' => 'one'],
        'declare_strict_types'         => true,
        'increment_style'              => ['style' => 'post'],
        'no_superfluous_phpdoc_tags'   => false,
        'no_useless_else'              => true,
        'no_useless_return'            => true,
        'ordered_class_elements'       => true,
        'ordered_imports'              => ['imports_order' => ['class', 'function', 'const']],
        'strict_comparison'            => true,
        'yoda_style'                   => ['equal' => false, 'identical' => false, 'less_and_greater' => false],
    ])
    ->setFinder($finder)
    ;
