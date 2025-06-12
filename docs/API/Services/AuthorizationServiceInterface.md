# AuthorizationServiceInterface

Service interface for authorization operations. This interface defines methods for all authorization operations including permission checks, relationship expansions, and object/user listing. It provides a focused API for authorization decisions separate from store and model management operations.

## Namespace

`OpenFGA\Services`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Services/AuthorizationServiceInterface.php)

## Related Classes

* [AuthorizationService](Services/AuthorizationService.md) (implementation)

## Methods

### Authorization

#### batchCheck

```php
public function batchCheck(
    StoreInterface|string $store,
    AuthorizationModelInterface|string $model,
    BatchCheckItemsInterface $checks,
): FailureInterface|SuccessInterface

```

Performs multiple authorization checks in a single batch request. This method allows checking multiple user-object relationships simultaneously for better performance when multiple authorization decisions are needed. Each check in the batch has a correlation ID to map results back to the original requests.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Services/AuthorizationServiceInterface.php#L39)

#### Parameters

| Name      | Type                                                                                   | Description                                |
| --------- | -------------------------------------------------------------------------------------- | ------------------------------------------ |
| `$store`  | [`StoreInterface`](Models/StoreInterface.md) &#124; `string`                           | The store to check against                 |
| `$model`  | [`AuthorizationModelInterface`](Models/AuthorizationModelInterface.md) &#124; `string` | The authorization model to use             |
| `$checks` | [`BatchCheckItemsInterface`](Models/Collections/BatchCheckItemsInterface.md)           | The batch check items with correlation IDs |

#### Returns

[`FailureInterface`](Results/FailureInterface.md) &#124; [`SuccessInterface`](Results/SuccessInterface.md) — Success with BatchCheckResponse, or Failure with error details

#### check

```php
public function check(
    StoreInterface|string $store,
    AuthorizationModelInterface|string $model,
    TupleKeyInterface $tupleKey,
    bool|null $trace = NULL,
    object|null $context = NULL,
    TupleKeysInterface|null $contextualTuples = NULL,
    Consistency|null $consistency = NULL,
): FailureInterface|SuccessInterface

```

Checks if a user has a specific relationship with an object. This method verifies whether the specified user has the given relationship (like &#039;reader&#039;, &#039;writer&#039;, or &#039;owner&#039;) with the target object. It&#039;s the core operation for making authorization decisions in your application.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Services/AuthorizationServiceInterface.php#L63)

#### Parameters

| Name                | Type                                                                                   | Description                                 |
| ------------------- | -------------------------------------------------------------------------------------- | ------------------------------------------- |
| `$store`            | [`StoreInterface`](Models/StoreInterface.md) &#124; `string`                           | The store to check against                  |
| `$model`            | [`AuthorizationModelInterface`](Models/AuthorizationModelInterface.md) &#124; `string` | The authorization model to use              |
| `$tupleKey`         | [`TupleKeyInterface`](Models/TupleKeyInterface.md)                                     | The relationship to check                   |
| `$trace`            | `bool` &#124; `null`                                                                   | Whether to include a trace in the response  |
| `$context`          | `object` &#124; `null`                                                                 | Additional context for the check            |
| `$contextualTuples` | [`TupleKeysInterface`](Models/Collections/TupleKeysInterface.md) &#124; `null`         | Additional tuples for contextual evaluation |
| `$consistency`      | [`Consistency`](Models/Enums/Consistency.md) &#124; `null`                             | Override the default consistency level      |

#### Returns

[`FailureInterface`](Results/FailureInterface.md) &#124; [`SuccessInterface`](Results/SuccessInterface.md) — Success with CheckResponse, or Failure with error details

#### expand

```php
public function expand(
    StoreInterface|string $store,
    TupleKeyInterface $tupleKey,
    AuthorizationModelInterface|string|null $model = NULL,
    TupleKeysInterface|null $contextualTuples = NULL,
    Consistency|null $consistency = NULL,
): FailureInterface|SuccessInterface

```

Expands a relationship tuple to show all users that have the relationship. This method recursively expands a relationship to reveal all users who have access through direct assignment, group membership, or computed relationships. It&#039;s useful for understanding why a user has a particular permission.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Services/AuthorizationServiceInterface.php#L89)

#### Parameters

| Name                | Type                                                                                                 | Description                                 |
| ------------------- | ---------------------------------------------------------------------------------------------------- | ------------------------------------------- |
| `$store`            | [`StoreInterface`](Models/StoreInterface.md) &#124; `string`                                         | The store containing the tuple              |
| `$tupleKey`         | [`TupleKeyInterface`](Models/TupleKeyInterface.md)                                                   | The tuple to expand                         |
| `$model`            | [`AuthorizationModelInterface`](Models/AuthorizationModelInterface.md) &#124; `string` &#124; `null` | The authorization model to use              |
| `$contextualTuples` | [`TupleKeysInterface`](Models/Collections/TupleKeysInterface.md) &#124; `null`                       | Additional tuples for contextual evaluation |
| `$consistency`      | [`Consistency`](Models/Enums/Consistency.md) &#124; `null`                                           | Override the default consistency level      |

#### Returns

[`FailureInterface`](Results/FailureInterface.md) &#124; [`SuccessInterface`](Results/SuccessInterface.md) — Success with ExpandResponse, or Failure with error details

### List Operations

#### listObjects

```php
public function listObjects(
    StoreInterface|string $store,
    AuthorizationModelInterface|string $model,
    string $type,
    string $relation,
    string $user,
    object|null $context = NULL,
    TupleKeysInterface|null $contextualTuples = NULL,
    Consistency|null $consistency = NULL,
): FailureInterface|SuccessInterface

```

Lists objects that have a specific relationship with a user. This method finds all objects of a given type that the specified user has a particular relationship with. It&#039;s useful for building filtered lists based on user permissions (for example &quot;show me all documents I can read&quot;).

[View source](https://github.com/evansims/openfga-php/blob/main/src/Services/AuthorizationServiceInterface.php#L116)

#### Parameters

| Name                | Type                                                                                   | Description                                 |
| ------------------- | -------------------------------------------------------------------------------------- | ------------------------------------------- |
| `$store`            | [`StoreInterface`](Models/StoreInterface.md) &#124; `string`                           | The store to query                          |
| `$model`            | [`AuthorizationModelInterface`](Models/AuthorizationModelInterface.md) &#124; `string` | The authorization model to use              |
| `$type`             | `string`                                                                               | The type of objects to list                 |
| `$relation`         | `string`                                                                               | The relationship to check                   |
| `$user`             | `string`                                                                               | The user to check relationships for         |
| `$context`          | `object` &#124; `null`                                                                 | Additional context for evaluation           |
| `$contextualTuples` | [`TupleKeysInterface`](Models/Collections/TupleKeysInterface.md) &#124; `null`         | Additional tuples for contextual evaluation |
| `$consistency`      | [`Consistency`](Models/Enums/Consistency.md) &#124; `null`                             | Override the default consistency level      |

#### Returns

[`FailureInterface`](Results/FailureInterface.md) &#124; [`SuccessInterface`](Results/SuccessInterface.md) — Success with ListObjectsResponse, or Failure with error details

#### listUsers

```php
public function listUsers(
    StoreInterface|string $store,
    AuthorizationModelInterface|string $model,
    string $object,
    string $relation,
    UserTypeFiltersInterface $userFilters,
    object|null $context = NULL,
    TupleKeysInterface|null $contextualTuples = NULL,
    Consistency|null $consistency = NULL,
): FailureInterface|SuccessInterface

```

Lists users that have a specific relationship with an object. This method finds all users (and optionally groups) that have a particular relationship with a specific object. It&#039;s useful for auditing access or building user interfaces that show who has permissions.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Services/AuthorizationServiceInterface.php#L146)

#### Parameters

| Name                | Type                                                                                   | Description                                 |
| ------------------- | -------------------------------------------------------------------------------------- | ------------------------------------------- |
| `$store`            | [`StoreInterface`](Models/StoreInterface.md) &#124; `string`                           | The store to query                          |
| `$model`            | [`AuthorizationModelInterface`](Models/AuthorizationModelInterface.md) &#124; `string` | The authorization model to use              |
| `$object`           | `string`                                                                               | The object to check relationships for       |
| `$relation`         | `string`                                                                               | The relationship to check                   |
| `$userFilters`      | [`UserTypeFiltersInterface`](Models/Collections/UserTypeFiltersInterface.md)           | Filters for user types to include           |
| `$context`          | `object` &#124; `null`                                                                 | Additional context for evaluation           |
| `$contextualTuples` | [`TupleKeysInterface`](Models/Collections/TupleKeysInterface.md) &#124; `null`         | Additional tuples for contextual evaluation |
| `$consistency`      | [`Consistency`](Models/Enums/Consistency.md) &#124; `null`                             | Override the default consistency level      |

#### Returns

[`FailureInterface`](Results/FailureInterface.md) &#124; [`SuccessInterface`](Results/SuccessInterface.md) — Success with ListUsersResponse, or Failure with error details

#### streamedListObjects

```php
public function streamedListObjects(
    StoreInterface|string $store,
    AuthorizationModelInterface|string $model,
    string $type,
    string $relation,
    string $user,
    object|null $context = NULL,
    TupleKeysInterface|null $contextualTuples = NULL,
    Consistency|null $consistency = NULL,
): FailureInterface|SuccessInterface

```

Lists objects that a user has a specific relationship with using streaming. This method finds all objects of a given type where the specified user has the requested relationship, returning results as a stream for efficient processing of large datasets. The streaming approach is memory-efficient for large result sets.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Services/AuthorizationServiceInterface.php#L176)

#### Parameters

| Name                | Type                                                                                   | Description                                 |
| ------------------- | -------------------------------------------------------------------------------------- | ------------------------------------------- |
| `$store`            | [`StoreInterface`](Models/StoreInterface.md) &#124; `string`                           | The store to query                          |
| `$model`            | [`AuthorizationModelInterface`](Models/AuthorizationModelInterface.md) &#124; `string` | The authorization model to use              |
| `$type`             | `string`                                                                               | The object type to filter by                |
| `$relation`         | `string`                                                                               | The relationship to check                   |
| `$user`             | `string`                                                                               | The user to check relationships for         |
| `$context`          | `object` &#124; `null`                                                                 | Additional context for evaluation           |
| `$contextualTuples` | [`TupleKeysInterface`](Models/Collections/TupleKeysInterface.md) &#124; `null`         | Additional tuples for contextual evaluation |
| `$consistency`      | [`Consistency`](Models/Enums/Consistency.md) &#124; `null`                             | Override the default consistency level      |

#### Returns

[`FailureInterface`](Results/FailureInterface.md) &#124; [`SuccessInterface`](Results/SuccessInterface.md) — Success with Generator&lt;StreamedListObjectsResponse&gt;, or Failure with error details
