<?php

declare(strict_types=1);
/**
 * AuthErrorCode.
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

/**
 * AuthErrorCode Class Doc Comment.
 *
 * @category Class
 *
 * @author   OpenAPI Generator team
 *
 * @link     https://openapi-generator.tech
 */
final class AuthErrorCode
{
    public const AUTH_FAILED_INVALID_AUDIENCE = 'auth_failed_invalid_audience';

    public const AUTH_FAILED_INVALID_BEARER_TOKEN = 'auth_failed_invalid_bearer_token';

    public const AUTH_FAILED_INVALID_ISSUER = 'auth_failed_invalid_issuer';

    public const AUTH_FAILED_INVALID_SUBJECT = 'auth_failed_invalid_subject';

    public const BEARER_TOKEN_MISSING = 'bearer_token_missing';

    public const FORBIDDEN = 'forbidden';

    public const INVALID_CLAIMS = 'invalid_claims';

    /**
     * Possible values of this enum.
     */
    public const NO_AUTH_ERROR = 'no_auth_error';

    public const UNAUTHENTICATED = 'unauthenticated';

    /**
     * Gets allowable values of the enum.
     *
     * @return string[]
     */
    public static function getAllowableEnumValues()
    {
        return [
            self::NO_AUTH_ERROR,
            self::AUTH_FAILED_INVALID_SUBJECT,
            self::AUTH_FAILED_INVALID_AUDIENCE,
            self::AUTH_FAILED_INVALID_ISSUER,
            self::INVALID_CLAIMS,
            self::AUTH_FAILED_INVALID_BEARER_TOKEN,
            self::BEARER_TOKEN_MISSING,
            self::UNAUTHENTICATED,
            self::FORBIDDEN,
        ];
    }
}
