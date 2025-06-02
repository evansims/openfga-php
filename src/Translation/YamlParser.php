<?php

declare(strict_types=1);

namespace OpenFGA\Translation;

use InvalidArgumentException;

use function count;
use function explode;
use function file_get_contents;
use function ltrim;
use function strlen;
use function substr;
use function trim;

/**
 * Simple YAML parser for translation files.
 *
 * This lightweight YAML parser handles the specific structure used by OpenFGA
 * translation files. It supports basic key-value pairs, nested structures,
 * and simple comments. It does not support advanced YAML features like
 * anchors, references, or complex data types.
 *
 * The parser is optimized for the simple, flat-ish structure of translation
 * files and provides better performance than a full YAML library for this
 * specific use case.
 */
final class YamlParser
{
    /**
     * Parse a YAML file and return the structured data.
     *
     * @param string $filename Path to the YAML file to parse
     *
     * @throws InvalidArgumentException If the file cannot be read or parsed
     *
     * @return array<string, mixed> The parsed YAML data
     */
    public static function parseFile(string $filename): array
    {
        $content = @file_get_contents($filename);

        if (false === $content) {
            throw new InvalidArgumentException('Cannot read file: ' . $filename);
        }

        return self::parseString($content);
    }

    /**
     * Parse a YAML string and return the structured data.
     *
     * @param string $yamlString The YAML content to parse
     *
     * @throws InvalidArgumentException If the YAML cannot be parsed
     *
     * @return array<string, mixed> The parsed YAML data
     *
     * @psalm-suppress UnsupportedReferenceUsage
     * @psalm-suppress MixedAssignment
     * @psalm-suppress MixedReturnStatement
     */
    public static function parseString(string $yamlString): array
    {
        $lines = explode("\n", $yamlString);

        /** @var array<string, mixed> $result */
        $result = [];

        /** @var array<array<string, mixed>> $stack */
        $stack = [&$result];
        $indentStack = [0];

        foreach ($lines as $lineNumber => $line) {
            $trimmedLine = trim($line);
            // Skip empty lines and comments
            if ('' === $trimmedLine) {
                continue;
            }
            if (str_starts_with($trimmedLine, '#')) {
                continue;
            }

            // Calculate indentation
            $indent = strlen($line) - strlen(ltrim($line));

            // Handle indentation changes
            while (1 < count($indentStack) && $indent <= $indentStack[count($indentStack) - 1]) {
                array_pop($stack);
                array_pop($indentStack);
            }

            // Parse the line
            if (! str_contains($trimmedLine, ':')) {
                throw new InvalidArgumentException('Invalid YAML syntax on line ' . ($lineNumber + 1) . ': missing colon');
            }

            $parts = explode(':', $trimmedLine, 2);
            if (2 > count($parts)) {
                throw new InvalidArgumentException('Invalid YAML syntax on line ' . ($lineNumber + 1) . ': missing value');
            }

            [$key, $value] = $parts;
            $key = trim($key);
            $value = trim($value);

            if ('' === $key) {
                throw new InvalidArgumentException('Invalid YAML syntax on line ' . ($lineNumber + 1) . ': empty key');
            }

            if (0 === count($stack)) {
                throw new InvalidArgumentException('Invalid YAML structure on line ' . ($lineNumber + 1));
            }

            $currentLevel = &$stack[count($stack) - 1];

            if ('' === $value) {
                // This is a parent key for nested values
                /** @var array<string, mixed> $newArray */
                $newArray = [];
                $currentLevel[$key] = $newArray;
                $stack[] = &$currentLevel[$key];
                $indentStack[] = $indent;
            } else {
                // This is a key-value pair
                $currentLevel[$key] = self::parseValue($value);
            }
        }

        return $result;
    }

    /**
     * Parse a YAML value, handling strings and basic types.
     *
     * @param  string $value The value string to parse
     * @return mixed  The parsed value
     */
    private static function parseValue(string $value): mixed
    {
        $value = trim($value);

        // Handle quoted strings
        if ((str_starts_with($value, "'") && str_ends_with($value, "'"))
            || (str_starts_with($value, '"') && str_ends_with($value, '"'))) {
            return substr($value, 1, -1);
        }

        // Handle boolean values
        if ('true' === $value) {
            return true;
        }
        if ('false' === $value) {
            return false;
        }

        // Handle null values
        if ('null' === $value || '~' === $value) {
            return null;
        }

        // Handle numeric values
        if (is_numeric($value)) {
            return str_contains($value, '.') ? (float) $value : (int) $value;
        }

        // Everything else is a string
        return $value;
    }
}
