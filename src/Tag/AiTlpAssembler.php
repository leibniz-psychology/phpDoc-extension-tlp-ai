<?php

declare(strict_types=1);

namespace LeibnizPsychology\PhpDocAiTlp\Tag;

use phpDocumentor\Descriptor\Builder\Reflector\Tags\BaseTagAssembler;

/** @extends BaseTagAssembler<AiTlpTag, AiTlpFactory> */
final class AiTlpAssembler extends BaseTagAssembler
{
    protected function buildDescriptor(object $data): AiTlpTag
    {
        return new AiTlpTag($data->getName(), $data->getLevel());
    }
}
