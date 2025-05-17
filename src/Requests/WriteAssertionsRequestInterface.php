<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use OpenFGA\Models\{Assertion, AssertionsInterface};

/**
 * @extends RequestInterface<array{assertions: array<array{tuple_key: array{user: string, relation: string, object: string}, expectation: bool}>, authorization_model_id: string, store_id: string}>
 */
interface WriteAssertionsRequestInterface extends RequestInterface
{
    /**
     * @return AssertionsInterface<Assertion>
     */
    public function getAssertions(): AssertionsInterface;

    public function getAuthorizationModel(): string;

    public function getStore(): string;
}
