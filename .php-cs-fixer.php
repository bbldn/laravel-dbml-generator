<?php

$finder = PhpCsFixer\Finder::create()->exclude(['vendor'])->in(__DIR__);

$config = new PhpCsFixer\Config();
$config->setFinder($finder);
$config->setCacheFile(__DIR__ . '/vendor/.php-cs-fixer.cache');

return $config->setRules([
    '@Symfony' => true,
    'cast_spaces' => false,
    'phpdoc_separation' => false,
    'phpdoc_to_comment' => false,
    'single_line_throw' => false,
    'phpdoc_align' => ['align' => 'left'],
    'no_superfluous_phpdoc_tags' => false,
    'concat_space' => ['spacing' => 'one'],
    'array_syntax' => ['syntax' => 'short'],
    'global_namespace_import' => ['import_classes' => true],
    'function_declaration' => ['closure_fn_spacing' => 'none'],
    'nullable_type_declaration_for_default_null_value' => false,
    'class_definition' => ['multi_line_extends_each_single_line' => true],
    'ordered_imports' => [
        'sort_algorithm' => 'length',
        'imports_order' => ['class', 'function', 'const'],
    ],
    'no_unneeded_control_parentheses' => [
        'statements' => [
            'break', 'clone', 'continue', 'echo_print', 'others', 'switch_case', 'yield', 'yield_from'
        ],
    ],
]);
