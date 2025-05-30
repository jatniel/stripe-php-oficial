<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
    ->notPath('tests/TestCase.php');

$config = new PhpCsFixer\Config();
$config->setRiskyAllowed(true);
if(PHP_OS == "Darwin") {
    // TODO Enable the parallel runner for CI to speed up formatter when we upgrade to php-cs-fixer v4+
    // PHP CS Fixer's parallel runner currently does not support Linux and is in beta.
    // Only enabling it locally for faster formatting.
    // See: https://github.com/PHP-CS-Fixer/PHP-CS-Fixer/blob/b62538df5cf84c9f1c990e67c04819fbf4c8f285/tests/Runner/RunnerTest.php#L254
    $config->setParallelConfig(PhpCsFixer\Runner\Parallel\ParallelConfigFactory::detect());
}
$config->setRules([
    // Rulesets
    '@PSR2' => true,
    '@PhpCsFixer' => true,
    '@PhpCsFixer:risky' => true,
    '@PHP56Migration:risky' => true,
    '@PHPUnit57Migration:risky' => true,

    // Additional rules
    'fopen_flags' => true,
    'linebreak_after_opening_tag' => true,
    // This one is non-deterministic based on what environment you are running it in and what `get_defined_constants` returns.
    'native_constant_invocation' => false,
    'native_function_invocation' => [
        "strict" => false,
    ],
    // We can remove this after we deprecate PHP 5.6.
    // bug: https://bugs.php.net/bug.php?id=66773
    'fully_qualified_strict_types' => ['import_symbols' => false],

    // --- Diffs from @PhpCsFixer / @PhpCsFixer:risky ---

    // This is the same as the default for the @PhpCsFixer ruleset, minus
    // the following values: ['include', 'include_once', 'require',
    // 'require_once']. We could enable them and remove this line after
    // updating codegen for the `init.php` file to be compliant.
    'blank_line_before_statement' => ['statements' => ['break', 'case', 'continue', 'declare', 'default', 'exit', 'goto', 'return', 'switch', 'throw', 'try']],

    // This is just prettier / easier to read.
    'concat_space' => ['spacing' => 'one'],

    // This causes strange ordering with codegen'd classes. We might be
    // able to enable this if we update codegen to output class elements
    // in the correct order.
    'ordered_class_elements' => false,

    // Keep this disabled to avoid unnecessary diffs in PHPDoc comments of
    // codegen'd classes.
    'phpdoc_align' => false,

    // This is a "risky" rule that causes a bug in our codebase.
    // Specifically, in `StripeObject.updateAttributes` we construct new
    // `StripeObject`s for metadata. We can't use `self` there because it
    // needs to be a raw `StripeObject`.
    'self_accessor' => false,

    // Visibility for constants requires PHP 7.1, but we support PHP 5.6
    'visibility_required' => [
        'elements' => [
            'method',
            'property',
        ],
    ],

    // Apparently "uninitialized" is distinct from "null" in some versions of PHP
    // so I am defensively disabling this rule so as to not cause breaking changes
    // but we can feel free to remove it in a major version (or maybe in a minor if
    // we devote some effort into determining that it is safe)
    'no_null_property_initialization' => false,

    // We have to support `@return void` to satisfy Symfony deprecations helper.
    // See https://github.com/stripe/stripe-php/pull/1230
    'phpdoc_no_empty_return' => false,
]);
$config->setFinder($finder);
return $config;
