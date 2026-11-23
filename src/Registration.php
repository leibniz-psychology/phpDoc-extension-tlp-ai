<?php

declare(strict_types=1);

namespace LeibnizPsychology\PhpDocAiTlp;

use LeibnizPsychology\PhpDocAiTlp\Tag\AiTlpAssembler;
use LeibnizPsychology\PhpDocAiTlp\Tag\AiTlpFactory;
use LogicException;
use phpDocumentor\Descriptor\Builder\AssemblerFactory;
use phpDocumentor\Descriptor\Builder\Matcher;
use phpDocumentor\Reflection\DocBlockFactory;
use phpDocumentor\Reflection\DocBlockFactoryInterface;

final class Registration
{
    public static function docBlockFactory(DocBlockFactoryInterface $factory): DocBlockFactoryInterface
    {
        if (!$factory instanceof DocBlockFactory) {
            throw new LogicException('AI-TLP requires phpDocumentor\'s standard DocBlock factory.');
        }

        $factory->registerTagHandler('ai-tlp', AiTlpFactory::class);
        return $factory;
    }

    public static function assemblerFactory(AssemblerFactory $factory): AssemblerFactory
    {
        // phpDocumentor's register() PHPDoc is invariant despite accepting specialized assemblers.
        $factory->register(
            Matcher::forType(AiTlpFactory::class), // @phpstan-ignore argument.type
            new AiTlpAssembler() // @phpstan-ignore argument.type
        );
        return $factory;
    }
}
