<?php

declare(strict_types=1);

namespace LeibnizPsychology\PhpDocAiTlp\Validator;

use InvalidArgumentException;

class AiTlpValidator
{
    public const LEVEL_WHITE = 'white';
    public const LEVEL_GREEN = 'green';
    public const LEVEL_AMBER = 'amber';
    public const LEVEL_ORANGE = 'orange';
    public const LEVEL_RED = 'red';
    public const LEVEL_YOLO = 'yolo';

    private const ALLOWED_LEVELS = [
        self::LEVEL_WHITE,
        self::LEVEL_GREEN,
        self::LEVEL_AMBER,
        self::LEVEL_ORANGE,
        self::LEVEL_RED,
        self::LEVEL_YOLO,
    ];

    private const MAPPING = [
        'ai:white' => self::LEVEL_WHITE,
        'ai:green' => self::LEVEL_GREEN,
        'ai:amber' => self::LEVEL_AMBER,
        'ai:orange' => self::LEVEL_ORANGE,
        'ai:red' => self::LEVEL_RED,
        'ai:yolo' => self::LEVEL_YOLO,
    ];

    /**
     * Validates and normalizes the AI-TLP level.
     *
     * @param string $level
     * @return string Normalized level (e.g., 'amber')
     * @throws InvalidArgumentException If the level is invalid.
     */
    public static function validate(string $level): string
    {
        $normalized = strtolower(trim($level));

        if (isset(self::MAPPING[$normalized])) {
            return self::MAPPING[$normalized];
        }

        if (in_array($normalized, self::ALLOWED_LEVELS, true)) {
            return $normalized;
        }

        throw new InvalidArgumentException(sprintf(
            'Invalid AI-TLP level: "%s". Allowed levels are: %s',
            $level,
            implode(', ', array_merge(self::ALLOWED_LEVELS, array_keys(self::MAPPING)))
        ));
    }

    /**
     * Returns the human-readable label for a normalized level.
     */
    public static function getLabel(string $level): string
    {
        return 'AI:' . strtoupper($level);
    }
}
