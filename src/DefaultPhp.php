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
        $ecsConfig->skip([
            Fixer\Operator\NotOperatorWithSuccessorSpaceFixer::class,
        ]);

        $ecsConfig->lineEnding("\n");

        $ecsConfig->sets([
            SetList::PSR_12,
            SetList::CLEAN_CODE,
            SetList::DOCTRINE_ANNOTATIONS,
            SetList::SPACES,
            SetList::ARRAY,
            SetList::DOCBLOCK,
            SetList::SYMPLIFY,
        ]);

        $ecsConfig->rule(Fixer\Whitespace\BlankLineBeforeStatementFixer::class);
        $ecsConfig->rule(Fixer\Strict\DeclareStrictTypesFixer::class);
        $ecsConfig->rule(Fixer\Strict\StrictComparisonFixer::class);
        $ecsConfig->rule(Fixer\Strict\StrictParamFixer::class);
        $ecsConfig->rule(Fixer\Import\GlobalNamespaceImportFixer::class);

        return $ecsConfig;
    }

}
