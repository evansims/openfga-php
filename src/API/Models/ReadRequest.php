<?php

declare(strict_types=1);
/**
 * ReadRequest.
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
 * ReadRequest Class Doc Comment.
 *
 * @category Class
 *
 * @author   OpenAPI Generator team
 *
 * @link     https://openapi-generator.tech
 *
 * @implements \ArrayAccess<string, mixed>
 */
final class ReadRequest implements ArrayAccess, JsonSerializable, ModelInterface
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
        'tuple_key' => 'tuple_key',
        'page_size' => 'page_size',
        'continuation_token' => 'continuation_token',
        'consistency' => 'consistency',
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests).
     *
     * @var string[]
     */
    private static $getters = [
        'tuple_key' => 'getTupleKey',
        'page_size' => 'getPageSize',
        'continuation_token' => 'getContinuationToken',
        'consistency' => 'getConsistency',
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
        'tuple_key' => null,
        'page_size' => 'int32',
        'continuation_token' => null,
        'consistency' => null,
    ];

    /**
     * The original name of the model.
     *
     * @var string
     */
    private static $openAPIModelName = 'Read_request';

    /**
     * Array of nullable properties. Used for (de)serialization.
     *
     * @var bool[]
     */
    private static array $openAPINullables = [
        'tuple_key' => false,
        'page_size' => false,
        'continuation_token' => false,
        'consistency' => false,
    ];

    /**
     * Array of property to type mappings. Used for (de)serialization.
     *
     * @var string[]
     */
    private static $openAPITypes = [
        'tuple_key' => '\OpenFGA\API\Models\ReadRequestTupleKey',
        'page_size' => 'int',
        'continuation_token' => 'string',
        'consistency' => '\OpenFGA\API\Models\ConsistencyPreference',
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses).
     *
     * @var string[]
     */
    private static $setters = [
        'tuple_key' => 'setTupleKey',
        'page_size' => 'setPageSize',
        'continuation_token' => 'setContinuationToken',
        'consistency' => 'setConsistency',
    ];

    /**
     * Constructor.
     *
     * @param mixed[] $data Associated array of property values
     *                      initializing the model
     */
    public function __construct(?array $data = null)
    {
        $this->setIfExists('tuple_key', $data ?? [], null);
        $this->setIfExists('page_size', $data ?? [], null);
        $this->setIfExists('continuation_token', $data ?? [], null);
        $this->setIfExists('consistency', $data ?? [], null);
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
     * Gets consistency.
     *
     * @return null|ConsistencyPreference
     */
    public function getConsistency()
    {
        return $this->container['consistency'];
    }

    /**
     * Gets continuation_token.
     *
     * @return null|string
     */
    public function getContinuationToken()
    {
        return $this->container['continuation_token'];
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
     * Gets page_size.
     *
     * @return null|int
     */
    public function getPageSize()
    {
        return $this->container['page_size'];
    }

    /**
     * Gets tuple_key.
     *
     * @return null|ReadRequestTupleKey
     */
    public function getTupleKey()
    {
        return $this->container['tuple_key'];
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

        if (null !== $this->container['page_size'] && ($this->container['page_size'] > 100)) {
            $invalidProperties[] = "invalid value for 'page_size', must be smaller than or equal to 100.";
        }

        if (null !== $this->container['page_size'] && ($this->container['page_size'] < 1)) {
            $invalidProperties[] = "invalid value for 'page_size', must be bigger than or equal to 1.";
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
     * Sets consistency.
     *
     * @param null|ConsistencyPreference $consistency consistency
     *
     * @return self
     */
    public function setConsistency($consistency)
    {
        if (null === $consistency) {
            throw new InvalidArgumentException('non-nullable consistency cannot be null');
        }
        $this->container['consistency'] = $consistency;

        return $this;
    }

    /**
     * Sets continuation_token.
     *
     * @param null|string $continuation_token continuation_token
     *
     * @return self
     */
    public function setContinuationToken($continuation_token)
    {
        if (null === $continuation_token) {
            throw new InvalidArgumentException('non-nullable continuation_token cannot be null');
        }
        $this->container['continuation_token'] = $continuation_token;

        return $this;
    }

    /**
     * Sets page_size.
     *
     * @param null|int $page_size page_size
     *
     * @return self
     */
    public function setPageSize($page_size)
    {
        if (null === $page_size) {
            throw new InvalidArgumentException('non-nullable page_size cannot be null');
        }

        if (($page_size > 100)) {
            throw new InvalidArgumentException('invalid value for $page_size when calling ReadRequest., must be smaller than or equal to 100.');
        }
        if (($page_size < 1)) {
            throw new InvalidArgumentException('invalid value for $page_size when calling ReadRequest., must be bigger than or equal to 1.');
        }

        $this->container['page_size'] = $page_size;

        return $this;
    }

    /**
     * Sets tuple_key.
     *
     * @param null|ReadRequestTupleKey $tuple_key tuple_key
     *
     * @return self
     */
    public function setTupleKey($tuple_key)
    {
        if (null === $tuple_key) {
            throw new InvalidArgumentException('non-nullable tuple_key cannot be null');
        }
        $this->container['tuple_key'] = $tuple_key;

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