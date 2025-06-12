<?php

declare(strict_types=1);

namespace OpenFGA\Services;

use OpenFGA\Models\{AuthorizationModelInterface, StoreInterface, TupleKeyInterface};
use OpenFGA\Models\Collections\{BatchCheckItemsInterface, TupleKeysInterface, UserTypeFiltersInterface};
use OpenFGA\Models\Enums\Consistency;
use OpenFGA\Results\{FailureInterface, SuccessInterface};

/**
 * Service interface for authorization operations.
 *
 * This interface defines methods for all authorization operations including
 * permission checks, relationship expansions, and object/user listing.
 * It provides a focused API for authorization decisions separate from
 * store and model management operations.
 *
 * @see https://openfga.dev/docs/api#/Relationship%20Queries Authorization query operations
 */
interface AuthorizationServiceInterface
{
    /**
     * Performs multiple authorization checks in a single batch request.
     *
     * This method allows checking multiple user-object relationships simultaneously
     * for better performance when multiple authorization decisions are needed.
     * Each check in the batch has a correlation ID to map results back to the
     * original requests.
     *
     * @param  StoreInterface|string              $store  The store to check against
     * @param  AuthorizationModelInterface|string $model  The authorization model to use
     * @param  BatchCheckItemsInterface           $checks The batch check items with correlation IDs
     * @return FailureInterface|SuccessInterface  Success with BatchCheckResponse, or Failure with error details
     *
     * @see https://openfga.dev/docs/api#/Relationship%20Queries/BatchCheck BatchCheck API reference
     */
    public function batchCheck(
        StoreInterface | string $store,
        AuthorizationModelInterface | string $model,
        BatchCheckItemsInterface $checks,
    ): FailureInterface | SuccessInterface;

    /**
     * Checks if a user has a specific relationship with an object.
     *
     * This method verifies whether the specified user has the given relationship
     * (like 'reader', 'writer', or 'owner') with the target object. It's the core
     * operation for making authorization decisions in your application.
     *
     * @param  StoreInterface|string              $store            The store to check against
     * @param  AuthorizationModelInterface|string $model            The authorization model to use
     * @param  TupleKeyInterface                  $tupleKey         The relationship to check
     * @param  bool|null                          $trace            Whether to include a trace in the response
     * @param  object|null                        $context          Additional context for the check
     * @param  TupleKeysInterface|null            $contextualTuples Additional tuples for contextual evaluation
     * @param  Consistency|null                   $consistency      Override the default consistency level
     * @return FailureInterface|SuccessInterface  Success with CheckResponse, or Failure with error details
     *
     * @see https://openfga.dev/docs/api#/Relationship%20Queries/Check Check API reference
     */
    public function check(
        StoreInterface | string $store,
        AuthorizationModelInterface | string $model,
        TupleKeyInterface $tupleKey,
        ?bool $trace = null,
        ?object $context = null,
        ?TupleKeysInterface $contextualTuples = null,
        ?Consistency $consistency = null,
    ): FailureInterface | SuccessInterface;

    /**
     * Expands a relationship tuple to show all users that have the relationship.
     *
     * This method recursively expands a relationship to reveal all users who have
     * access through direct assignment, group membership, or computed relationships.
     * It's useful for understanding why a user has a particular permission.
     *
     * @param  StoreInterface|string                   $store            The store containing the tuple
     * @param  TupleKeyInterface                       $tupleKey         The tuple to expand
     * @param  AuthorizationModelInterface|string|null $model            The authorization model to use
     * @param  TupleKeysInterface|null                 $contextualTuples Additional tuples for contextual evaluation
     * @param  Consistency|null                        $consistency      Override the default consistency level
     * @return FailureInterface|SuccessInterface       Success with ExpandResponse, or Failure with error details
     *
     * @see https://openfga.dev/docs/api#/Relationship%20Queries/Expand Expand API reference
     */
    public function expand(
        StoreInterface | string $store,
        TupleKeyInterface $tupleKey,
        AuthorizationModelInterface | string | null $model = null,
        ?TupleKeysInterface $contextualTuples = null,
        ?Consistency $consistency = null,
    ): FailureInterface | SuccessInterface;

    /**
     * Lists objects that have a specific relationship with a user.
     *
     * This method finds all objects of a given type that the specified user has
     * a particular relationship with. It's useful for building filtered lists
     * based on user permissions (for example "show all documents the user can read").
     *
     * @param  StoreInterface|string              $store            The store to query
     * @param  AuthorizationModelInterface|string $model            The authorization model to use
     * @param  string                             $type             The type of objects to list
     * @param  string                             $relation         The relationship to check
     * @param  string                             $user             The user to check relationships for
     * @param  object|null                        $context          Additional context for evaluation
     * @param  TupleKeysInterface|null            $contextualTuples Additional tuples for contextual evaluation
     * @param  Consistency|null                   $consistency      Override the default consistency level
     * @return FailureInterface|SuccessInterface  Success with ListObjectsResponse, or Failure with error details
     *
     * @see https://openfga.dev/docs/api#/Relationship%20Queries/ListObjects ListObjects API reference
     */
    public function listObjects(
        StoreInterface | string $store,
        AuthorizationModelInterface | string $model,
        string $type,
        string $relation,
        string $user,
        ?object $context = null,
        ?TupleKeysInterface $contextualTuples = null,
        ?Consistency $consistency = null,
    ): FailureInterface | SuccessInterface;

    /**
     * Lists users that have a specific relationship with an object.
     *
     * This method finds all users (and optionally groups) that have a particular
     * relationship with a specific object. It's useful for auditing access or
     * building user interfaces that show who has permissions.
     *
     * @param  StoreInterface|string              $store            The store to query
     * @param  AuthorizationModelInterface|string $model            The authorization model to use
     * @param  string                             $object           The object to check relationships for
     * @param  string                             $relation         The relationship to check
     * @param  UserTypeFiltersInterface           $userFilters      Filters for user types to include
     * @param  object|null                        $context          Additional context for evaluation
     * @param  TupleKeysInterface|null            $contextualTuples Additional tuples for contextual evaluation
     * @param  Consistency|null                   $consistency      Override the default consistency level
     * @return FailureInterface|SuccessInterface  Success with ListUsersResponse, or Failure with error details
     *
     * @see https://openfga.dev/docs/api#/Relationship%20Queries/ListUsers ListUsers API reference
     */
    public function listUsers(
        StoreInterface | string $store,
        AuthorizationModelInterface | string $model,
        string $object,
        string $relation,
        UserTypeFiltersInterface $userFilters,
        ?object $context = null,
        ?TupleKeysInterface $contextualTuples = null,
        ?Consistency $consistency = null,
    ): FailureInterface | SuccessInterface;

    /**
     * Lists objects that a user has a specific relationship with using streaming.
     *
     * This method finds all objects of a given type where the specified user has
     * the requested relationship, returning results as a stream for efficient processing
     * of large datasets. The streaming approach is memory-efficient for large result sets.
     *
     * @param  StoreInterface|string              $store            The store to query
     * @param  AuthorizationModelInterface|string $model            The authorization model to use
     * @param  string                             $type             The object type to filter by
     * @param  string                             $relation         The relationship to check
     * @param  string                             $user             The user to check relationships for
     * @param  object|null                        $context          Additional context for evaluation
     * @param  TupleKeysInterface|null            $contextualTuples Additional tuples for contextual evaluation
     * @param  Consistency|null                   $consistency      Override the default consistency level
     * @return FailureInterface|SuccessInterface  Success with Generator<StreamedListObjectsResponse>, or Failure with error details
     *
     * @see https://openfga.dev/docs/api#/Relationship%20Queries/StreamedListObjects StreamedListObjects API reference
     */
    public function streamedListObjects(
        StoreInterface | string $store,
        AuthorizationModelInterface | string $model,
        string $type,
        string $relation,
        string $user,
        ?object $context = null,
        ?TupleKeysInterface $contextualTuples = null,
        ?Consistency $consistency = null,
    ): FailureInterface | SuccessInterface;
}
