<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Translation;

use InvalidArgumentException;
use OpenFGA\Translation\YamlParser;

describe('YamlParser', function (): void {
    test('parses simple key-value pairs', function (): void {
        $yaml = <<<'YAML'
            key1: value1
            key2: value2
            key3: "quoted value"
            YAML;

        $result = YamlParser::parseString($yaml);

        expect($result)->toBe([
            'key1' => 'value1',
            'key2' => 'value2',
            'key3' => 'quoted value',
        ]);
    });

    test('parses nested structures', function (): void {
        $yaml = <<<'YAML'
            parent:
              child1: value1
              child2: value2
            parent2:
              nested:
                deep: value3
            YAML;

        $result = YamlParser::parseString($yaml);

        expect($result)->toBe([
            'parent' => [
                'child1' => 'value1',
                'child2' => 'value2',
            ],
            'parent2' => [
                'nested' => [
                    'deep' => 'value3',
                ],
            ],
        ]);
    });

    test('parses boolean values', function (): void {
        $yaml = <<<'YAML'
            bool_true: true
            bool_false: false
            YAML;

        $result = YamlParser::parseString($yaml);

        expect($result)->toBe([
            'bool_true' => true,
            'bool_false' => false,
        ]);
    });

    test('parses null values', function (): void {
        $yaml = <<<'YAML'
            null_value1: null
            null_value2: ~
            YAML;

        $result = YamlParser::parseString($yaml);

        expect($result)->toBe([
            'null_value1' => null,
            'null_value2' => null,
        ]);
    });

    test('parses numeric values', function (): void {
        $yaml = <<<'YAML'
            integer: 42
            float: 3.14
            negative: -10
            YAML;

        $result = YamlParser::parseString($yaml);

        expect($result)->toBe([
            'integer' => 42,
            'float' => 3.14,
            'negative' => -10,
        ]);
    });

    test('parses quoted strings', function (): void {
        $yaml = <<<'YAML'
            single_quoted: 'value with spaces'
            double_quoted: "another value"
            YAML;

        $result = YamlParser::parseString($yaml);

        expect($result)->toBe([
            'single_quoted' => 'value with spaces',
            'double_quoted' => 'another value',
        ]);
    });

    test('ignores comments and empty lines', function (): void {
        $yaml = <<<'YAML'
            # This is a comment
            key1: value1

            # Another comment
            key2: value2

            YAML;

        $result = YamlParser::parseString($yaml);

        expect($result)->toBe([
            'key1' => 'value1',
            'key2' => 'value2',
        ]);
    });

    test('handles complex nested structure', function (): void {
        $yaml = <<<'YAML'
            messages:
              errors:
                validation: "Validation failed"
                network: "Network error occurred"
              success:
                saved: "Data saved successfully"
            config:
              timeout: 30
              retries: 3
              enabled: true
            YAML;

        $result = YamlParser::parseString($yaml);

        expect($result)->toBe([
            'messages' => [
                'errors' => [
                    'validation' => 'Validation failed',
                    'network' => 'Network error occurred',
                ],
                'success' => [
                    'saved' => 'Data saved successfully',
                ],
            ],
            'config' => [
                'timeout' => 30,
                'retries' => 3,
                'enabled' => true,
            ],
        ]);
    });

    test('handles indentation changes correctly', function (): void {
        $yaml = <<<'YAML'
            level1:
              level2:
                level3: deep_value
              back_to_level2: value
            back_to_level1: value
            YAML;

        $result = YamlParser::parseString($yaml);

        expect($result)->toBe([
            'level1' => [
                'level2' => [
                    'level3' => 'deep_value',
                ],
                'back_to_level2' => 'value',
            ],
            'back_to_level1' => 'value',
        ]);
    });

    test('parses empty string', function (): void {
        $result = YamlParser::parseString('');

        expect($result)->toBe([]);
    });

    test('parses whitespace-only string', function (): void {
        $yaml = "   \n  \n  ";

        $result = YamlParser::parseString($yaml);

        expect($result)->toBe([]);
    });

    test('throws exception for missing colon', function (): void {
        $yaml = <<<'YAML'
            key1: value1
            invalid_line_without_colon
            YAML;

        expect(fn () => YamlParser::parseString($yaml))
            ->toThrow(InvalidArgumentException::class, 'Invalid YAML syntax on line 2: missing colon');
    });

    test('throws exception for empty key', function (): void {
        $yaml = <<<'YAML'
            : value_without_key
            YAML;

        expect(fn () => YamlParser::parseString($yaml))
            ->toThrow(InvalidArgumentException::class, 'Invalid YAML syntax on line 1: empty key');
    });

    test('throws exception for malformed line', function (): void {
        $yaml = <<<'YAML'
            key1: value1
            key2
            YAML;

        expect(fn () => YamlParser::parseString($yaml))
            ->toThrow(InvalidArgumentException::class, 'Invalid YAML syntax on line 2: missing colon');
    });

    test('handles keys with colons in values', function (): void {
        $yaml = <<<'YAML'
            url: "https://example.com:8080"
            message: "Error: something went wrong"
            YAML;

        $result = YamlParser::parseString($yaml);

        expect($result)->toBe([
            'url' => 'https://example.com:8080',
            'message' => 'Error: something went wrong',
        ]);
    });

    test('handles various whitespace scenarios', function (): void {
        $yaml = <<<'YAML'
                spaced_key  :   spaced_value
              another_key:another_value
            YAML;

        $result = YamlParser::parseString($yaml);

        expect($result)->toBe([
            'spaced_key' => 'spaced_value',
            'another_key' => 'another_value',
        ]);
    });

    test('parseFile throws exception for non-existent file', function (): void {
        expect(fn () => YamlParser::parseFile('/non/existent/file.yaml'))
            ->toThrow(InvalidArgumentException::class, 'Cannot read file: /non/existent/file.yaml');
    });

    test('handles mixed indentation correctly', function (): void {
        $yaml = <<<'YAML'
            root:
                level1:
                  level2a: value1
                  level2b: value2
                back_to_level1: value3
            YAML;

        $result = YamlParser::parseString($yaml);

        expect($result)->toBe([
            'root' => [
                'level1' => [
                    'level2a' => 'value1',
                    'level2b' => 'value2',
                ],
                'back_to_level1' => 'value3',
            ],
        ]);
    });

    test('handles empty values correctly', function (): void {
        $yaml = <<<'YAML'
            empty_parent:
              child: value
            empty_value: ""
            YAML;

        $result = YamlParser::parseString($yaml);

        expect($result)->toBe([
            'empty_parent' => [
                'child' => 'value',
            ],
            'empty_value' => '',
        ]);
    });

    test('handles special string values that look like other types', function (): void {
        $yaml = <<<'YAML'
            quoted_true: "true"
            quoted_false: "false"
            quoted_null: "null"
            quoted_number: "123"
            YAML;

        $result = YamlParser::parseString($yaml);

        expect($result)->toBe([
            'quoted_true' => 'true',
            'quoted_false' => 'false',
            'quoted_null' => 'null',
            'quoted_number' => '123',
        ]);
    });
});
