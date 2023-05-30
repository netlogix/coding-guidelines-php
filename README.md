# netlogix coding guidelines for PHP

## Installation
- Install the Composer Package via:
```bash
composer require --dev netlogix/coding-guidelines-php
```

- Import the `CodeStyleSettings.xml` into your PhpStorm IDE using:
<kbd>Settings/Preferences</kbd> > <kbd>Editor</kbd> > <kbd>Code Style</kbd> > <kbd>PHP</kbd> > <kbd>⚙️</kbd> > <kbd>Import Scheme...</kbd>


## Basic configuration
Once installed, add a `ecs.php` file next to your composer.json:

```php
<?php

declare(strict_types=1);

use Netlogix\CodingGuidelines\Php\DefaultPhp;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return static function (ECSConfig $ecsConfig): void {
    (new DefaultPhp())->configure($ecsConfig);

    $ecsConfig->paths(
        [
            __DIR__ . '/src',
        ]
    );
};
```

Add composer scripts for `lint` and `lint-fix`:
```json
{
    "name": "my/package",
    "require-dev": {
        "netlogix/coding-guidelines-php": "@dev"
    },
    "scripts": {
        "lint": "ecs check",
        "lint-fix": "ecs check --fix"
    }
}
```

You can then use `composer run lint` for linting and `composer run lint-fix` to fix issues where possible.

## Configuration for Neos / Flow projects
There is a special ruleset for Neos or Flow projects called `DefaultFlow`. You can simply use that instead of `DefaultPhp` in your `ecs.php`:

```php
<?php

declare(strict_types=1);

use Netlogix\CodingGuidelines\Php\Neos\Flow\DefaultFlow;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return static function (ECSConfig $ecsConfig): void {
    (new DefaultFlow())->configure($ecsConfig);

    $ecsConfig->paths(
        [
            __DIR__ . '/DistributionPackages',
        ]
    );
};
```

## Customizing linting rules
To adjust the linting rules for a specific project, you can use the getters provided by our rulesets in your `ecs.php`:

```php
<?php

declare(strict_types=1);

use Netlogix\CodingGuidelines\Php\DefaultPhp;
use Netlogix\CodingGuidelines\Php\Neos\Flow\DefaultFlow;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return static function (ECSConfig $ecsConfig): void {
    (new DefaultFlow())->configure($ecsConfig);

    // Combine rules of DefaultPhp, DefaultFlow and the FinalClassFixer
    $ecsConfig->rules(
        array_merge(
            DefaultPhp::getRules(),
            DefaultFlow::getRules(),
            [
                \PhpCsFixer\Fixer\ClassNotation\FinalClassFixer::class,
            ]
        )
    );
    
    // Combine skips of DefaultPhp, DefaultFlow and the OrderedClassElementsFixer
    $ecsConfig->skip(
        array_merge(
            DefaultPhp::getSkips(),
            DefaultFlow::getSkips(),
            [
                \PhpCsFixer\Fixer\ClassNotation\OrderedClassElementsFixer::class,
            ]
        )
    );

    $ecsConfig->paths(
        [
            __DIR__ . '/src',
        ]
    );
};

```
