<?php

declare(strict_types=1);

namespace OpenFGA\Schema;

use ArrayAccess;
use BackedEnum;
use DateTimeImmutable;
use Exception;
use OpenFGA\Exceptions\{SerializationError, SerializationException};
use ReflectionClass;

use ReflectionException;
use ReflectionNamedType;

use ReflectionParameter;

use function array_key_exists;
use function count;
use function gettype;
use function in_array;
use function is_array;
use function is_bool;
use function is_float;
use function is_int;
use function is_object;
use function is_scalar;
use function is_string;
use function sprintf;

final class SchemaValidator
{
    /**
     * @var array<string, SchemaInterface>
     */
    private array $schemas = [];

    /**
     * Get all registered schemas.
     *
     * @return array<string, SchemaInterface>
     */
    public function getSchemas(): array
    {
        return $this->schemas;
    }

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
     * @template T of object
     *
     * @param mixed           $data
     * @param class-string<T> $className
     *
     * @throws SerializationException If validation fails, missing required constructor parameter, or could not add items to collection
     *
     * @return T
     */
    public function validateAndTransform(mixed $data, string $className): object
    {
        if (! is_array($data)) {
            throw SerializationError::InvalidItemType->exception(context: ['className' => $className]);
        }

        if (! isset($this->schemas[$className])) {
            throw SerializationError::UndefinedItemType->exception(context: ['className' => $className]);
        }

        $schema = $this->schemas[$className];

        if ($schema instanceof CollectionSchemaInterface) {
            // Check if the collection expects data wrapped in a specific key
            $wrapperKey = $schema->getWrapperKey();
            if (null !== $wrapperKey && array_key_exists($wrapperKey, $data)) {
                if (! is_array($data[$wrapperKey])) {
                    throw SerializationError::InvalidItemType->exception(context: ['className' => $className, 'property' => $wrapperKey, 'expected' => 'array', 'actual' => gettype($data[$wrapperKey])]);
                }
                $data = $data[$wrapperKey];
            }

            // Only convert to list for IndexedCollections, preserve keys for KeyedCollections
            if (! array_is_list($data) && ! is_subclass_of($className, \OpenFGA\Models\Collections\KeyedCollection::class)) {
                $data = array_values($data);
            }

            /** @var T */
            return $this->validateAndTransformCollection($data, $schema, $className);
        }

        $errors = [];
        $transformedData = [];

        // Validate against schema
        foreach ($schema->getProperties() as $schemaProperty) {
            $name = $schemaProperty->name;
            $type = $schemaProperty->type;
            $required = $schemaProperty->required;
            $format = $schemaProperty->format;
            $enum = $schemaProperty->enum;
            $items = $schemaProperty->items;
            $propClassName = $schemaProperty->className;

            // Handle 'self' type - it's a recursive reference to the same class
            if ('self' === $type) {
                $type = 'object';
                $propClassName = $className;
            }

            // Check if required property exists
            if (! isset($data[$name])) {
                if ($required) {
                    $errors[] = sprintf("Required property '%s' is missing", $name);

                    continue;
                }

                /** @var mixed $default */
                $default = $schemaProperty->default;
                $transformedData[$name] = $default;

                continue;
            }

            $value = $data[$name];

            // Type validation (skip for object types with className as they're handled recursively)
            if (! ('object' === $type && null !== $propClassName) && ! $this->validateType($value, $type, $format, $enum)) {
                // Add more context to help debug
                $valueType = gettype($value);
                if (is_object($value)) {
                    $valueType = $value::class;
                }

                throw SerializationError::InvalidItemType->exception(context: ['property' => $name, 'type' => $type, 'format' => $format, 'expected' => $schemaProperty->type, 'value' => $value, 'actual_type' => $valueType, 'className' => $className]);
            }

            // Handle nested objects and arrays
            if ('object' === $type && null !== $propClassName) {
                if (! is_array($value)) {
                    throw SerializationError::InvalidItemType->exception(context: ['property' => $name, 'type' => $type]);
                }

                $transformedData[$name] = $this->validateAndTransform($value, $propClassName);
            } elseif ('object' === $type && null === $propClassName) {
                // Plain object without specific class - accept as-is
                $transformedData[$name] = $value;
            } elseif ('array' === $type && null !== $items) {
                $itemType = $items['type'] ?? null;
                $itemClassName = $items['className'] ?? null;

                if (! is_array($value)) {
                    throw SerializationError::InvalidItemType->exception(context: ['property' => $name, 'type' => $type]);
                }

                $transformedArray = [];

                /** @var array<int, mixed> $value */
                foreach ($value as $item) {
                    if ('object' === $itemType && null !== $itemClassName) {
                        $transformedItem = $this->validateAndTransform($item, $itemClassName);
                        $transformedArray[] = $transformedItem;
                    } else {
                        if (! $this->validateType($item, $itemType)) {
                            throw SerializationError::InvalidItemType->exception(context: ['property' => $name, 'type' => $itemType]);
                        }
                        $transformedArray[] = $item;
                    }
                }

                // Only add the transformed array if there were no errors
                if ([] === $errors) {
                    $transformedData[$name] = $transformedArray;
                }
            } else {
                // Ensure the type is one of the expected values
                $validTypes = ['string', 'integer', 'number', 'boolean', 'array', 'object', 'null'];
                if (! in_array($type, $validTypes, true)) {
                    $type = 'string'; // Default to string for unknown types
                }
                $transformedData[$name] = $this->transformValue($value, $type, $format);
            }
        }

        // Check if there were any errors and throw exception if needed
        if ([] !== $errors) {
            throw SerializationError::InvalidItemType->exception(context: ['errors' => implode(', ', $errors)]);
        }

        // Create data class instance using reflection
        return $this->createInstance($className, $transformedData);
    }

    /**
     * Convert camelCase to snake_case.
     *
     * @param string $input
     */
    private function camelToSnakeCase(string $input): string
    {
        $result = preg_replace('/([a-z])([A-Z])/', '$1_$2', $input);

        return strtolower($result ?? $input);
    }

    /**
     * Create a collection instance with the given items.
     *
     * @template T of object
     *
     * @param class-string<T>          $className
     * @param array<int|string, mixed> $items
     *
     * @throws ReflectionException
     *
     * @return T
     */
    private function createCollectionInstance(string $className, array $items)
    {
        $reflection = new ReflectionClass($className);
        $constructor = $reflection->getConstructor();

        if (null !== $constructor && 1 === $constructor->getNumberOfParameters()) {
            $param = $constructor->getParameters()[0];
            $paramType = $param->getType();

            if ($paramType instanceof ReflectionNamedType && 'array' === $paramType->getName()) {
                return $reflection->newInstance($items);
            }
        }

        $instance = $reflection->newInstance();

        if ($instance instanceof ArrayAccess) {
            /** @var ArrayAccess<int|string, mixed>&T $arrayInstance */
            $arrayInstance = $instance;
            foreach ($items as $key => $item) {
                $arrayInstance->offsetSet($key, $item);
            }
        } elseif ($reflection->hasMethod('add')) {
            $addMethod = $reflection->getMethod('add');
            $params = $addMethod->getParameters();
            $paramCount = count($params);

            foreach ($items as $key => $item) {
                if (2 === $paramCount) {
                    // If add() accepts two parameters, pass both key and item
                    $addMethod->invoke($instance, $key, $item);
                } else {
                    // Otherwise, just pass the item
                    $addMethod->invoke($instance, $item);
                }
            }
        } else {
            throw SerializationError::CouldNotAddItemsToCollection->exception(context: ['className' => $className]);
        }

        return $instance;
    }

    /**
     * Create a data class instance.
     *
     * @template T of object
     *
     * @param array<string, mixed> $data
     * @param class-string<T>      $className
     *
     * @throws SerializationException
     * @throws ReflectionException
     *
     * @return T
     */
    private function createInstance(string $className, array $data): object
    {
        $reflection = new ReflectionClass($className);

        // Check if the class has a static fromArray method
        if ($reflection->hasMethod('fromArray') && $reflection->getMethod('fromArray')->isStatic()) {
            $method = $reflection->getMethod('fromArray');

            /** @var T */
            return $method->invoke(null, $data);
        }

        $constructor = $reflection->getConstructor();

        // If there's a constructor, try to use it with named parameters
        if (null !== $constructor) {
            $params = [];

            // Get schema if available to check for parameter mappings
            $schema = $this->schemas[$className] ?? SchemaRegistry::get($className);
            $parameterMappings = [];
            if (null !== $schema) {
                foreach ($schema->getProperties() as $schemaProperty) {
                    if (null !== $schemaProperty->parameterName) {
                        $parameterMappings[$schemaProperty->parameterName] = $schemaProperty->name;
                    }
                }
            }

            foreach ($constructor->getParameters() as $parameter) {
                $paramName = $parameter->getName();
                $snakeCaseName = $this->camelToSnakeCase($paramName);

                // Check if there's a custom mapping for this parameter
                $mappedFieldName = $parameterMappings[$paramName] ?? null;

                if (null !== $mappedFieldName && array_key_exists($mappedFieldName, $data)) {
                    // Use the mapped field name
                    $params[$paramName] = $this->transformParameterValue($data[$mappedFieldName], $parameter);
                } elseif (array_key_exists($paramName, $data)) {
                    $params[$paramName] = $this->transformParameterValue($data[$paramName], $parameter);
                } elseif (array_key_exists($snakeCaseName, $data)) {
                    $params[$paramName] = $this->transformParameterValue($data[$snakeCaseName], $parameter);
                } elseif ($parameter->isDefaultValueAvailable()) {
                    $params[$paramName] = $parameter->getDefaultValue();
                } else {
                    throw SerializationError::MissingRequiredConstructorParameter->exception(context: ['className' => $className, 'paramName' => $paramName]);
                }
            }

            $instance = $reflection->newInstanceArgs($params);
        } else {
            // Create instance without constructor
            $instance = $reflection->newInstanceWithoutConstructor();
        }

        // Set public properties (skip readonly properties)
        foreach ($data as $name => $value) {
            if ($reflection->hasProperty($name)) {
                $schemaProperty = $reflection->getProperty($name);

                if (! $schemaProperty->isReadOnly()) {
                    $schemaProperty->setValue($instance, $value);
                }
            }
        }

        return $instance;
    }

    /**
     * Transform a parameter value to match the expected type.
     *
     * @param mixed               $value
     * @param ReflectionParameter $parameter
     */
    private function transformParameterValue(mixed $value, ReflectionParameter $parameter): mixed
    {
        $type = $parameter->getType();

        if ($type instanceof ReflectionNamedType) {
            $typeName = $type->getName();

            // Handle enum types
            if (enum_exists($typeName) && is_string($value)) {
                /** @var class-string<BackedEnum> $enumClass */
                $enumClass = $typeName;

                return $enumClass::from($value);
            }

            // Handle object types - convert empty arrays to stdClass
            if ('object' === $typeName && is_array($value)) {
                return (object) $value;
            }
        }

        return $value;
    }

    /**
     * Transform a value to the correct type.
     *
     * @param mixed                                                         $value
     * @param 'array'|'boolean'|'integer'|'null'|'number'|'object'|'string' $type
     * @param ?string                                                       $format
     *
     * @return null|array<mixed>|bool|float|int|object|string
     */
    private function transformValue(mixed $value, string $type, ?string $format = null): mixed
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
                if ('date' === $format) {
                    if ($value instanceof DateTimeImmutable) {
                        return $value;
                    }
                    if (is_string($value)) {
                        $date = DateTimeImmutable::createFromFormat('Y-m-d', $value);
                        if (false !== $date) {
                            return $date;
                        }
                    }

                    return null;
                }

                if ('datetime' === $format) {
                    if ($value instanceof DateTimeImmutable) {
                        return $value;
                    }

                    if (is_string($value)) {
                        try {
                            return new DateTimeImmutable($value);
                        } catch (Exception) {
                        }
                    }

                    return null;
                }

                if (is_string($value)) {
                    return $value;
                }
                if (is_scalar($value) || (is_object($value) && method_exists($value, '__toString'))) {
                    return (string) $value;
                }

                return '';

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
     * @template T of object
     *
     * @param array<int|string, mixed>  $data      Array of items
     * @param CollectionSchemaInterface $schema    Collection schema
     * @param class-string<T>           $className
     *
     * @throws SerializationException If validation fails
     *
     * @return T
     */
    private function validateAndTransformCollection(array $data, CollectionSchemaInterface $schema, string $className): object
    {
        $transformedItems = [];
        $itemType = $schema->getItemType();

        /** @var class-string<T> $itemType */

        // Check if collection requires items
        if ($schema->requiresItems() && [] === $data) {
            throw SerializationError::EmptyCollection->exception();
        }

        // Validate each item in the array
        foreach ($data as $key => $item) {
            // Special handling for UsersListUser - API returns strings directly
            if (\OpenFGA\Models\UsersListUser::class === $itemType && is_string($item)) {
                $transformedItems[$key] = $this->validateAndTransform(['user' => $item], $itemType);
            } else {
                $transformedItems[$key] = $this->validateAndTransform($item, $itemType);
            }
        }

        return $this->createCollectionInstance($className, $transformedItems);
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
                if (! is_string($value) && ! $value instanceof DateTimeImmutable) {
                    return false;
                }

                if ('date' === $format) {
                    return is_string($value) && false !== DateTimeImmutable::createFromFormat('Y-m-d', $value);
                }

                if ('datetime' === $format) {
                    if (! is_string($value)) {
                        return false;
                    }

                    try {
                        new DateTimeImmutable($value);
                    } catch (Exception) {
                        return false;
                    }

                    return true;
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
                // Accept actual objects
                if (is_object($value)) {
                    return true;
                }
                // Accept arrays as objects (PHP's json_decode converts JSON objects to arrays)
                // Empty objects {} in JSON become empty arrays [] in PHP
                if (is_array($value)) {
                    // Empty array is valid for empty object
                    if ([] === $value) {
                        return true;
                    }
                    // Check if it's an associative array (object-like)
                    foreach (array_keys($value) as $k) {
                        if (! is_int($k)) {
                            return true;
                        }
                    }

                    // Numeric arrays are not valid objects
                    return false;
                }

                return false;

            case 'null':
                return null === $value;

            default:
                return false;
        }
    }
}
