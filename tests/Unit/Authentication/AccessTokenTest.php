<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Authentication;

use JsonException;
use OpenFGA\Authentication\{AccessToken, AccessTokenInterface};
use OpenFGA\Exceptions\SerializationException;
use OpenFGA\Messages;
use Psr\Http\Message\{ResponseInterface, StreamInterface};

// Helper functions for JWT testing
function createMockResponse(array $data): ResponseInterface
{
    $stream = test()->createMock(StreamInterface::class);
    $stream->method('getContents')
        ->willReturn(json_encode($data));

    $response = test()->createMock(ResponseInterface::class);
    $response->method('getBody')
        ->willReturn($stream);

    return $response;
}

function base64url_encode(string $data): string
{
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

function createValidJwt(array $header, array $payload): string
{
    $headerEncoded = base64url_encode(json_encode($header));
    $payloadEncoded = base64url_encode(json_encode($payload));

    // For testing purposes, we don't need a valid signature
    $signature = base64url_encode('fake_signature');

    return $headerEncoded . '.' . $payloadEncoded . '.' . $signature;
}

describe('AccessToken', function (): void {
    test('implements AccessTokenInterface', function (): void {
        $token = new AccessToken('token123', time() + 3600);

        expect($token)->toBeInstanceOf(AccessTokenInterface::class);
    });

    test('constructs with required parameters', function (): void {
        $tokenValue = 'access_token_123';
        $expiresAt = time() + 3600;

        $token = new AccessToken($tokenValue, $expiresAt);

        expect($token->getToken())->toBe($tokenValue);
        expect($token->getExpires())->toBe($expiresAt);
        expect($token->getScope())->toBeNull();
    });

    test('constructs with scope', function (): void {
        $tokenValue = 'access_token_123';
        $expiresAt = time() + 3600;
        $scope = 'read write';

        $token = new AccessToken($tokenValue, $expiresAt, $scope);

        expect($token->getToken())->toBe($tokenValue);
        expect($token->getExpires())->toBe($expiresAt);
        expect($token->getScope())->toBe($scope);
    });

    test('toString returns token value', function (): void {
        $tokenValue = 'access_token_123';
        $token = new AccessToken($tokenValue, time() + 3600);

        expect((string) $token)->toBe($tokenValue);
    });

    test('isExpired returns false for future expiration', function (): void {
        $token = new AccessToken('token', time() + 3600); // 1 hour from now

        expect($token->isExpired())->toBeFalse();
    });

    test('isExpired returns true for past expiration', function (): void {
        $token = new AccessToken('token', time() - 3600); // 1 hour ago

        expect($token->isExpired())->toBeTrue();
    });

    test('isExpired returns true for current time', function (): void {
        $currentTime = time();
        $token = new AccessToken('token', $currentTime);

        // At exact expiration time, token is expired (expires < time())
        expect($token->isExpired())->toBeFalse();

        // One second past expiration, token should be expired
        $expiredToken = new AccessToken('token', $currentTime - 1);
        expect($expiredToken->isExpired())->toBeTrue();
    });

    test('fromResponse parses valid response', function (): void {
        $responseData = [
            'access_token' => 'test_token_123',
            'expires_in' => 3600,
            'scope' => 'read write',
        ];

        $stream = test()->createMock(StreamInterface::class);
        $stream->method('getContents')
            ->willReturn(json_encode($responseData));

        $response = test()->createMock(ResponseInterface::class);
        $response->method('getBody')
            ->willReturn($stream);

        $beforeTime = time();
        $token = AccessToken::fromResponse($response);
        $afterTime = time();

        expect($token)->toBeInstanceOf(AccessToken::class);
        expect($token->getToken())->toBe('test_token_123');
        expect($token->getExpires())->toBeGreaterThanOrEqual($beforeTime + 3600);
        expect($token->getExpires())->toBeLessThanOrEqual($afterTime + 3600);
        expect($token->getScope())->toBe('read write');
    });

    test('fromResponse handles response without scope', function (): void {
        $responseData = [
            'access_token' => 'test_token_456',
            'expires_in' => 7200,
        ];

        $stream = test()->createMock(StreamInterface::class);
        $stream->method('getContents')
            ->willReturn(json_encode($responseData));

        $response = test()->createMock(ResponseInterface::class);
        $response->method('getBody')
            ->willReturn($stream);

        $token = AccessToken::fromResponse($response);

        expect($token->getToken())->toBe('test_token_456');
        expect($token->getScope())->toBeNull();
    });

    test('fromResponse throws on invalid JSON', function (): void {
        $stream = test()->createMock(StreamInterface::class);
        $stream->method('getContents')
            ->willReturn('invalid json');

        $response = test()->createMock(ResponseInterface::class);
        $response->method('getBody')
            ->willReturn($stream);

        AccessToken::fromResponse($response);
    })->throws(JsonException::class);

    test('fromResponse throws on non-array response', function (): void {
        $stream = test()->createMock(StreamInterface::class);
        $stream->method('getContents')
            ->willReturn('"not an array"');

        $response = test()->createMock(ResponseInterface::class);
        $response->method('getBody')
            ->willReturn($stream);

        AccessToken::fromResponse($response);
    })->throws(SerializationException::class, trans(Messages::AUTH_INVALID_RESPONSE_FORMAT));

    test('fromResponse throws on missing access_token', function (): void {
        $responseData = [
            'expires_in' => 3600,
        ];

        $stream = test()->createMock(StreamInterface::class);
        $stream->method('getContents')
            ->willReturn(json_encode($responseData));

        $response = test()->createMock(ResponseInterface::class);
        $response->method('getBody')
            ->willReturn($stream);

        AccessToken::fromResponse($response);
    })->throws(SerializationException::class, trans(Messages::AUTH_MISSING_REQUIRED_FIELDS));

    test('fromResponse throws on missing expires_in', function (): void {
        $responseData = [
            'access_token' => 'test_token',
        ];

        $stream = test()->createMock(StreamInterface::class);
        $stream->method('getContents')
            ->willReturn(json_encode($responseData));

        $response = test()->createMock(ResponseInterface::class);
        $response->method('getBody')
            ->willReturn($stream);

        AccessToken::fromResponse($response);
    })->throws(SerializationException::class, trans(Messages::AUTH_MISSING_REQUIRED_FIELDS));

    test('fromResponse throws on non-string access_token', function (): void {
        $responseData = [
            'access_token' => 123,
            'expires_in' => 3600,
        ];

        $stream = test()->createMock(StreamInterface::class);
        $stream->method('getContents')
            ->willReturn(json_encode($responseData));

        $response = test()->createMock(ResponseInterface::class);
        $response->method('getBody')
            ->willReturn($stream);

        AccessToken::fromResponse($response);
    })->throws(SerializationException::class, trans(Messages::AUTH_ACCESS_TOKEN_MUST_BE_STRING));

    test('fromResponse throws on non-integer expires_in', function (): void {
        $responseData = [
            'access_token' => 'test_token',
            'expires_in' => '3600',
        ];

        $stream = test()->createMock(StreamInterface::class);
        $stream->method('getContents')
            ->willReturn(json_encode($responseData));

        $response = test()->createMock(ResponseInterface::class);
        $response->method('getBody')
            ->willReturn($stream);

        AccessToken::fromResponse($response);
    })->throws(SerializationException::class, trans(Messages::AUTH_EXPIRES_IN_MUST_BE_INTEGER));

    test('fromResponse handles non-string scope gracefully', function (): void {
        $responseData = [
            'access_token' => 'test_token',
            'expires_in' => 3600,
            'scope' => 123, // Invalid type
        ];

        $stream = test()->createMock(StreamInterface::class);
        $stream->method('getContents')
            ->willReturn(json_encode($responseData));

        $response = test()->createMock(ResponseInterface::class);
        $response->method('getBody')
            ->willReturn($stream);

        $token = AccessToken::fromResponse($response);

        expect($token->getScope())->toBeNull();
    });

    test('handles empty scope string', function (): void {
        $token = new AccessToken('token', time() + 3600, '');

        expect($token->getScope())->toBe('');
    });

    describe('JWT validation', function (): void {
        test('fromResponse validates valid JWT token with issuer and audience', function (): void {
            $currentTime = time();
            $header = ['typ' => 'JWT', 'alg' => 'HS256'];
            $payload = [
                'iss' => 'https://auth.example.com',
                'aud' => 'https://api.example.com',
                'exp' => $currentTime + 3600,
                'nbf' => $currentTime - 60,
                'iat' => $currentTime,
            ];

            $jwtToken = createValidJwt($header, $payload);
            $responseData = [
                'access_token' => $jwtToken,
                'expires_in' => 3600,
            ];

            $response = createMockResponse($responseData);

            $token = AccessToken::fromResponse(
                $response,
                'https://auth.example.com',
                'https://api.example.com',
            );

            expect($token)->toBeInstanceOf(AccessToken::class);
            expect($token->getToken())->toBe($jwtToken);
        });

        test('fromResponse validates JWT token with array audience', function (): void {
            $currentTime = time();
            $header = ['typ' => 'JWT', 'alg' => 'HS256'];
            $payload = [
                'iss' => 'https://auth.example.com',
                'aud' => ['https://api.example.com', 'https://other.example.com'],
                'exp' => $currentTime + 3600,
                'iat' => $currentTime,
            ];

            $jwtToken = createValidJwt($header, $payload);
            $responseData = [
                'access_token' => $jwtToken,
                'expires_in' => 3600,
            ];

            $response = createMockResponse($responseData);

            $token = AccessToken::fromResponse(
                $response,
                'https://auth.example.com',
                'https://api.example.com',
            );

            expect($token)->toBeInstanceOf(AccessToken::class);
        });

        test('fromResponse skips JWT validation for non-JWT tokens', function (): void {
            $responseData = [
                'access_token' => 'simple_bearer_token',
                'expires_in' => 3600,
            ];

            $response = createMockResponse($responseData);

            $token = AccessToken::fromResponse(
                $response,
                'https://auth.example.com',
                'https://api.example.com',
            );

            expect($token)->toBeInstanceOf(AccessToken::class);
            expect($token->getToken())->toBe('simple_bearer_token');
        });

        test('fromResponse skips validation for non-JWT format tokens', function (): void {
            $responseData = [
                'access_token' => 'invalid.jwt', // Only 2 parts - not a JWT format
                'expires_in' => 3600,
            ];

            $response = createMockResponse($responseData);

            // This won't trigger JWT validation since it's not a proper JWT format
            $token = AccessToken::fromResponse($response);
            expect($token)->toBeInstanceOf(AccessToken::class);
        });

        test('fromResponse skips validation for tokens with wrong number of parts', function (): void {
            // Create a token that looks like JWT but has wrong structure - won't trigger JWT validation
            $invalidJwt = 'header.payload.signature.extra';

            $responseData = [
                'access_token' => $invalidJwt,
                'expires_in' => 3600,
            ];

            $response = createMockResponse($responseData);

            $token = AccessToken::fromResponse($response);
            expect($token)->toBeInstanceOf(AccessToken::class);
        });

        test('fromResponse throws on JWT with invalid header', function (): void {
            $header = ['typ' => 'INVALID', 'alg' => 'HS256']; // Invalid typ
            $payload = ['exp' => time() + 3600];
            $jwtToken = createValidJwt($header, $payload);

            $responseData = [
                'access_token' => $jwtToken,
                'expires_in' => 3600,
            ];

            $response = createMockResponse($responseData);

            AccessToken::fromResponse($response);
        })->throws(SerializationException::class, trans(Messages::JWT_INVALID_HEADER));

        test('fromResponse throws on JWT missing header fields', function (): void {
            $header = ['typ' => 'JWT']; // Missing alg
            $payload = ['exp' => time() + 3600];
            $jwtToken = createValidJwt($header, $payload);

            $responseData = [
                'access_token' => $jwtToken,
                'expires_in' => 3600,
            ];

            $response = createMockResponse($responseData);

            AccessToken::fromResponse($response);
        })->throws(SerializationException::class, trans(Messages::JWT_INVALID_HEADER));

        test('fromResponse throws on expired JWT token', function (): void {
            $header = ['typ' => 'JWT', 'alg' => 'HS256'];
            $payload = [
                'exp' => time() - 3600, // Expired 1 hour ago
                'iat' => time() - 7200,
            ];
            $jwtToken = createValidJwt($header, $payload);

            $responseData = [
                'access_token' => $jwtToken,
                'expires_in' => 3600,
            ];

            $response = createMockResponse($responseData);

            AccessToken::fromResponse($response);
        })->throws(SerializationException::class, trans(Messages::JWT_TOKEN_EXPIRED));

        test('fromResponse throws on JWT token not yet valid', function (): void {
            $currentTime = time();
            $header = ['typ' => 'JWT', 'alg' => 'HS256'];
            $payload = [
                'nbf' => $currentTime + 3600, // Not valid for 1 hour
                'exp' => $currentTime + 7200,
                'iat' => $currentTime,
            ];
            $jwtToken = createValidJwt($header, $payload);

            $responseData = [
                'access_token' => $jwtToken,
                'expires_in' => 3600,
            ];

            $response = createMockResponse($responseData);

            AccessToken::fromResponse($response);
        })->throws(SerializationException::class, trans(Messages::JWT_TOKEN_NOT_YET_VALID));

        test('fromResponse throws on JWT with future issued at time beyond clock skew', function (): void {
            $currentTime = time();
            $header = ['typ' => 'JWT', 'alg' => 'HS256'];
            $payload = [
                'iat' => $currentTime + 400, // 400 seconds in future (beyond 300s skew)
                'exp' => $currentTime + 3600,
            ];
            $jwtToken = createValidJwt($header, $payload);

            $responseData = [
                'access_token' => $jwtToken,
                'expires_in' => 3600,
            ];

            $response = createMockResponse($responseData);

            AccessToken::fromResponse($response);
        })->throws(SerializationException::class, trans(Messages::JWT_INVALID_PAYLOAD));

        test('fromResponse allows JWT with future issued at time within clock skew', function (): void {
            $currentTime = time();
            $header = ['typ' => 'JWT', 'alg' => 'HS256'];
            $payload = [
                'iat' => $currentTime + 200, // 200 seconds in future (within 300s skew)
                'exp' => $currentTime + 3600,
            ];
            $jwtToken = createValidJwt($header, $payload);

            $responseData = [
                'access_token' => $jwtToken,
                'expires_in' => 3600,
            ];

            $response = createMockResponse($responseData);

            $token = AccessToken::fromResponse($response);

            expect($token)->toBeInstanceOf(AccessToken::class);
        });

        test('fromResponse throws on JWT with invalid issuer', function (): void {
            $currentTime = time();
            $header = ['typ' => 'JWT', 'alg' => 'HS256'];
            $payload = [
                'iss' => 'https://wrong-issuer.com',
                'exp' => $currentTime + 3600,
                'iat' => $currentTime,
            ];
            $jwtToken = createValidJwt($header, $payload);

            $responseData = [
                'access_token' => $jwtToken,
                'expires_in' => 3600,
            ];

            $response = createMockResponse($responseData);

            AccessToken::fromResponse(
                $response,
                'https://auth.example.com', // Expected issuer
                null,
            );
        })->throws(SerializationException::class, trans(Messages::JWT_INVALID_ISSUER));

        test('fromResponse throws on JWT missing issuer when expected', function (): void {
            $currentTime = time();
            $header = ['typ' => 'JWT', 'alg' => 'HS256'];
            $payload = [
                'exp' => $currentTime + 3600,
                'iat' => $currentTime,
                // Missing 'iss' claim
            ];
            $jwtToken = createValidJwt($header, $payload);

            $responseData = [
                'access_token' => $jwtToken,
                'expires_in' => 3600,
            ];

            $response = createMockResponse($responseData);

            AccessToken::fromResponse(
                $response,
                'https://auth.example.com', // Expected issuer
                null,
            );
        })->throws(SerializationException::class, trans(Messages::JWT_INVALID_ISSUER));

        test('fromResponse throws on JWT with invalid audience', function (): void {
            $currentTime = time();
            $header = ['typ' => 'JWT', 'alg' => 'HS256'];
            $payload = [
                'aud' => 'https://wrong-audience.com',
                'exp' => $currentTime + 3600,
                'iat' => $currentTime,
            ];
            $jwtToken = createValidJwt($header, $payload);

            $responseData = [
                'access_token' => $jwtToken,
                'expires_in' => 3600,
            ];

            $response = createMockResponse($responseData);

            AccessToken::fromResponse(
                $response,
                null,
                'https://api.example.com', // Expected audience
            );
        })->throws(SerializationException::class, trans(Messages::JWT_INVALID_AUDIENCE));

        test('fromResponse throws on JWT missing audience when expected', function (): void {
            $currentTime = time();
            $header = ['typ' => 'JWT', 'alg' => 'HS256'];
            $payload = [
                'exp' => $currentTime + 3600,
                'iat' => $currentTime,
                // Missing 'aud' claim
            ];
            $jwtToken = createValidJwt($header, $payload);

            $responseData = [
                'access_token' => $jwtToken,
                'expires_in' => 3600,
            ];

            $response = createMockResponse($responseData);

            AccessToken::fromResponse(
                $response,
                null,
                'https://api.example.com', // Expected audience
            );
        })->throws(SerializationException::class, trans(Messages::JWT_INVALID_AUDIENCE));

        test('fromResponse throws on JWT with invalid base64url encoding', function (): void {
            // Create a JWT with valid base64url characters but invalid content
            $invalidJwt = 'invalidheader123.invalidpayload456.invalidsignature789';

            $responseData = [
                'access_token' => $invalidJwt,
                'expires_in' => 3600,
            ];

            $response = createMockResponse($responseData);

            AccessToken::fromResponse($response);
        })->throws(SerializationException::class, trans(Messages::JWT_INVALID_HEADER));

        test('fromResponse throws on JWT with malformed JSON in header', function (): void {
            $invalidHeader = base64url_encode('{invalid json}');
            $validPayload = base64url_encode(json_encode(['exp' => time() + 3600]));
            $invalidJwt = $invalidHeader . '.' . $validPayload . '.signature';

            $responseData = [
                'access_token' => $invalidJwt,
                'expires_in' => 3600,
            ];

            $response = createMockResponse($responseData);

            AccessToken::fromResponse($response);
        })->throws(SerializationException::class, trans(Messages::JWT_INVALID_HEADER));

        test('fromResponse throws on JWT with malformed JSON in payload', function (): void {
            $validHeader = base64url_encode(json_encode(['typ' => 'JWT', 'alg' => 'HS256']));
            $invalidPayload = base64url_encode('{invalid json}');
            $invalidJwt = $validHeader . '.' . $invalidPayload . '.signature';

            $responseData = [
                'access_token' => $invalidJwt,
                'expires_in' => 3600,
            ];

            $response = createMockResponse($responseData);

            AccessToken::fromResponse($response);
        })->throws(SerializationException::class, trans(Messages::JWT_INVALID_PAYLOAD));

        test('fromResponse throws on JWT with non-numeric exp claim', function (): void {
            $header = ['typ' => 'JWT', 'alg' => 'HS256'];
            $payload = [
                'exp' => 'invalid', // Should be numeric
                'iat' => time(),
            ];
            $jwtToken = createValidJwt($header, $payload);

            $responseData = [
                'access_token' => $jwtToken,
                'expires_in' => 3600,
            ];

            $response = createMockResponse($responseData);

            AccessToken::fromResponse($response);
        })->throws(SerializationException::class, trans(Messages::JWT_INVALID_PAYLOAD));

        test('fromResponse throws on JWT with non-numeric nbf claim', function (): void {
            $header = ['typ' => 'JWT', 'alg' => 'HS256'];
            $payload = [
                'nbf' => 'invalid', // Should be numeric
                'exp' => time() + 3600,
                'iat' => time(),
            ];
            $jwtToken = createValidJwt($header, $payload);

            $responseData = [
                'access_token' => $jwtToken,
                'expires_in' => 3600,
            ];

            $response = createMockResponse($responseData);

            AccessToken::fromResponse($response);
        })->throws(SerializationException::class, trans(Messages::JWT_INVALID_PAYLOAD));

        test('fromResponse throws on JWT with non-numeric iat claim', function (): void {
            $header = ['typ' => 'JWT', 'alg' => 'HS256'];
            $payload = [
                'iat' => 'invalid', // Should be numeric
                'exp' => time() + 3600,
            ];
            $jwtToken = createValidJwt($header, $payload);

            $responseData = [
                'access_token' => $jwtToken,
                'expires_in' => 3600,
            ];

            $response = createMockResponse($responseData);

            AccessToken::fromResponse($response);
        })->throws(SerializationException::class, trans(Messages::JWT_INVALID_PAYLOAD));

        test('fromResponse throws on JWT with non-string issuer claim', function (): void {
            $header = ['typ' => 'JWT', 'alg' => 'HS256'];
            $payload = [
                'iss' => 123, // Should be string
                'exp' => time() + 3600,
                'iat' => time(),
            ];
            $jwtToken = createValidJwt($header, $payload);

            $responseData = [
                'access_token' => $jwtToken,
                'expires_in' => 3600,
            ];

            $response = createMockResponse($responseData);

            AccessToken::fromResponse(
                $response,
                'https://auth.example.com',
                null,
            );
        })->throws(SerializationException::class, trans(Messages::JWT_INVALID_PAYLOAD));

        test('fromResponse accepts JWT with float timestamp claims', function (): void {
            $currentTime = time();
            $header = ['typ' => 'JWT', 'alg' => 'HS256'];
            $payload = [
                'exp' => (float) ($currentTime + 3600),
                'nbf' => (float) ($currentTime - 60),
                'iat' => (float) $currentTime,
            ];
            $jwtToken = createValidJwt($header, $payload);

            $responseData = [
                'access_token' => $jwtToken,
                'expires_in' => 3600,
            ];

            $response = createMockResponse($responseData);

            $token = AccessToken::fromResponse($response);

            expect($token)->toBeInstanceOf(AccessToken::class);
        });
    });
});
