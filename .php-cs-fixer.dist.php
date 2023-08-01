<?php

use PhpCsFixer\{
    Config, Finder
};

$finder = Finder::create()
                ->in(__DIR__ . '/src')
                ->name('*.php')
                ->ignoreDotFiles(true)
                ->exclude(['bin','views','out','translations', 'metadata'])
                ->notPath(['src/metadata.php'])
                ->ignoreVCS(true);

$config = new Config();
return $config
    ->setRiskyAllowed(true)
    ->setHideProgress(true)
    ->setUsingCache(false)
    ->setRules([
                   '@PSR2'                        => true,
                   '@PSR12'                       => true,
                   '@PHP74Migration:risky'        => true,
                   '@Symfony'                     => true,
                   'array_syntax'                 => ['syntax' => 'short'],
                   'binary_operator_spaces'       => [
                       'operators' => [
                           '='  => 'align',
                           '=>' => 'align',
                       ]
                   ],
                   'blank_line_after_opening_tag' => true,
                   'line_ending'                  => true,
                   'class_attributes_separation'  => false,
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
                   'nullable_type_declaration_for_default_null_value' => false,
               ])
    ->setFinder($finder);

