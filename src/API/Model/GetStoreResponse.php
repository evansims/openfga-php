<?php

declare(strict_types=1);
/**
 * GetStoreResponse.
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

namespace OpenFGA\API\Model;

use ArrayAccess;
use DateTime;
use InvalidArgumentException;
use JsonSerializable;
use OpenFGA\API\ObjectSerializer;
use ReturnTypeWillChange;

use function array_key_exists;
use function count;
use function in_array;

/**
 * GetStoreResponse Class Doc Comment.
 *
 * @category Class
 *
 * @author   OpenAPI Generator team
 *
 * @link     https://openapi-generator.tech
 *
 * @implements \ArrayAccess<string, mixed>
 */
final class GetStoreResponse implements ArrayAccess, JsonSerializable, ModelInterface
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
        'id' => 'id',
        'name' => 'name',
        'createdAt' => 'created_at',
        'updatedAt' => 'updated_at',
        'deletedAt' => 'deleted_at',
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests).
     *
     * @var string[]
     */
    private static $getters = [
        'id' => 'getId',
        'name' => 'getName',
        'createdAt' => 'getCreatedAt',
        'updatedAt' => 'getUpdatedAt',
        'deletedAt' => 'getDeletedAt',
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
        'id' => null,
        'name' => null,
        'createdAt' => 'date-time',
        'updatedAt' => 'date-time',
        'deletedAt' => 'date-time',
    ];

    /**
     * The original name of the model.
     *
     * @var string
     */
    private static $openAPIModelName = 'GetStoreResponse';

    /**
     * Array of nullable properties. Used for (de)serialization.
     *
     * @var bool[]
     */
    private static array $openAPINullables = [
        'id' => false,
        'name' => false,
        'createdAt' => false,
        'updatedAt' => false,
        'deletedAt' => false,
    ];

    /**
     * Array of property to type mappings. Used for (de)serialization.
     *
     * @var string[]
     */
    private static $openAPITypes = [
        'id' => 'string',
        'name' => 'string',
        'createdAt' => '\DateTime',
        'updatedAt' => '\DateTime',
        'deletedAt' => '\DateTime',
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses).
     *
     * @var string[]
     */
    private static $setters = [
        'id' => 'setId',
        'name' => 'setName',
        'createdAt' => 'setCreatedAt',
        'updatedAt' => 'setUpdatedAt',
        'deletedAt' => 'setDeletedAt',
    ];

    /**
     * Constructor.
     *
     * @param mixed[] $data Associated array of property values
     *                      initializing the model
     */
    public function __construct(?array $data = null)
    {
        $this->setIfExists('id', $data ?? [], null);
        $this->setIfExists('name', $data ?? [], null);
        $this->setIfExists('createdAt', $data ?? [], null);
        $this->setIfExists('updatedAt', $data ?? [], null);
        $this->setIfExists('deletedAt', $data ?? [], null);
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
     * Gets createdAt.
     *
     * @return DateTime
     */
    public function getCreatedAt()
    {
        return $this->container['createdAt'];
    }

    /**
     * Gets deletedAt.
     *
     * @return null|DateTime
     */
    public function getDeletedAt()
    {
        return $this->container['deletedAt'];
    }

    /**
     * Gets id.
     *
     * @return string
     */
    public function getId()
    {
        return $this->container['id'];
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
     * Gets name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->container['name'];
    }

    /**
     * Gets updatedAt.
     *
     * @return DateTime
     */
    public function getUpdatedAt()
    {
        return $this->container['updatedAt'];
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
        $invalidProperties = [];

        if (null === $this->container['id']) {
            $invalidProperties[] = "'id' can't be null";
        }
        if (null === $this->container['name']) {
            $invalidProperties[] = "'name' can't be null";
        }
        if (null === $this->container['createdAt']) {
            $invalidProperties[] = "'createdAt' can't be null";
        }
        if (null === $this->container['updatedAt']) {
            $invalidProperties[] = "'updatedAt' can't be null";
        }

        return $invalidProperties;
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
     * Sets createdAt.
     *
     * @param DateTime $createdAt createdAt
     *
     * @return self
     */
    public function setCreatedAt($createdAt)
    {
        if (null === $createdAt) {
            throw new InvalidArgumentException('non-nullable createdAt cannot be null');
        }
        $this->container['createdAt'] = $createdAt;

        return $this;
    }

    /**
     * Sets deletedAt.
     *
     * @param null|DateTime $deletedAt deletedAt
     *
     * @return self
     */
    public function setDeletedAt($deletedAt)
    {
        if (null === $deletedAt) {
            throw new InvalidArgumentException('non-nullable deletedAt cannot be null');
        }
        $this->container['deletedAt'] = $deletedAt;

        return $this;
    }

    /**
     * Sets id.
     *
     * @param string $id id
     *
     * @return self
     */
    public function setId($id)
    {
        if (null === $id) {
            throw new InvalidArgumentException('non-nullable id cannot be null');
        }
        $this->container['id'] = $id;

        return $this;
    }

    /**
     * Sets name.
     *
     * @param string $name name
     *
     * @return self
     */
    public function setName($name)
    {
        if (null === $name) {
            throw new InvalidArgumentException('non-nullable name cannot be null');
        }
        $this->container['name'] = $name;

        return $this;
    }

    /**
     * Sets updatedAt.
     *
     * @param DateTime $updatedAt updatedAt
     *
     * @return self
     */
    public function setUpdatedAt($updatedAt)
    {
        if (null === $updatedAt) {
            throw new InvalidArgumentException('non-nullable updatedAt cannot be null');
        }
        $this->container['updatedAt'] = $updatedAt;

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
