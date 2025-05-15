<?php

declare(strict_types=1);

namespace OpenFGA\Schema;

use DateTimeImmutable;
use InvalidArgumentException;
use OpenFGA\Exceptions\SchemaValidationException;
use ReflectionClass;
use RuntimeException;

use function array_key_exists;
use function count;
use function in_array;
use function is_array;
use function is_bool;
use function is_int;
use function is_string;

final class SchemaValidator
{
    /**
     * @var array<string, SchemaInterface>
     */
    private array $schemas = [];

    /**
     * Register a schema.
     *
     * @param SchemaInterface $schema
     */
    public function registerSchema(SchemaInterface $schema): self
    {
        $this->schemas[$schema->className] = $schema;

        return $this;
    }

    /**
     * Validate JSON data against a schema and transform it into a data class.
     *
     * @template T of object
     *
     * @param array<mixed, mixed> $data      JSON data (decoded)
     * @param class-string<T>     $className Target data class
     *
     * @throws SchemaValidationException If validation fails
     *
     * @return T
     */
    public function validateAndTransform(array $data, string $className): object
    {
        if (! isset($this->schemas[$className])) {
            throw new InvalidArgumentException("No schema registered for class: {$className}");
        }

        $schema = $this->schemas[$className];
        $errors = [];
        $transformedData = [];

        // Validate against schema
        foreach ($schema->getProperties() as $property) {
            $name = $property->name;
            $type = $property->type;
            $required = $property->required;
            $default = $property->default;
            $format = $property->format;
            $enum = $property->enum;
            $items = $property->items;
            $propClassName = $property->className;

            // Check if required property exists
            if (! isset($data[$name])) {
                if ($required) {
                    $errors[] = "Required property '{$name}' is missing";

                    continue;
                }

                $transformedData[$name] = $default;

                continue;
            }

            $value = $data[$name];

            // Type validation
            if (! $this->validateType($value, $type, $format, $enum)) {
                $errors[] = "Property '{$name}' has invalid type, expected {$type}";

                continue;
            }

            // Handle nested objects and arrays
            if ('object' === $type && null !== $propClassName) {
                try {
                    $transformedData[$name] = $this->validateAndTransform($value, $propClassName);
                } catch (SchemaValidationException $e) {
                    foreach ($e->getErrors() as $error) {
                        $errors[] = "{$name}.{$error}";
                    }
                }
            } elseif ('array' === $type && null !== $items) {
                $itemType = $items['type'] ?? null;
                $itemClassName = $items['className'] ?? null;

                if (! is_array($value)) {
                    $errors[] = "Property '{$name}' must be an array";

                    continue;
                }

                $transformedArray = [];
                foreach ($value as $i => $item) {
                    if ('object' === $itemType && null !== $itemClassName) {
                        try {
                            $transformedArray[] = $this->validateAndTransform($item, $itemClassName);
                        } catch (SchemaValidationException $e) {
                            foreach ($e->getErrors() as $error) {
                                $errors[] = "{$name[$i]}.{$error}";
                            }
                        }
                    } else {
                        if (! $this->validateType($item, $itemType)) {
                            $errors[] = "Item {$i} in array '{$name}' has invalid type, expected {$itemType}";

                            continue;
                        }
                        $transformedArray[] = $item;
                    }
                }
                $transformedData[$name] = $transformedArray;
            } else {
                $transformedData[$name] = $this->transformValue($value, $type);
            }
        }

        if (! empty($errors)) {
            throw new SchemaValidationException($errors);
        }

        // Create data class instance using reflection
        return $this->createInstance($className, $transformedData);
    }

    /**
     * Create a data class instance.
     *
     * @template T of object
     *
     * @param class-string<T>      $className
     * @param array<string, mixed> $data
     *
     * @return T
     */
    private function createInstance(string $className, array $data): object
    {
        $reflection = new ReflectionClass($className);
        $constructor = $reflection->getConstructor();

        if (null !== $constructor) {
            // Create using constructor
            $params = [];
            foreach ($constructor->getParameters() as $param) {
                $paramName = $param->getName();
                if (array_key_exists($paramName, $data)) {
                    $params[] = $data[$paramName];
                } elseif ($param->isDefaultValueAvailable()) {
                    $params[] = $param->getDefaultValue();
                } else {
                    throw new RuntimeException("Missing required constructor parameter: {$paramName}");
                }
            }

            return $reflection->newInstanceArgs($params);
        }
        // Create instance and set properties
        $instance = $reflection->newInstance();
        foreach ($data as $name => $value) {
            if ($reflection->hasProperty($name)) {
                $property = $reflection->getProperty($name);
                $property->setValue($instance, $value);
            }
        }

        return $instance;
    }

    /**
     * Transform a value to the correct type.
     *
     * @param mixed  $value
     * @param string $type
     *
     * @return mixed
     */
    private function transformValue(mixed $value, string $type): mixed
    {
        switch ($type) {
            case 'integer':
                return (int) $value;
            case 'number':
                return (float) $value;
            case 'boolean':
                return (bool) $value;
            default:
                return $value;
        }
    }

    /**
     * Validate a value against a type.
     *
     * @param mixed       $value
     * @param string      $type
     * @param null|string $format
     * @param null|array  $enum
     *
     * @return bool
     */
    private function validateType(mixed $value, string $type, ?string $format = null, ?array $enum = null): bool
    {
        switch ($type) {
            case 'string':
                if (! is_string($value)) {
                    return false;
                }

                if ('date' === $format) {
                    return (bool) DateTimeImmutable::createFromFormat('Y-m-d', $value);
                }

                if ('datetime' === $format) {
                    return (bool) DateTimeImmutable::createFromFormat('Y-m-d\TH:i:s\Z', $value);
                }

                if (null !== $enum) {
                    return in_array($value, $enum, true);
                }

                return true;

            case 'integer':
                return is_int($value);

            case 'number':
                return is_numeric($value);

            case 'boolean':
                return is_bool($value);

            case 'array':
                return is_array($value);

            case 'object':
                return is_array($value) && array_keys($value) !== range(0, count($value) - 1);

            case 'null':
                return null === $value;

            default:
                return false;
        }
    }
}
