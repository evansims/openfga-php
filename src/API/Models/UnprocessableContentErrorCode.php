<?php

declare(strict_types=1);
/**
 * UnprocessableContentErrorCode.
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
 * UnprocessableContentErrorCode Class Doc Comment.
 *
 * @category Class
 *
 * @author   OpenAPI Generator team
 *
 * @link     https://openapi-generator.tech
 */
final class UnprocessableContentErrorCode
{
    /**
     * Possible values of this enum.
     */
    public const NO_THROTTLED_ERROR_CODE = 'no_throttled_error_code';

    public const THROTTLED_TIMEOUT_ERROR = 'throttled_timeout_error';

    /**
     * Gets allowable values of the enum.
     *
     * @return string[]
     */
    public static function getAllowableEnumValues()
    {
        return [
            self::NO_THROTTLED_ERROR_CODE,
            self::THROTTLED_TIMEOUT_ERROR,
        ];
    }
}