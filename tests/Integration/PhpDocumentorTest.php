<?php

declare(strict_types=1);

namespace LeibnizPsychology\PhpDocAiTlp\Tests\Integration;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

final class PhpDocumentorTest extends TestCase
{
    public function testComposerDiscoveryParsingAndRendering(): void
    {
        $phar = getenv('PHPDOC_PHAR');
        if ($phar === false || $phar === '') {
            self::markTestSkipped('Set PHPDOC_PHAR to the phpDocumentor 3.10 PHAR.');
        }

        self::assertFileExists($phar);
        $root = dirname(__DIR__, 2);
        $directory = sys_get_temp_dir() . '/ai-tlp-' . bin2hex(random_bytes(8));
        $filesystem = new Filesystem();
        $filesystem->mkdir([$directory . '/src', $directory . '/.phpdoc/template/components']);

        try {
            $composer = [
                'name' => 'example/ai-tlp-test',
                'repositories' => [
                    [
                        'type' => 'path',
                        'url' => $root,
                        'options' => ['versions' => ['leibniz-psychology/phpdoc-extension-tlp-ai' => 'dev-test']],
                    ],
                    ['packagist.org' => false],
                ],
                'require-dev' => ['leibniz-psychology/phpdoc-extension-tlp-ai' => 'dev-test'],
            ];
            $filesystem->dumpFile(
                $directory . '/composer.json',
                json_encode($composer, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT)
            );
            $filesystem->copy($root . '/examples/phpdoc.dist.xml', $directory . '/phpdoc.dist.xml');
            $filesystem->copy(
                $root . '/templates/overrides/components/tags.html.twig',
                $directory . '/.phpdoc/template/components/tags.html.twig'
            );
            $filesystem->dumpFile($directory . '/src/Example.php', <<<'PHP'
                <?php
                namespace Demo;
                /**
                 * @ai-tlp AI:AMBER Reviewed **carefully**.
                 * @ai-tlp green
                 * @ai-tlp white
                 * @ai-tlp red
                 * @ai-tlp yolo
                 * @since 1.2.3
                 */
                class Example {
                    /** @ai-tlp blue Bad level. */
                    public function invalid(): void {}
                    /** @ai-tlp orange */
                    public function valid(): void {}
                }
                PHP);

            $this->runCommand(
                ['composer', 'install', '--no-interaction', '--no-plugins', '--no-scripts', '--no-progress'],
                $directory
            );
            $firstRun = $this->runCommand([PHP_BINARY, $phar, '--no-progress', '--force'], $directory);
            self::assertStringContainsString('leibniz-psychology/phpdoc-extension-tlp-ai', $firstRun);
            $this->assertGeneratedDocumentation($directory);

            $this->runCommand([PHP_BINARY, $phar, '--no-progress'], $directory);
            $this->assertGeneratedDocumentation($directory);
        } finally {
            $filesystem->remove($directory);
        }
    }

    private function runCommand(array $command, string $directory): string
    {
        $process = new Process($command, $directory, ['XDEBUG_MODE' => 'off']);
        $process->setTimeout(120);
        $process->run();
        self::assertTrue($process->isSuccessful(), $process->getOutput() . $process->getErrorOutput());
        return $process->getOutput();
    }

    private function assertGeneratedDocumentation(string $directory): void
    {
        $html = file_get_contents($directory . '/build/docs/classes/Demo-Example.html');
        self::assertIsString($html);
        self::assertStringContainsString('phpdocumentor-tag-ai-tlp--amber', $html);
        self::assertStringContainsString('AI:AMBER', $html);
        self::assertStringContainsString('Reviewed <strong>carefully</strong>', $html);
        self::assertStringContainsString('phpdocumentor-tag-ai-tlp--green', $html);
        self::assertStringContainsString('phpdocumentor-tag-ai-tlp--white', $html);
        self::assertStringContainsString('phpdocumentor-tag-ai-tlp--orange', $html);
        self::assertStringContainsString('phpdocumentor-tag-ai-tlp--red', $html);
        self::assertStringContainsString('phpdocumentor-tag-ai-tlp--yolo', $html);
        self::assertStringContainsString('1.2.3', $html);
        self::assertStringNotContainsString('phpdocumentor-tag-ai-tlp--blue', $html);

        $report = file_get_contents($directory . '/build/docs/reports/errors.html');
        self::assertIsString($report);
        self::assertStringContainsString('Invalid AI-TLP level: &quot;blue&quot;', $report);
    }
}
