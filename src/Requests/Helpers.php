<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use OpenFGA\ClientInterface;
use OpenFGA\Models\{AuthorizationModelInterface, TupleKeyInterface};
use OpenFGA\Models\Collections\{TupleKeys, TupleKeysInterface};
use OpenFGA\Models\Enums\Consistency;
use Throwable;

/**
 * Write relationship tuples.
 *
 * @param ClientInterface                                         $client An OpenFGA client.
 * @param string                                                  $store  The store ID.
 * @param AuthorizationModelInterface                             $model  The authorization model.
 * @param TupleKeyInterface|TupleKeysInterface<TupleKeyInterface> $tuples The tuples to write.
 *
 * @throws Throwable If the write fails.
 */
function write(ClientInterface $client, string $store, AuthorizationModelInterface $model, TupleKeyInterface | TupleKeysInterface $tuples): void
{
    if ($tuples instanceof TupleKeyInterface) {
        $tuples = new TupleKeys([$tuples]);
    }

    $client->writeTuples(store: $store, model: $model, writes: $tuples);
}

/**
 * Delete relationship tuples.
 *
 * @param ClientInterface                                         $client An OpenFGA client.
 * @param string                                                  $store  The store ID.
 * @param string                                                  $model  The authorization model ID.
 * @param TupleKeyInterface|TupleKeysInterface<TupleKeyInterface> $tuples The tuples to delete.
 *
 * @throws Throwable If the delete fails.
 */
function delete(ClientInterface $client, string $store, string $model, TupleKeyInterface | TupleKeysInterface $tuples): void
{
    if ($tuples instanceof TupleKeyInterface) {
        $tuples = new TupleKeys([$tuples]);
    }

    $client->writeTuples(store: $store, model: $model, deletes: $tuples);
}

/**
 * Check for a relationship.
 *
 * @param ClientInterface                        $client           An OpenFGA client.
 * @param string                                 $store            The store ID.
 * @param string                                 $model            The authorization model ID.
 * @param TupleKeyInterface                      $tuple            The tuple to check.
 * @param ?bool                                  $trace            Whether to trace the check.
 * @param ?object                                $context          The context to use for the check.
 * @param ?TupleKeysInterface<TupleKeyInterface> $contextualTuples The contextual tuples to use for the check.
 * @param ?Consistency                           $consistency      The consistency to use for the check.
 *
 * @throws Throwable If the check fails.
 *
 * @return bool True if the tuple is allowed, false otherwise.
 */
function allowed(ClientInterface $client, string $store, string $model, TupleKeyInterface $tuple, ?bool $trace = null, ?object $context = null, ?TupleKeysInterface $contextualTuples = null, ?Consistency $consistency = null): bool
{
    /** @var \OpenFGA\Responses\CheckResponseInterface $response */
    $response = $client->check(store: $store, model: $model, tupleKey: $tuple, trace: $trace, context: $context, contextualTuples: $contextualTuples, consistency: $consistency)
        ->unwrap();

    return $response->getAllowed() ?? false;
}

/**
 * Create an authorization model.
 *
 * @param ClientInterface             $client An OpenFGA client.
 * @param string                      $store  The store ID.
 * @param AuthorizationModelInterface $model  The authorization model.
 *
 * @throws Throwable If the model creation fails.
 *
 * @return string The authorization model ID.
 */
function model(ClientInterface $client, string $store, AuthorizationModelInterface $model): string
{
    /** @var \OpenFGA\Responses\CreateAuthorizationModelResponseInterface $response */
    $response = $client->createAuthorizationModel(
        store: $store,
        typeDefinitions: $model->getTypeDefinitions(),
        conditions: $model->getConditions(),
        schemaVersion: $model->getSchemaVersion(),
    )
        ->unwrap();

    return $response->getModel();
}
