<?php

declare(strict_types=1);

namespace Netlogix\CodingGuidelines\Php\Neos\Flow;

use Netlogix\CodingGuidelines\Php\DefaultPhp;
use PhpCsFixer\Fixer;
use Symplify\CodingStandard\Fixer as SymplifyFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;

final class DefaultFlow
{
    public function configure(ECSConfig $ecsConfig): ECSConfig
    {
        (new DefaultPhp())->configure($ecsConfig);

        $ecsConfig->rules(
            array_merge(
                DefaultPhp::getRules(),
                DefaultFlow::getRules()
            )
        );

        $ecsConfig->skip(
            array_merge(
                DefaultPhp::getSkips(),
                DefaultFlow::getSkips(),
            )
        );

        return $ecsConfig;
    }

    public static function getSkips(): array
    {
        return [
            // FlowAnnotationAwareProtectedToPrivateFixer is used instead
            Fixer\ClassNotation\ProtectedToPrivateFixer::class,
            // This may break Flow Property Mapping
            Fixer\Phpdoc\NoSuperfluousPhpdocTagsFixer::class,
            // Keep default "TODO" comments as they usually indicate there really needs to be done something
            SymplifyFixer\Commenting\RemoveUselessDefaultCommentFixer::class,
        ];
    }

    public static function getSets(): array
    {
        return [];
    }

    public static function getRules(): array
    {
        return [
            FlowAnnotationAwareProtectedToPrivateFixer::class
        ];
    }
}
