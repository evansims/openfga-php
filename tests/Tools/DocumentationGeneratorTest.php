<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Tools;

use OpenFGA\Tools\DocumentationGenerator;
use ReflectionClass;

require_once implode(DIRECTORY_SEPARATOR, [__DIR__, '..', '..', 'tools', 'docs', 'generate.php']);

test('method signature prints default values using PHP syntax', function (): void {
    final class DocumentationGeneratorTest
    {
        public function foo(string $bar = 'baz', array $data = [1, 2]): void
        {
        }
    }

    $generator = new DocumentationGenerator(__DIR__, __DIR__);
    $method = (new ReflectionClass(DocumentationGeneratorTest::class))->getMethod('foo');

    $signatureMethod = (new ReflectionClass(DocumentationGenerator::class))
        ->getMethod('getMethodSignature');
    $signatureMethod->setAccessible(true);
    $signature = $signatureMethod->invoke($generator, $method);

    expect($signature)
        ->toContain("= 'baz'")
        ->and($signature)->toContain('array')
        ->and($signature)->toContain('0 => 1');
});
