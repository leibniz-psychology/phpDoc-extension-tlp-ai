<?php

declare(strict_types=1);

namespace LeibnizPsychology\PhpDocAiTlp\Tests\Validator;

use LeibnizPsychology\PhpDocAiTlp\Validator\AiTlpValidator;
use PHPUnit\Framework\TestCase;
use InvalidArgumentException;

class AiTlpValidatorTest extends TestCase
{
    /**
     * @dataProvider validLevelProvider
     */
    public function testValidateValidLevels(string $input, string $expected): void
    {
        $this->assertEquals($expected, AiTlpValidator::validate($input));
    }

    public static function validLevelProvider(): array
    {
        return [
            ['white', 'white'],
            ['GREEN', 'green'],
            [' AI:AMBER ', 'amber'],
            ['ai:orange', 'orange'],
            ['red', 'red'],
            ['yolo', 'yolo'],
            ['AI:WHITE', 'white'],
        ];
    }

    public function testValidateInvalidLevel(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid AI-TLP level: "blue"');
        AiTlpValidator::validate('blue');
    }

    public function testGetLabel(): void
    {
        $this->assertEquals('AI:AMBER', AiTlpValidator::getLabel('amber'));
    }
}
