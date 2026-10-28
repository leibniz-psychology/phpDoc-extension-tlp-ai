<?php

declare(strict_types=1);

namespace LeibnizPsychology\PhpDocAiTlp;

use LeibnizPsychology\PhpDocAiTlp\Tag\AiTlpFactory;
use LeibnizPsychology\PhpDocAiTlp\Tag\AiTlpTag;
use phpDocumentor\Descriptor\Builder\Reflector\Tags\TagAssembler;
use phpDocumentor\Descriptor\DocBlock\DescriptionDescriptor;
use phpDocumentor\Descriptor\ProjectDescriptorBuilder;
use phpDocumentor\Reflection\DocBlock\Tag as ReflectionTag;
use phpDocumentor\Reflection\DocBlock\TagFactory;

class Extension
{
    public function __construct(
        private ProjectDescriptorBuilder $builder,
        private TagFactory $tagFactory
    ) {
    }

    public function load(): void
    {
        // 1. Register the custom tag with the DocBlock Reflection library
        // This allows phpDocumentor to recognize @ai-tlp during the reflection phase
        $this->tagFactory->registerTagHandler('ai-tlp', AiTlpFactory::class);

        // 2. Register an assembler to convert the Reflection Tag into a Descriptor
        // This puts it into the AST that Twig can access.
        $this->builder->getAssemblerFactory()->register(
            fn($tag) => $tag instanceof AiTlpFactory,
            new class extends TagAssembler {
                public function create(ReflectionTag $data): AiTlpTag
                {
                    /** @var AiTlpFactory $data */
                    $tag = new AiTlpTag($data->getName(), $data->getLevel());
                    $tag->setDescription(new DescriptionDescriptor($data->getDescription(), []));
                    return $tag;
                }
            }
        );
    }
}
