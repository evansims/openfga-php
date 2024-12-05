<?php

declare(strict_types=1);
/**
 * RelationMetadata.
 *
 * PHP version 7.4
 *
 * @category Class
 *
 * @author   OpenAPI Generator team
 *
 * @link     https://openapi-generator.tech
 */

/**
 * OpenFGA.
 *
 * A high performance and flexible authorization/permission engine built for developers and inspired by Google Zanzibar.
 *
 * The version of the OpenAPI document: 1.x
 * Contact: community@openfga.dev
 * Generated by: https://openapi-generator.tech
 * Generator version: 7.10.0
 */

/**
 * NOTE: This class is auto generated by OpenAPI Generator (https://openapi-generator.tech).
 * https://openapi-generator.tech
 * Do not edit the class manually.
 */

namespace OpenFGA\API\Models;

use ArrayAccess;
use InvalidArgumentException;
use JsonSerializable;
use OpenFGA\API\ObjectSerializer;
use ReturnTypeWillChange;

use function array_key_exists;
use function count;
use function in_array;

/**
 * RelationMetadata Class Doc Comment.
 *
 * @category Class
 *
 * @author   OpenAPI Generator team
 *
 * @link     https://openapi-generator.tech
 *
 * @implements \ArrayAccess<string, mixed>
 */
final class RelationMetadata implements ArrayAccess, JsonSerializable, ModelInterface
{
    public const DISCRIMINATOR = null;

    /**
     * Associative array for storing property values.
     *
     * @var mixed[]
     */
    private $container = [];

    /**
     * If a nullable field gets set to null, insert it here.
     *
     * @var bool[]
     */
    private array $openAPINullablesSetToNull = [];

    /**
     * Array of attributes where the key is the local name,
     * and the value is the original name.
     *
     * @var string[]
     */
    private static $attributeMap = [
        'directly_related_user_types' => 'directly_related_user_types',
        'module' => 'module',
        'source_info' => 'source_info',
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests).
     *
     * @var string[]
     */
    private static $getters = [
        'directly_related_user_types' => 'getDirectlyRelatedUserTypes',
        'module' => 'getModule',
        'source_info' => 'getSourceInfo',
    ];

    /**
     * Array of property to format mappings. Used for (de)serialization.
     *
     * @var string[]
     *
     * @phpstan-var array<string, string|null>
     *
     * @psalm-var array<string, string|null>
     */
    private static $openAPIFormats = [
        'directly_related_user_types' => null,
        'module' => null,
        'source_info' => null,
    ];

    /**
     * The original name of the model.
     *
     * @var string
     */
    private static $openAPIModelName = 'RelationMetadata';

    /**
     * Array of nullable properties. Used for (de)serialization.
     *
     * @var bool[]
     */
    private static array $openAPINullables = [
        'directly_related_user_types' => false,
        'module' => false,
        'source_info' => false,
    ];

    /**
     * Array of property to type mappings. Used for (de)serialization.
     *
     * @var string[]
     */
    private static $openAPITypes = [
        'directly_related_user_types' => '\OpenFGA\API\Models\RelationReference[]',
        'module' => 'string',
        'source_info' => '\OpenFGA\API\Models\SourceInfo',
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses).
     *
     * @var string[]
     */
    private static $setters = [
        'directly_related_user_types' => 'setDirectlyRelatedUserTypes',
        'module' => 'setModule',
        'source_info' => 'setSourceInfo',
    ];

    /**
     * Constructor.
     *
     * @param mixed[] $data Associated array of property values
     *                      initializing the model
     */
    public function __construct(?array $data = null)
    {
        $this->setIfExists('directly_related_user_types', $data ?? [], null);
        $this->setIfExists('module', $data ?? [], null);
        $this->setIfExists('source_info', $data ?? [], null);
    }

    /**
     * Gets the string presentation of the object.
     *
     * @return string
     */
    public function __toString()
    {
        return json_encode(
            ObjectSerializer::sanitizeForSerialization($this),
            JSON_PRETTY_PRINT,
        );
    }

    /**
     * Gets directly_related_user_types.
     *
     * @return null|RelationReference[]
     */
    public function getDirectlyRelatedUserTypes()
    {
        return $this->container['directly_related_user_types'];
    }

    /**
     * The original name of the model.
     *
     * @return string
     */
    public function getModelName()
    {
        return self::$openAPIModelName;
    }

    /**
     * Gets module.
     *
     * @return null|string
     */
    public function getModule()
    {
        return $this->container['module'];
    }

    /**
     * Gets source_info.
     *
     * @return null|SourceInfo
     */
    public function getSourceInfo()
    {
        return $this->container['source_info'];
    }

    /**
     * Checks if a nullable property is set to null.
     *
     * @param string $property
     *
     * @return bool
     */
    public function isNullableSetToNull(string $property): bool
    {
        return in_array($property, $this->getOpenAPINullablesSetToNull(), true);
    }

    /**
     * Serializes the object to a value that can be serialized natively by json_encode().
     *
     * @link https://www.php.net/manual/en/jsonserializable.jsonserialize.php
     *
     * @return mixed Returns data which can be serialized by json_encode(), which is a value
     *               of any type other than a resource.
     */
    #[ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return ObjectSerializer::sanitizeForSerialization($this);
    }

    /**
     * Show all the invalid properties with reasons.
     *
     * @return array invalid properties with reasons
     */
    public function listInvalidProperties()
    {
        return [];
    }

    /**
     * Returns true if offset exists. False otherwise.
     *
     * @param int $offset Offset
     *
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return isset($this->container[$offset]);
    }

    /**
     * Gets offset.
     *
     * @param int $offset Offset
     *
     * @return null|mixed
     */
    #[ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->container[$offset] ?? null;
    }

    /**
     * Sets value based on offset.
     *
     * @param null|int $offset Offset
     * @param mixed    $value  Value to be set
     */
    public function offsetSet($offset, $value): void
    {
        if (null === $offset) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
    }

    /**
     * Unsets offset.
     *
     * @param int $offset Offset
     */
    public function offsetUnset($offset): void
    {
        unset($this->container[$offset]);
    }

    /**
     * Sets directly_related_user_types.
     *
     * @param null|RelationReference[] $directly_related_user_types directly_related_user_types
     *
     * @return self
     */
    public function setDirectlyRelatedUserTypes($directly_related_user_types)
    {
        if (null === $directly_related_user_types) {
            throw new InvalidArgumentException('non-nullable directly_related_user_types cannot be null');
        }
        $this->container['directly_related_user_types'] = $directly_related_user_types;

        return $this;
    }

    /**
     * Sets module.
     *
     * @param null|string $module module
     *
     * @return self
     */
    public function setModule($module)
    {
        if (null === $module) {
            throw new InvalidArgumentException('non-nullable module cannot be null');
        }
        $this->container['module'] = $module;

        return $this;
    }

    /**
     * Sets source_info.
     *
     * @param null|SourceInfo $source_info source_info
     *
     * @return self
     */
    public function setSourceInfo($source_info)
    {
        if (null === $source_info) {
            throw new InvalidArgumentException('non-nullable source_info cannot be null');
        }
        $this->container['source_info'] = $source_info;

        return $this;
    }

    /**
     * Gets a header-safe presentation of the object.
     *
     * @return string
     */
    public function toHeaderValue()
    {
        return json_encode(ObjectSerializer::sanitizeForSerialization($this));
    }

    /**
     * Validate all the properties in the model
     * return true if all passed.
     *
     * @return bool True if all properties are valid
     */
    public function valid()
    {
        return 0 === count($this->listInvalidProperties());
    }

    /**
     * Array of attributes where the key is the local name,
     * and the value is the original name.
     *
     * @return array
     */
    public static function attributeMap()
    {
        return self::$attributeMap;
    }

    /**
     * Array of attributes to getter functions (for serialization of requests).
     *
     * @return array
     */
    public static function getters()
    {
        return self::$getters;
    }

    /**
     * Checks if a property is nullable.
     *
     * @param string $property
     *
     * @return bool
     */
    public static function isNullable(string $property): bool
    {
        return self::openAPINullables()[$property] ?? false;
    }

    /**
     * Array of property to format mappings. Used for (de)serialization.
     *
     * @return array
     */
    public static function openAPIFormats()
    {
        return self::$openAPIFormats;
    }

    /**
     * Array of property to type mappings. Used for (de)serialization.
     *
     * @return array
     */
    public static function openAPITypes()
    {
        return self::$openAPITypes;
    }

    /**
     * Array of attributes to setter functions (for deserialization of responses).
     *
     * @return array
     */
    public static function setters()
    {
        return self::$setters;
    }

    /**
     * Array of nullable field names deliberately set to null.
     *
     * @return bool[]
     */
    private function getOpenAPINullablesSetToNull(): array
    {
        return $this->openAPINullablesSetToNull;
    }

    /**
     * Sets $this->container[$variableName] to the given data or to the given default Value; if $variableName
     * is nullable and its value is set to null in the $fields array, then mark it as "set to null" in the
     * $this->openAPINullablesSetToNull array.
     *
     * @param string $variableName
     * @param array  $fields
     * @param mixed  $defaultValue
     */
    private function setIfExists(string $variableName, array $fields, $defaultValue): void
    {
        if (self::isNullable($variableName) && array_key_exists($variableName, $fields) && null === $fields[$variableName]) {
            $this->openAPINullablesSetToNull[] = $variableName;
        }

        $this->container[$variableName] = $fields[$variableName] ?? $defaultValue;
    }

    /**
     * Setter - Array of nullable field names deliberately set to null.
     *
     * @param bool[] $openAPINullablesSetToNull
     */
    private function setOpenAPINullablesSetToNull(array $openAPINullablesSetToNull): void
    {
        $this->openAPINullablesSetToNull = $openAPINullablesSetToNull;
    }

    /**
     * Array of nullable properties.
     *
     * @return array
     */
    private static function openAPINullables(): array
    {
        return self::$openAPINullables;
    }
}