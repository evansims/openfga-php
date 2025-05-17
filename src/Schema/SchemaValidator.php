<?php

declare(strict_types=1);

namespace OpenFGA\Schema;

use ArrayAccess;
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
use function is_float;
use function is_int;
use function is_object;
use function is_scalar;
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
        $this->schemas[$schema->getClassName()] = $schema;

        return $this;
    }

    /**
     * Validate JSON data against a schema and transform it into a data class.
     *
     * @template T of object
     *
     * @param mixed           $data      JSON data (decoded)
     * @param class-string<T> $className Target data class
     *
     * @throws SchemaValidationException If validation fails
     * @throws InvalidArgumentException  If no schema is registered for the class or invalid data type
     * @throws RuntimeException          If there's an error creating the instance
     *
     * @return T
     */
    public function validateAndTransform(mixed $data, string $className): object
    {
        if (! is_array($data)) {
            throw new InvalidArgumentException('Data must be an array');
        }

        if (! isset($this->schemas[$className])) {
            throw new InvalidArgumentException("No schema registered for class: {$className}");
        }

        $schema = $this->schemas[$className];

        if ($schema instanceof CollectionSchemaInterface) {
            return $this->validateAndTransformCollection($data, $schema);
        }

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
                if (! is_array($value)) {
                    $errors[] = "Property '{$name}' must be an object";

                    continue;
                }

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
                                $errors[] = "{$name}[{$i}].{$error}";
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
                // Ensure the type is one of the expected values
                $validTypes = ['string', 'integer', 'number', 'boolean', 'array', 'object', 'null'];
                if (! in_array($type, $validTypes, true)) {
                    $type = 'string'; // Default to string for unknown types
                }
                $transformedData[$name] = $this->transformValue($value, $type);
            }
        }

        if (count($errors) > 0) {
            throw new SchemaValidationException($errors);
        }

        // Create data class instance using reflection
        return $this->createInstance($className, $transformedData);
    }

    /**
     * Create a collection instance with the given items.
     *
     * @template T of object
     *
     * @param class-string<T>    $collectionClass
     * @param array<int, object> $items
     *
     * @return T
     */
    private function createCollectionInstance(string $collectionClass, array $items): object
    {
        $reflection = new ReflectionClass($collectionClass);

        $constructor = $reflection->getConstructor();
        if ($constructor && 1 === $constructor->getNumberOfParameters()) {
            $param = $constructor->getParameters()[0];
            if ($param->getType() && 'array' === $param->getType()->getName()) {
                return $reflection->newInstance($items);
            }
        }

        $collection = $reflection->newInstance();

        if ($reflection->implementsInterface(ArrayAccess::class)) {
            foreach ($items as $key => $item) {
                $collection->offsetSet($key, $item);
            }
        } elseif ($reflection->hasMethod('add')) {
            $addMethod = $reflection->getMethod('add');
            $params = $addMethod->getParameters();
            $paramCount = count($params);

            foreach ($items as $key => $item) {
                if (2 === $paramCount) {
                    // If add() accepts two parameters, pass both key and item
                    $addMethod->invoke($collection, $key, $item);
                } else {
                    // Otherwise, just pass the item
                    $addMethod->invoke($collection, $item);
                }
            }
        } else {
            throw new RuntimeException("Could not add items to collection: {$collectionClass}");
        }

        return $collection;
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
     * @param mixed                                                         $value
     * @param 'array'|'boolean'|'integer'|'null'|'number'|'object'|'string' $type
     *
     * @return null|array<mixed>|bool|float|int|object|string
     */
    private function transformValue(mixed $value, string $type): mixed
    {
        switch ($type) {
            case 'integer':
                if (is_int($value)) {
                    return $value;
                }
                if (is_numeric($value)) {
                    return (int) $value;
                }

                return 0;
            case 'number':
                return is_float($value) ? $value : (is_int($value) ? (float) $value : (float) 0);
            case 'boolean':
                return is_bool($value) ? $value : (bool) $value;
            case 'array':
                return is_array($value) ? $value : [];
            case 'object':
                return is_object($value) ? $value : (object) (is_array($value) ? $value : []);
            case 'null':
                return null;
            case 'string':
            default:
                if (is_string($value)) {
                    return $value;
                }
                if (is_scalar($value) || (is_object($value) && method_exists($value, '__toString'))) {
                    return (string) $value;
                }

                return '';
        }
    }

    /**
     * Validate and transform a collection (direct array).
     *
     * @param array<int, mixed>         $data   Array of items
     * @param CollectionSchemaInterface $schema Collection schema
     *
     * @throws SchemaValidationException If validation fails
     *
     * @return object
     */
    private function validateAndTransformCollection(array $data, CollectionSchemaInterface $schema): object
    {
        $errors = [];
        $transformedItems = [];
        $itemType = $schema->getItemType();

        // Check if collection requires items
        if ($schema->requiresItems() && empty($data)) {
            throw new SchemaValidationException(['Collection requires at least one item']);
        }

        // Validate each item in the array
        foreach ($data as $index => $item) {
            try {
                $transformedItems[] = $this->validateAndTransform($item, $itemType);
            } catch (SchemaValidationException $e) {
                foreach ($e->getErrors() as $error) {
                    $errors[] = "[{$index}].{$error}";
                }
            }
        }

        if (! empty($errors)) {
            throw new SchemaValidationException($errors);
        }

        // Create collection instance with transformed items
        $collectionClass = $schema->getClassName();

        return $this->createCollectionInstance($collectionClass, $transformedItems);
    }

    /**
     * Validate a value against a type.
     *
     * @param mixed              $value
     * @param string             $type
     * @param null|string        $format
     * @param null|array<string> $enum
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
                return is_int($value) || (is_string($value) && ctype_digit($value) && $value === (string) (int) $value);

            case 'number':
                return is_float($value) || is_int($value) || (is_string($value) && is_numeric($value));

            case 'boolean':
                return is_bool($value);

            case 'array':
                return is_array($value);

            case 'object':
                if (! is_array($value)) {
                    return false;
                }
                if (0 === count($value)) {
                    return false;
                }
                // Accept associative arrays (at least one non-numeric key)
                foreach (array_keys($value) as $k) {
                    if (! is_int($k)) {
                        return true;
                    }
                }

                return false;

            case 'null':
                return null === $value;

            default:
                return false;
        }
    }
}
