<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use OpenFGA\Models\{Assertion, AssertionsInterface};
use OpenFGA\Schema\SchemaInterface;

/**
 * @extends ResponseInterface<array{authorization_model_id: string, assertions: array<array{tuple_key: array{user: string, relation: string, object: string}, expectation: bool}>}>
 */
interface ReadAssertionsResponseInterface extends ResponseInterface
{
    /**
     * @return null|AssertionsInterface<Assertion>
     */
    public function getAssertions(): ?AssertionsInterface;

    public function getAuthorizationModelId(): string;

    public static function schema(): SchemaInterface;
}
