<?php

declare(strict_types=1);
/**
 * TypeName.
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

/**
 * TypeName Class Doc Comment.
 *
 * @category Class
 *
 * @author   OpenAPI Generator team
 *
 * @link     https://openapi-generator.tech
 */
final class TypeName
{
    public const _LIST = 'TYPE_NAME_LIST';

    public const ANY = 'TYPE_NAME_ANY';

    public const BOOL = 'TYPE_NAME_BOOL';

    public const DOUBLE = 'TYPE_NAME_DOUBLE';

    public const DURATION = 'TYPE_NAME_DURATION';

    public const INT = 'TYPE_NAME_INT';

    public const IPADDRESS = 'TYPE_NAME_IPADDRESS';

    public const MAP = 'TYPE_NAME_MAP';

    public const STRING = 'TYPE_NAME_STRING';

    public const TIMESTAMP = 'TYPE_NAME_TIMESTAMP';

    public const UINT = 'TYPE_NAME_UINT';

    /**
     * Possible values of this enum.
     */
    public const UNSPECIFIED = 'TYPE_NAME_UNSPECIFIED';

    /**
     * Gets allowable values of the enum.
     *
     * @return string[]
     */
    public static function getAllowableEnumValues()
    {
        return [
            self::UNSPECIFIED,
            self::ANY,
            self::BOOL,
            self::STRING,
            self::INT,
            self::UINT,
            self::DOUBLE,
            self::DURATION,
            self::TIMESTAMP,
            self::MAP,
            self::_LIST,
            self::IPADDRESS,
        ];
    }
}
