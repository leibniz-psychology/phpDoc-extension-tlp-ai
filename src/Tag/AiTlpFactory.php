<?php

declare(strict_types=1);

namespace LeibnizPsychology\PhpDocAiTlp\Tag;

use phpDocumentor\Descriptor\Builder\Reflector\AssemblerAbstract;
use phpDocumentor\Reflection\DocBlock\Tag;
use phpDocumentor\Reflection\DocBlock\Tags\BaseTag;
use phpDocumentor\Reflection\DocBlock\Tags\Factory\StaticMethod;
use Webmozart\Assert\Assert;

class AiTlpFactory extends BaseTag implements StaticMethod
{
    protected string $level;

    public function __construct(string $level, ?string $description = null)
    {
        $this->name = 'ai-tlp';
        $this->level = $level;
        $this->description = $description;
    }

    public static function create(string $body): self
    {
        $parts = preg_split('/\s+/', trim($body), 2);
        $level = $parts[0] ?? '';
        $description = $parts[1] ?? null;

        return new self($level, $description);
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
