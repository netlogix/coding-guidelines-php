<?php

declare(strict_types=1);

namespace Netlogix\CodingGuidelines\Php\Neos\Flow;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Fixer\ClassNotation\ProtectedToPrivateFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TokensAnalyzer;

final class FlowAnnotationAwareProtectedToPrivateFixer extends AbstractFixer
{

    private ProtectedToPrivateFixer $protectedToPrivateFixer;

    public function __construct()
    {
        parent::__construct();

        $this->protectedToPrivateFixer = new ProtectedToPrivateFixer();
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinition() : FixerDefinitionInterface
    {
        return new FixerDefinition('Converts `protected` variables and methods to `private` where possible (respects @Flow and @ORM annotations or attributes).', [new CodeSample('<?php
use Neos\Flow\Annotations as Flow;
use Doctrine\ORM\Mapping as ORM;

final class Sample
{
    protected $a;

    /**
     * @var Some\Service
     * @Flow\Inject
    */
    protected $b;

    /**
     * @var int
     * @ORM\Column(nullable=true)
    */
    protected $b;

    #[Flow\Inject]
    protected Some\Service $c;

    protected function test()
    {
    }
}
')]);
    }

    public function isCandidate(Tokens $tokens): bool
    {
        if (!$this->protectedToPrivateFixer->isCandidate($tokens)) {
            return false;
        }

        return true;
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        $tokensAnalyzer = new TokensAnalyzer($tokens);
        $tokensToKeep = [];

        foreach ($tokensAnalyzer->getClassyElements() as $index => $element) {
            if (!in_array($element['type'], ['property', 'method'], true)) {
                continue;
            }

            $protectedIndex = $this->getPrevToken($index, [\T_PROTECTED], $tokens);
            if ($protectedIndex === null) {
                continue;
            }

            if ($element['type'] === 'method') {
                $nextMeaningful = $tokens[$tokens->getNextMeaningfulToken($index)];
                assert($nextMeaningful instanceof Token);

                if (in_array($nextMeaningful->getContent(), ['__construct'], true)) {
                    $tokensToKeep[] = [
                        'index' => $index,
                        'protected' => $protectedIndex,
                    ];

                    continue;
                }
            }

            if (PHP_MAJOR_VERSION >= 8) {
                $attributeIndex = $this->getPrevToken($index, [\T_ATTRIBUTE], $tokens);
                if ($attributeIndex !== null) {
                    $attribute = $tokens[$attributeIndex];

                    $nextMeaningfulIndex = $tokens->getNextMeaningfulToken($attributeIndex);
                    $nextMeaningful = $tokens[$nextMeaningfulIndex];

                    assert($nextMeaningful instanceof Token);
                    if (in_array($nextMeaningful->getContent(), ['Flow', 'ORM'], true)) {
                        $tokensToKeep[] = [
                            'index' => $index,
                            'protected' => $protectedIndex,
                        ];
                        continue;
                    }
                }
            }

            $docCommentIndex = $this->getPrevToken($protectedIndex, [\T_COMMENT, \T_DOC_COMMENT], $tokens);
            if ($docCommentIndex === null) {
                continue;
            }
            $docComment = $tokens[$docCommentIndex];

            assert($docComment instanceof Token);
            if (strpos($docComment->getContent(), '@Flow\\') !== false
                || strpos($docComment->getContent(), '@ORM\\') !== false) {
                $tokensToKeep[] = [
                    'index' => $index,
                    'protected' => $protectedIndex,
                ];
            }
        }

        $modifiersToRestore = [];
        foreach ($tokensToKeep as $token) {
            $modifiersToRestore[$token['protected']] = $tokens[$token['protected']];
        }

        $this->protectedToPrivateFixer->fix($file, $tokens);

        foreach ($modifiersToRestore as $index => $token) {
            $tokens[$index] = $token;
        }
    }

    private function getPrevToken(int $index, array $types, Tokens $tokens): ?int
    {
        $previous = $index;

        do {
            $previous = $tokens->getPrevNonWhitespace($previous);
            $previousToken = $tokens[$previous];
            assert($previousToken instanceof Token);

            if ($previousToken->isGivenKind($types)) {
                return $previous;
            }
        } while (!$previousToken->isGivenKind([\T_CLASS, \T_TRAIT, 312/*312 = Property*/]));

        return null;
    }

}
