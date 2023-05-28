<?php

$rules = [
    '@Symfony' => true,
    'native_function_invocation' => ['include' => ['@compiler_optimized'], 'scope' => 'namespaced'],
    'no_superfluous_phpdoc_tags' => ['allow_mixed' => true],
    'phpdoc_to_comment' => false,
    'phpdoc_align' => false,
    'php_unit_method_casing' => false,
    'phpdoc_separation' => ['groups' => [['test', 'dataProvider']]],
];

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
;

$config = new PhpCsFixer\Config();
return $config
    ->setFinder($finder)
    ->setRiskyAllowed(true)
    ->setRules($rules)
    ->setUsingCache(true)
;
