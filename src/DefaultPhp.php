<?php

declare(strict_types=1);

namespace Netlogix\CodingGuidelines\Php;

use PhpCsFixer\Fixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

final class DefaultPhp
{
    public function configure(ECSConfig $ecsConfig): ECSConfig
    {
        $ecsConfig->lineEnding("\n");
        $ecsConfig->skip(DefaultPhp::getSkips());
        $ecsConfig->sets(DefaultPhp::getSets());
        $ecsConfig->rules(DefaultPhp::getRules());

        return $ecsConfig;
    }

    public static function getSkips(): array
    {
        return [
            Fixer\Operator\NotOperatorWithSuccessorSpaceFixer::class,
        ];
    }

    public static function getSets(): array
    {
        return [
            SetList::PSR_12,
            SetList::CLEAN_CODE,
            SetList::DOCTRINE_ANNOTATIONS,
            SetList::SPACES,
            SetList::ARRAY,
            SetList::DOCBLOCK,
            SetList::SYMPLIFY,
        ];
    }

    public static function getRules(): array
    {
        return [
            Fixer\Whitespace\BlankLineBeforeStatementFixer::class,
            Fixer\Strict\DeclareStrictTypesFixer::class,
            Fixer\Strict\StrictComparisonFixer::class,
            Fixer\Strict\StrictParamFixer::class,
            Fixer\Import\GlobalNamespaceImportFixer::class,
        ];
    }
}
