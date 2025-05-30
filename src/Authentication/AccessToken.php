<?php

declare(strict_types=1);

namespace OpenFGA\Authentication;

use const JSON_THROW_ON_ERROR;

use InvalidArgumentException;
use JsonException;
use OpenFGA\Exceptions\{ClientThrowable, SerializationError};
use OpenFGA\Messages;
use OpenFGA\Translation\Translator;
use Override;
use Psr\Http\Message\ResponseInterface;
use ReflectionException;

use function base64_decode;
use function count;
use function explode;
use function is_array;
use function is_float;
use function is_int;
use function is_string;
use function json_decode;
use function preg_match;
use function str_repeat;
use function str_replace;
use function strlen;
use function time;

/**
 * Immutable access token implementation for OpenFGA API authentication.
 *
 * This class represents an OAuth 2.0 access token with expiration tracking
 * and scope management. Access tokens are typically obtained through OAuth
 * flows and provide time-limited access to OpenFGA resources.
 *
 * @see AccessTokenInterface For the complete access token contract
 */
final readonly class AccessToken implements AccessTokenInterface
{
    /**
     * Create a new access token instance.
     *
     * Creates an immutable access token with the provided credentials and metadata.
     * The expiration timestamp should be calculated as the current time plus the
     * token's lifetime in seconds (expires_in from OAuth response).
     *
     * @param string      $token   The access token value obtained from the authentication server
     * @param int         $expires Unix timestamp when the token expires and should no longer be used
     * @param string|null $scope   The optional token scope defining granted permissions, or null if no scope restrictions apply
     */
    public function __construct(
        private string $token,
        private int $expires,
        private ?string $scope = null,
    ) {
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function __toString(): string
    {
        return $this->token;
    }

    /**
     * @inheritDoc
     *
     * @throws InvalidArgumentException If message translation parameters are invalid
     * @throws ReflectionException      If location capture fails during exception creation
     */
    #[Override]
    public static function fromResponse(ResponseInterface $response, ?string $expectedIssuer = null, ?string $expectedAudience = null): self
    {
        $body = $response->getBody()->getContents();
        $data = json_decode($body, true, 512, JSON_THROW_ON_ERROR);

        if (! is_array($data)) {
            throw SerializationError::Response->exception(response: $response, context: ['message' => Translator::trans(Messages::AUTH_INVALID_RESPONSE_FORMAT)]);
        }

        if (! isset($data['access_token'], $data['expires_in'])) {
            throw SerializationError::Response->exception(response: $response, context: ['message' => Translator::trans(Messages::AUTH_MISSING_REQUIRED_FIELDS)]);
        }

        if (! is_string($data['access_token'])) {
            throw SerializationError::InvalidItemType->exception(response: $response, context: ['message' => Translator::trans(Messages::AUTH_ACCESS_TOKEN_MUST_BE_STRING)]);
        }

        if (! is_int($data['expires_in'])) {
            throw SerializationError::InvalidItemType->exception(response: $response, context: ['message' => Translator::trans(Messages::AUTH_EXPIRES_IN_MUST_BE_INTEGER)]);
        }

        // Validate JWT token if it looks like a JWT
        $accessToken = $data['access_token'];
        if (self::isJwtToken($accessToken)) {
            self::validateJwtToken($accessToken, $response, $expectedIssuer, $expectedAudience);
        }

        return new self(
            token: $accessToken,
            expires: time() + $data['expires_in'],
            scope: isset($data['scope']) && is_string($data['scope']) ? $data['scope'] : null,
        );
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getExpires(): int
    {
        return $this->expires;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getScope(): ?string
    {
        return $this->scope;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function isExpired(): bool
    {
        return $this->expires < time();
    }

    /**
     * Decode a JWT part (header or payload) from base64url encoding.
     *
     * @param string            $encoded      The base64url encoded part
     * @param ResponseInterface $response     The original response for error context
     * @param Messages          $errorMessage The error message to use if decoding fails
     *
     * @throws ClientThrowable          If decoding fails
     * @throws InvalidArgumentException If message translation parameters are invalid
     * @throws ReflectionException      If location capture fails during exception creation
     *
     * @return mixed The decoded data
     */
    private static function decodeJwtPart(string $encoded, ResponseInterface $response, Messages $errorMessage): mixed
    {
        // Convert base64url to base64
        $base64 = str_replace(['-', '_'], ['+', '/'], $encoded);

        // Add padding if needed
        $padding = strlen($base64) % 4;
        if (0 < $padding) {
            $base64 .= str_repeat('=', 4 - $padding);
        }

        $decoded = base64_decode($base64, true);
        if (false === $decoded) {
            throw SerializationError::InvalidItemType->exception(response: $response, context: ['message' => Translator::trans($errorMessage)]);
        }

        try {
            return json_decode($decoded, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            throw SerializationError::InvalidItemType->exception(response: $response, context: ['message' => Translator::trans($errorMessage)]);
        }
    }

    /**
     * Check if a token appears to be a JWT token.
     *
     * Performs a basic check to determine if the token follows JWT format
     * (three base64url-encoded parts separated by dots).
     *
     * @param  string $token The token to check
     * @return bool   True if the token appears to be a JWT, false otherwise
     */
    private static function isJwtToken(string $token): bool
    {
        return 1 === preg_match('/^[A-Za-z0-9_-]+\.[A-Za-z0-9_-]+\.[A-Za-z0-9_-]+$/', $token);
    }

    /**
     * Validate JWT claims including expiration, not-before times, issuer, and audience.
     *
     * @param array<string, mixed> $payload          The decoded JWT payload
     * @param ResponseInterface    $response         The original response for error context
     * @param string|null          $expectedIssuer   Expected issuer for validation
     * @param string|null          $expectedAudience Expected audience for validation
     *
     * @throws ClientThrowable          If claims are invalid
     * @throws InvalidArgumentException If message translation parameters are invalid
     * @throws ReflectionException      If location capture fails during exception creation
     */
    private static function validateJwtClaims(array $payload, ResponseInterface $response, ?string $expectedIssuer = null, ?string $expectedAudience = null): void
    {
        $currentTime = time();

        // Check expiration time (exp claim)
        if (isset($payload['exp'])) {
            if (! is_int($payload['exp']) && ! is_float($payload['exp'])) {
                throw SerializationError::InvalidItemType->exception(response: $response, context: ['message' => Translator::trans(Messages::JWT_INVALID_PAYLOAD)]);
            }

            if ($payload['exp'] <= $currentTime) {
                throw SerializationError::InvalidItemType->exception(response: $response, context: ['message' => Translator::trans(Messages::JWT_TOKEN_EXPIRED)]);
            }
        }

        // Check not-before time (nbf claim)
        if (isset($payload['nbf'])) {
            if (! is_int($payload['nbf']) && ! is_float($payload['nbf'])) {
                throw SerializationError::InvalidItemType->exception(response: $response, context: ['message' => Translator::trans(Messages::JWT_INVALID_PAYLOAD)]);
            }

            if ($payload['nbf'] > $currentTime) {
                throw SerializationError::InvalidItemType->exception(response: $response, context: ['message' => Translator::trans(Messages::JWT_TOKEN_NOT_YET_VALID)]);
            }
        }

        // Check issued at time (iat claim) - should not be in the future
        if (isset($payload['iat'])) {
            if (! is_int($payload['iat']) && ! is_float($payload['iat'])) {
                throw SerializationError::InvalidItemType->exception(response: $response, context: ['message' => Translator::trans(Messages::JWT_INVALID_PAYLOAD)]);
            }

            // Allow some clock skew (5 minutes)
            if ($payload['iat'] > $currentTime + 300) {
                throw SerializationError::InvalidItemType->exception(response: $response, context: ['message' => Translator::trans(Messages::JWT_INVALID_PAYLOAD)]);
            }
        }

        // Validate issuer claim (iss)
        if (null !== $expectedIssuer) {
            if (! isset($payload['iss'])) {
                throw SerializationError::InvalidItemType->exception(response: $response, context: ['message' => Translator::trans(Messages::JWT_INVALID_ISSUER)]);
            }

            if (! is_string($payload['iss'])) {
                throw SerializationError::InvalidItemType->exception(response: $response, context: ['message' => Translator::trans(Messages::JWT_INVALID_PAYLOAD)]);
            }

            if ($payload['iss'] !== $expectedIssuer) {
                throw SerializationError::InvalidItemType->exception(response: $response, context: ['message' => Translator::trans(Messages::JWT_INVALID_ISSUER)]);
            }
        }

        // Validate audience claim (aud)
        if (null !== $expectedAudience) {
            if (! isset($payload['aud'])) {
                throw SerializationError::InvalidItemType->exception(response: $response, context: ['message' => Translator::trans(Messages::JWT_INVALID_AUDIENCE)]);
            }

            // Audience can be a string or array of strings
            $audiences = is_array($payload['aud']) ? $payload['aud'] : [$payload['aud']];

            $audienceMatched = false;

            /** @var mixed $audience */
            foreach ($audiences as $audience) {
                if (is_string($audience) && $audience === $expectedAudience) {
                    $audienceMatched = true;

                    break;
                }
            }

            if (! $audienceMatched) {
                throw SerializationError::InvalidItemType->exception(response: $response, context: ['message' => Translator::trans(Messages::JWT_INVALID_AUDIENCE)]);
            }
        }
    }

    /**
     * Validate JWT token format and basic structure.
     *
     * Performs comprehensive validation of a JWT token without requiring
     * third-party dependencies. Validates structure, encoding, and basic
     * claims including expiration, not-before times, issuer, and audience.
     *
     * @param string            $token            The JWT token to validate
     * @param ResponseInterface $response         The original response for error context
     * @param string|null       $expectedIssuer   Expected issuer for validation
     * @param string|null       $expectedAudience Expected audience for validation
     *
     * @throws ClientThrowable          If the JWT token is invalid
     * @throws InvalidArgumentException If message translation parameters are invalid
     * @throws ReflectionException      If location capture fails during exception creation
     */
    private static function validateJwtToken(string $token, ResponseInterface $response, ?string $expectedIssuer = null, ?string $expectedAudience = null): void
    {
        // Check if token looks like a JWT (has 3 parts separated by dots)
        if (1 !== preg_match('/^[A-Za-z0-9_-]+\.[A-Za-z0-9_-]+\.[A-Za-z0-9_-]+$/', $token)) {
            throw SerializationError::InvalidItemType->exception(response: $response, context: ['message' => Translator::trans(Messages::JWT_INVALID_FORMAT)]);
        }

        $parts = explode('.', $token);
        if (3 !== count($parts)) {
            throw SerializationError::InvalidItemType->exception(response: $response, context: ['message' => Translator::trans(Messages::JWT_INVALID_FORMAT)]);
        }

        [$headerEncoded, $payloadEncoded] = $parts;

        // Validate and decode header
        $header = self::decodeJwtPart($headerEncoded, $response, Messages::JWT_INVALID_HEADER);
        if (! is_array($header) || ! isset($header['typ']) || ! isset($header['alg'])) {
            throw SerializationError::InvalidItemType->exception(response: $response, context: ['message' => Translator::trans(Messages::JWT_INVALID_HEADER)]);
        }

        // Validate header type
        if ('JWT' !== $header['typ']) {
            throw SerializationError::InvalidItemType->exception(response: $response, context: ['message' => Translator::trans(Messages::JWT_INVALID_HEADER)]);
        }

        // Validate and decode payload
        $payload = self::decodeJwtPart($payloadEncoded, $response, Messages::JWT_INVALID_PAYLOAD);
        if (! is_array($payload)) {
            throw SerializationError::InvalidItemType->exception(response: $response, context: ['message' => Translator::trans(Messages::JWT_INVALID_PAYLOAD)]);
        }

        // Validate required claims
        /** @var array<string, mixed> $payload */
        self::validateJwtClaims($payload, $response, $expectedIssuer, $expectedAudience);
    }
}
