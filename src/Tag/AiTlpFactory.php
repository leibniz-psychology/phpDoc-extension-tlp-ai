<?php

declare(strict_types=1);

namespace LeibnizPsychology\PhpDocAiTlp\Tag;

use LeibnizPsychology\PhpDocAiTlp\Validator\AiTlpValidator;
use phpDocumentor\Reflection\DocBlock\Description;
use phpDocumentor\Reflection\DocBlock\DescriptionFactory;
use phpDocumentor\Reflection\DocBlock\Tags\BaseTag;
use phpDocumentor\Reflection\DocBlock\Tags\Factory\StaticMethod;
use phpDocumentor\Reflection\Types\Context;

class AiTlpFactory extends BaseTag implements StaticMethod
{
    protected string $level;

    public function __construct(string $level, ?Description $description = null)
    {
        $this->name = 'ai-tlp';
        $this->level = $level;
        $this->description = $description;
    }

    public static function create(
        string $body,
        ?DescriptionFactory $descriptionFactory = null,
        ?Context $context = null
    ): self {
        $parts = preg_split('/\s+/', trim($body), 2);
        $level = AiTlpValidator::validate($parts[0] ?? '');
        $description = $parts[1] ?? null;

        return new self(
            $level,
            $descriptionFactory && $description !== null
                ? $descriptionFactory->create($description, $context)
                : null
        );
    }

    public function getLevel(): string
    {
        return $this->level;
    }

    public function __toString(): string
    {
        return $this->level . ($this->description ? ' ' . $this->description : '');
    }
}
