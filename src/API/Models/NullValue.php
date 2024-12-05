<?php

declare(strict_types=1);
/**
 * NullValue.
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
 * NullValue Class Doc Comment.
 *
 * @category Class
 *
 * @description &#x60;NullValue&#x60; is a singleton enumeration to represent the null value for the &#x60;Value&#x60; type union.  The JSON representation for &#x60;NullValue&#x60; is JSON &#x60;null&#x60;.   - NULL_VALUE: Null value.
 *
 * @author   OpenAPI Generator team
 *
 * @link     https://openapi-generator.tech
 */
final class NullValue
{
    /**
     * Possible values of this enum.
     */
    public const NULL_VALUE = 'NULL_VALUE';

    /**
     * Gets allowable values of the enum.
     *
     * @return string[]
     */
    public static function getAllowableEnumValues()
    {
        return [
            self::NULL_VALUE,
        ];
    }
}