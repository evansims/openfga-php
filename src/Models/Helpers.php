<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use OpenFGA\ClientInterface;
use OpenFGA\Models\Collections\TupleKeys;
use Throwable;

/**
 * Helper for creating a TupleKey.
 *
 * @param string                  $user
 * @param string                  $relation
 * @param string                  $object
 * @param null|ConditionInterface $condition
 */
function tuple(string $user, string $relation, string $object, ?ConditionInterface $condition = null): TupleKey
{
    return new TupleKey($user, $relation, $object, $condition);
}

/**
 * Helper for creating a TupleKeys collection.
 *
 * @param TupleKey ...$tuples
 */
function tuples(TupleKey ...$tuples): TupleKeys
{
    return new TupleKeys($tuples);
}

/**
 * Create a store.
 *
 * @param ClientInterface $client An OpenFGA client.
 * @param string          $name   The name of the store.
 *
 * @throws Throwable If the store creation fails.
 *
 * @return string The store ID.
 */
function store(ClientInterface $client, string $name): string
{
    /** @var \OpenFGA\Responses\CreateStoreResponseInterface $response */
    $response = $client->createStore(name: $name)
        ->unwrap();

    return $response->getId();
}

/**
 * Create an authorization model.
 *
 * @param ClientInterface $client An OpenFGA client.
 * @param string          $dsl    The authorization model DSL.
 *
 * @throws Throwable If the authorization model creation fails.
 *
 * @return AuthorizationModelInterface The authorization model.
 */
function dsl(ClientInterface $client, string $dsl): AuthorizationModelInterface
{
    /** @var AuthorizationModelInterface */
    return $client->dsl($dsl)
        ->unwrap();
}
