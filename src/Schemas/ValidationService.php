<?php

declare(strict_types=1);

namespace OpenFGA\Schemas;

use InvalidArgumentException;
use OpenFGA\Exceptions\{SerializationError, SerializationException};
use Override;
use ReflectionException;

use function array_key_exists;
use function gettype;
use function in_array;
use function is_array;
use function is_bool;
use function is_float;
use function is_int;
use function is_object;
use function is_string;
use function sprintf;

/**
 * Service for validating data against schemas.
 *
 * This service encapsulates validation logic, separating it from object construction
 * concerns in SchemaValidator. It provides validation for both complete data structures
 * and individual properties, with detailed error reporting.
 */
final class ValidationService implements ValidationServiceInterface
{
    /**
     * @var array<string, SchemaInterface>
     */
    private array $schemas = [];

    /**
     * @inheritDoc
     */
    #[Override]
    public function hasSchema(string $className): bool
    {
        return isset($this->schemas[$className]);
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function registerSchema(SchemaInterface $schema): self
    {
        $this->schemas[$schema->getClassName()] = $schema;

        return $this;
    }

    /**
     * @inheritDoc
     *
     * @throws InvalidArgumentException If message translation parameters are invalid
     * @throws ReflectionException      If exception location capture fails
     *
     * @return array<string, mixed>
     */
    #[Override]
    public function validate(mixed $data, string $className): array
    {
        if (! is_array($data)) {
            throw SerializationError::InvalidItemType->exception(context: ['className' => $className, 'expected' => 'array', 'actual' => gettype($data)]);
        }

        if (! isset($this->schemas[$className])) {
            throw SerializationError::UndefinedItemType->exception(context: ['className' => $className]);
        }

        $schema = $this->schemas[$className];
        $errors = [];

        /** @var array<string, mixed> $validatedData Schema validation produces mixed types by design */
        $validatedData = [];

        // Validate each property against the schema
        foreach ($schema->getProperties() as $schemaProperty) {
            $name = $schemaProperty->getName();
            $required = $schemaProperty->isRequired();

            // Check if required property exists
            if (! array_key_exists($name, $data)) {
                if ($required) {
                    $errors[] = sprintf("Required property '%s' is missing", $name);

                    continue;
                }

                // Use default value for optional properties
                /** @var mixed $default */
                $default = $schemaProperty->getDefault();
                // Schema validation produces mixed types by design
                $validatedData = array_merge($validatedData, [$name => $default]);

                continue;
            }

            try {
                // Validate the property value
                /** @var mixed $validatedValue */
                $validatedValue = $this->validateProperty(
                    $data[$name],
                    $schemaProperty,
                    sprintf('%s.%s', $className, $name),
                );
                // Schema validation produces mixed types by design
                $validatedData = array_merge($validatedData, [$name => $validatedValue]);
            } catch (SerializationException $e) {
                $errors[] = sprintf("Property '%s': %s", $name, $e->getMessage());
            }
        }

        // Check if there were any validation errors
        if ([] !== $errors) {
            throw SerializationError::InvalidItemType->exception(context: ['className' => $className, 'errors' => implode(', ', $errors)]);
        }

        return $validatedData;
    }

    /**
     * @inheritDoc
     *
     * @throws InvalidArgumentException If message translation parameters are invalid
     * @throws ReflectionException      If exception location capture fails
     */
    #[Override]
    public function validateProperty(mixed $value, SchemaPropertyInterface $property, string $path): mixed
    {
        $type = $property->getType();
        $format = $property->getFormat();
        $enum = $property->getEnum();

        // Type validation
        if (! $this->validateType($value, $type, $enum)) {
            $valueType = gettype($value);

            if (is_object($value)) {
                $valueType = $value::class;
            }

            throw SerializationError::InvalidItemType->exception(context: ['path' => $path, 'expected' => $type, 'actual' => $valueType, 'format' => $format]);
        }

        // Handle nested objects
        if ('object' === $type && null !== $property->getClassName()) {
            if (! is_array($value)) {
                throw SerializationError::InvalidItemType->exception(context: ['path' => $path, 'expected' => 'array (for object)', 'actual' => gettype($value)]);
            }

            return $this->validate($value, $property->getClassName());
        }

        // Handle arrays
        if ('array' === $type && null !== $property->getItems()) {
            if (! is_array($value)) {
                throw SerializationError::InvalidItemType->exception(context: ['path' => $path, 'expected' => 'array', 'actual' => gettype($value)]);
            }

            /** @var array<int, mixed> $validatedArray Array validation produces mixed types by design */
            $validatedArray = [];
            $items = $property->getItems();
            $itemType = $items['type'] ?? 'mixed';
            $itemClassName = $items['className'] ?? null;

            /** @var array<int, mixed> $value */
            /** @var mixed $item */
            foreach ($value as $index => $item) {
                if ('object' === $itemType && null !== $itemClassName) {
                    if (! is_array($item)) {
                        throw SerializationError::InvalidItemType->exception(context: ['path' => sprintf('%s[%d]', $path, $index), 'expected' => 'array (for object)', 'actual' => gettype($item)]);
                    }
                    $validatedItem = $this->validate($item, $itemClassName);
                    $validatedArray[] = $validatedItem;
                } else {
                    if (! $this->validateType($item, $itemType, null)) {
                        throw SerializationError::InvalidItemType->exception(context: ['path' => sprintf('%s[%d]', $path, $index), 'expected' => $itemType, 'actual' => gettype($item)]);
                    }

                    // Array validation produces mixed types by design
                    /** @var list<mixed> $validatedArray */
                    $validatedArray = [...$validatedArray, $item];
                }
            }

            return $validatedArray;
        }

        return $value;
    }

    /**
     * Validate a value against a type specification.
     *
     * @param  mixed              $value The value to validate
     * @param  string             $type  The expected type
     * @param  array<string>|null $enum  Optional enumeration values
     * @return bool               True if the value matches the type specification
     */
    private function validateType(mixed $value, string $type, ?array $enum = null): bool
    {
        switch ($type) {
            case 'string':
                if (! is_string($value)) {
                    return false;
                }

                if (null !== $enum) {
                    return in_array($value, $enum, true);
                }

                return true;

            case 'integer':
                return is_int($value);

            case 'number':
                return is_float($value) || is_int($value);

            case 'boolean':
                return is_bool($value);

            case 'array':
                return is_array($value);

            case 'object':
                // Accept objects or associative arrays
                if (is_object($value)) {
                    return true;
                }

                if (is_array($value)) {
                    // Empty array is valid for empty object
                    if ([] === $value) {
                        return true;
                    }

                    // Check if it's an associative array
                    foreach (array_keys($value) as $k) {
                        if (! is_int($k)) {
                            return true;
                        }
                    }

                    return false;
                }

                return false;

            case 'null':
                return null === $value;

            case 'mixed':
                return true;

            default:
                return false;
        }
    }
}
