<?php

declare(strict_types=1);

namespace OpenFGA\Schemas;

use ArrayAccess;
use BackedEnum;
use DateTimeImmutable;
use Exception;
use InvalidArgumentException;
use OpenFGA\Exceptions\{ClientThrowable, SerializationError};
use OpenFGA\Models\Collections\KeyedCollection;
use OpenFGA\Models\UsersListUser;
use Override;
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
use function is_numeric;
use function is_object;
use function is_scalar;
use function is_string;
use function method_exists;
use function sprintf;

/**
 * Validates and transforms data according to registered JSON schemas.
 *
 * This validator ensures that API response data conforms to expected schemas
 * and transforms raw arrays into strongly typed model objects. It handles
 * nested objects, collections, and complex validation rules while providing
 * detailed error reporting for schema violations.
 *
 * @see SchemaValidatorInterface For the complete API specification
 */
final class SchemaValidator implements SchemaValidatorInterface
{
    /**
     * The validation service for separating validation from construction.
     */
    private readonly ValidationServiceInterface $validationService;

    /**
     * @var array<string, SchemaInterface>
     */
    private array $schemas = [];

    /**
     * Create a new schema validator instance.
     *
     * @param ValidationServiceInterface|null $validationService Optional validation service
     */
    public function __construct(?ValidationServiceInterface $validationService = null)
    {
        $this->validationService = $validationService ?? new ValidationService;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getSchemas(): array
    {
        return $this->schemas;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function registerSchema(SchemaInterface $schema): self
    {
        $this->schemas[$schema->getClassName()] = $schema;
        $this->validationService->registerSchema($schema);

        return $this;
    }

    /**
     * @inheritDoc
     *
     * @throws InvalidArgumentException If message translation parameters are invalid
     * @throws ReflectionException      If exception location capture fails
     */
    #[Override]
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
            if (! array_is_list($data) && ! is_subclass_of($className, KeyedCollection::class)) {
                $data = array_values($data);
            }

            return $this->validateAndTransformCollection($data, $schema, $className);
        }

        $errors = [];

        /** @var array<string, mixed> $transformedData */
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
                    $errors[] = sprintf("Required property '%s' is missing.", $name);

                    continue;
                }

                /** @var mixed $default */
                $default = $schemaProperty->default;

                // Explicitly assign mixed default value to transformedData array
                $this->assignMixed($transformedData, $name, $default);

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
                $this->assignMixed($transformedData, $name, $value);
            } elseif ('array' === $type && null !== $items) {
                $itemType = $items['type'] ?? null;
                $itemClassName = $items['className'] ?? null;

                if (! is_array($value)) {
                    throw SerializationError::InvalidItemType->exception(context: ['property' => $name, 'type' => $type]);
                }

                /** @var array<int, mixed> $transformedArray */
                $transformedArray = [];

                /** @var array<int, mixed> $value */
                /** @var mixed $item */
                foreach ($value as $item) {
                    if ('object' === $itemType && null !== $itemClassName) {
                        $transformedItem = $this->validateAndTransform($item, $itemClassName);
                        $transformedArray[] = $transformedItem;
                    } else {
                        $validationItemType = $itemType ?? 'mixed';

                        if (! $this->validateType($item, $validationItemType, null, null)) {
                            throw SerializationError::InvalidItemType->exception(context: ['property' => $name, 'type' => $validationItemType]);
                        }

                        // Add validated item to array - type already validated above
                        $this->appendMixed($transformedArray, $item);
                    }
                }

                // Only add the transformed array if there were no errors
                if ([] === $errors) {
                    $transformedData[$name] = $transformedArray;
                }
            } else {
                // Ensure the type is one of the expected scalar values (object and array handled above)
                $validTypes = ['string', 'integer', 'number', 'boolean', 'null'];

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
     * Safely append a mixed value to an array to satisfy Psalm.
     *
     * @param array<mixed> $array The target array
     * @param mixed        $value The value to append
     *
     * @psalm-suppress MixedAssignment
     */
    private function appendMixed(array &$array, mixed $value): void
    {
        $array[] = $value;
    }

    /**
     * Safely assign a mixed value to an array to satisfy Psalm.
     *
     * @param array<string, mixed> $array The target array
     * @param string               $key   The array key
     * @param mixed                $value The value to assign
     *
     * @psalm-suppress MixedAssignment
     */
    private function assignMixed(array &$array, string $key, mixed $value): void
    {
        $array[$key] = $value;
    }

    /**
     * Convert camelCase to snake_case.
     *
     * @param  string $input The camelCase string to convert
     * @return string The converted snake_case string
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
     * @param class-string<T>          $className The fully qualified class name of the collection to create
     * @param array<int|string, mixed> $items     The items to populate the collection with
     *
     * @throws ClientThrowable          If the collection cannot be populated with items
     * @throws InvalidArgumentException If message translation parameters are invalid
     * @throws ReflectionException      If exception location capture fails
     * @throws ReflectionException      If the class cannot be reflected or instantiated
     *
     * @return T The created and populated collection instance
     */
    private function createCollectionInstance(string $className, array $items)
    {
        $reflection = new ReflectionClass($className);
        $constructor = $reflection->getConstructor();

        if (null !== $constructor && 1 === $constructor->getNumberOfParameters()) {
            $parameters = $constructor->getParameters();

            if (! isset($parameters[0])) {
                throw SerializationError::InvalidItemType->exception(context: ['className' => $className, 'message' => 'Constructor parameter not found']);
            }
            $param = $parameters[0];
            $paramType = $param->getType();

            if ($paramType instanceof ReflectionNamedType && 'array' === $paramType->getName()) {
                return $reflection->newInstance($items);
            }
        }

        $instance = $reflection->newInstance();

        if ($instance instanceof ArrayAccess) {
            /** @var ArrayAccess<int|string, mixed>&T $arrayInstance */
            $arrayInstance = $instance;

            /** @var mixed $item */
            foreach ($items as $key => $item) {
                $arrayInstance->offsetSet($key, $item);
            }
        } elseif ($reflection->hasMethod('add')) {
            $addMethod = $reflection->getMethod('add');
            $params = $addMethod->getParameters();
            $paramCount = count($params);

            /** @var mixed $item */
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
     * @param array<string, mixed> $data      The validated data to use for object creation
     * @param class-string<T>      $className The fully qualified class name to instantiate
     *
     * @throws ClientThrowable          If object creation fails or required constructor parameters are missing
     * @throws InvalidArgumentException If message translation parameters are invalid
     * @throws ReflectionException      If exception location capture fails
     * @throws ReflectionException      If the class cannot be reflected or instantiated
     *
     * @return T The created and initialized object instance
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
            /** @var array<string, mixed> $params */
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
                    /** @var mixed $transformedValue */
                    $transformedValue = $this->transformParameterValue($data[$mappedFieldName], $parameter);

                    // Assign transformed parameter value to constructor params
                    $this->assignMixed($params, $paramName, $transformedValue);
                } elseif (array_key_exists($paramName, $data)) {
                    /** @var mixed $transformedValue */
                    $transformedValue = $this->transformParameterValue($data[$paramName], $parameter);

                    // Assign transformed parameter value to constructor params
                    $this->assignMixed($params, $paramName, $transformedValue);
                } elseif (array_key_exists($snakeCaseName, $data)) {
                    /** @var mixed $transformedValue */
                    $transformedValue = $this->transformParameterValue($data[$snakeCaseName], $parameter);

                    // Assign transformed parameter value to constructor params
                    $this->assignMixed($params, $paramName, $transformedValue);
                } elseif ($parameter->isDefaultValueAvailable()) {
                    /** @var mixed $defaultValue */
                    $defaultValue = $parameter->getDefaultValue();

                    // Assign default parameter value to constructor params
                    $this->assignMixed($params, $paramName, $defaultValue);
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
        /** @var mixed $value */
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
     * @param  mixed               $value     The raw value to transform
     * @param  ReflectionParameter $parameter The constructor parameter reflection for type information
     * @return mixed               The transformed value matching the expected parameter type
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
     * Transform string values with format handling.
     *
     * @param  mixed                         $value  The value to transform
     * @param  string|null                   $format Optional format specification
     * @return DateTimeImmutable|string|null The transformed string or date value
     */
    private function transformStringValue(mixed $value, ?string $format): null | DateTimeImmutable | string
    {
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
    }

    /**
     * Transform a value to the correct type.
     *
     * @param  mixed                                          $value  The raw value to transform
     * @param  string                                         $type   The target type to transform to
     * @param  string|null                                    $format Optional format constraint for string types (for example 'date', 'datetime')
     * @return array<mixed>|bool|float|int|object|string|null The transformed value in the correct type
     */
    private function transformValue(mixed $value, string $type, ?string $format = null): mixed
    {
        return match ($type) {
            'integer' => is_int($value) ? $value : (is_numeric($value) ? (int) $value : 0),
            'number' => is_float($value) ? $value : (is_int($value) ? (float) $value : (float) 0),
            'boolean' => is_bool($value) ? $value : (bool) $value,
            'array' => is_array($value) ? $value : [],
            'object' => is_object($value) ? $value : (object) (is_array($value) ? $value : []),
            'null' => null,
            'string' => $this->transformStringValue($value, $format),
            default => $this->transformStringValue($value, $format),
        };
    }

    /**
     * Validate and transform a collection (direct array).
     *
     * @template T of object
     *
     * @param array<int|string, mixed>  $data      Array of items to validate and transform
     * @param CollectionSchemaInterface $schema    The collection schema defining validation rules
     * @param class-string<T>           $className The fully qualified class name of the collection to create
     *
     * @throws ClientThrowable          If validation fails or collection cannot be created
     * @throws InvalidArgumentException If message translation parameters are invalid
     * @throws ReflectionException      If exception location capture fails
     *
     * @return T The validated and populated collection instance
     */
    private function validateAndTransformCollection(array $data, CollectionSchemaInterface $schema, string $className): object
    {
        /** @var array<mixed> $transformedItems */
        $transformedItems = [];
        $itemType = $schema->getItemType();

        /** @var class-string<T> $itemType */

        // Check if collection requires items
        if ($schema->requiresItems() && [] === $data) {
            throw SerializationError::EmptyCollection->exception();
        }

        // Validate each item in the array
        /** @var array<mixed> $data */
        /** @var mixed $item */
        foreach ($data as $key => $item) {
            // Special handling for UsersListUser - API returns strings directly
            if (UsersListUser::class === $itemType && is_string($item)) {
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
     * @param  mixed              $value  The value to validate
     * @param  string             $type   The expected type (string, integer, boolean, etc.)
     * @param  string|null        $format Optional format constraint for string types
     * @param  array<string>|null $enum   Optional array of allowed values for enumeration validation
     * @return bool               True if the value matches the expected type and constraints, false otherwise
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
