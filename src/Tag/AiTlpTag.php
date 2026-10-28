<?php

declare(strict_types=1);

namespace LeibnizPsychology\PhpDocAiTlp\Tag;

use phpDocumentor\Descriptor\TagDescriptor;
use LeibnizPsychology\PhpDocAiTlp\Validator\AiTlpValidator;

class AiTlpTag extends TagDescriptor
{
    private string $level;

    public function __construct(string $name, string $level, ?string $description = null)
    {
        parent::__construct($name);
        $this->level = AiTlpValidator::validate($level);
        if ($description !== null) {
            $this->setDescription($description);
        }
    }

    public function getLevel(): string
    {
        return $this->level;
    }

    public function getFullLabel(): string
    {
        return AiTlpValidator::getLabel($this->level);
    }
}
