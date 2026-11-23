<?php

declare(strict_types=1);

namespace LeibnizPsychology\PhpDocAiTlp;

use phpDocumentor\Descriptor\Builder\AssemblerFactory;
use phpDocumentor\Extension\Extension as BaseExtension;
use phpDocumentor\Reflection\DocBlockFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class Extension extends BaseExtension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $container->register('ai_tlp.docblock_factory', DocBlockFactoryInterface::class)
            ->setDecoratedService(DocBlockFactoryInterface::class)
            ->setFactory([Registration::class, 'docBlockFactory'])
            ->setArguments([new Reference('ai_tlp.docblock_factory.inner')]);

        $container->register('ai_tlp.assembler_factory', AssemblerFactory::class)
            ->setDecoratedService(AssemblerFactory::class)
            ->setFactory([Registration::class, 'assemblerFactory'])
            ->setArguments([new Reference('ai_tlp.assembler_factory.inner')]);
    }
}
