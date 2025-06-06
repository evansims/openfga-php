# TupleService

Service implementation for managing OpenFGA relationship tuples. Provides business-focused operations for working with relationship tuples, which represent the core relationships in your authorization model. This service abstracts the underlying repository implementation and adds value through validation, duplicate filtering, and enhanced error handling.

## Namespace

`OpenFGA\Services`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Services/TupleService.php)

## Implements

* [`TupleServiceInterface`](TupleServiceInterface.md)

## Related Classes

* [TupleServiceInterface](Services/TupleServiceInterface.md) (interface)

## Methods

### CRUD Operations

#### delete

```php
public function delete(
    OpenFGA\Models\StoreInterface|string $store,
    string $user,
    string $relation,
    string $object,
    bool $confirmExists = false,
): OpenFGA\Results\FailureInterface|OpenFGA\Results\SuccessInterface

```

Delete a single relationship tuple. Removes the specified relationship, with optional existence checking to provide better error messages when the tuple doesn&#039;t exist.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Services/TupleService.php#L56)

#### Parameters

| Name             | Type                                                         | Description                                                    |
| ---------------- | ------------------------------------------------------------ | -------------------------------------------------------------- |
| `$store`         | [`StoreInterface`](Models/StoreInterface.md) &#124; `string` | The store containing the tuple                                 |
| `$user`          | `string`                                                     | The user identifier                                            |
| `$relation`      | `string`                                                     | The relationship type                                          |
| `$object`        | `string`                                                     | The object identifier                                          |
| `$confirmExists` | `bool`                                                       | Whether to check tuple exists before deletion (default: false) |

#### Returns

[`FailureInterface`](Results/FailureInterface.md) &#124; [`SuccessInterface`](Results/SuccessInterface.md) — Success if deleted, or Failure with error details

#### deleteBatch

```php
public function deleteBatch(
    OpenFGA\Models\StoreInterface|string $store,
    OpenFGA\Models\Collections\TupleKeysInterface $tupleKeys,
    bool $transactional = true,
    bool $confirmExists = false,
): OpenFGA\Results\FailureInterface|OpenFGA\Results\SuccessInterface

```

Delete multiple relationship tuples in a batch operation. Efficiently removes multiple tuples, with automatic chunking and optional existence checking for better error reporting.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Services/TupleService.php#L92)

#### Parameters

| Name             | Type                                                             | Description                                                    |
| ---------------- | ---------------------------------------------------------------- | -------------------------------------------------------------- |
| `$store`         | [`StoreInterface`](Models/StoreInterface.md) &#124; `string`     | The store containing the tuples                                |
| `$tupleKeys`     | [`TupleKeysInterface`](Models/Collections/TupleKeysInterface.md) | The tuples to delete                                           |
| `$transactional` | `bool`                                                           | Whether to use transactional deletes (default: true)           |
| `$confirmExists` | `bool`                                                           | Whether to check tuples exist before deletion (default: false) |

#### Returns

[`FailureInterface`](Results/FailureInterface.md) &#124; [`SuccessInterface`](Results/SuccessInterface.md) — Success if all deleted, or Failure with error details

#### read

```php
public function read(
    OpenFGA\Models\StoreInterface|string $store,
    ?OpenFGA\Models\TupleKeyInterface $tupleKey = NULL,
    ?string $continuationToken = NULL,
    ?int $pageSize = NULL,
    ?OpenFGA\Models\Enums\Consistency $consistency = NULL,
): OpenFGA\Results\FailureInterface|OpenFGA\Results\SuccessInterface

```

Read relationship tuples with optional filtering. Retrieves tuples matching the specified criteria, with automatic pagination handling for large result sets.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Services/TupleService.php#L185)

#### Parameters

| Name                 | Type                                                             | Description                                         |
| -------------------- | ---------------------------------------------------------------- | --------------------------------------------------- |
| `$store`             | [`StoreInterface`](Models/StoreInterface.md) &#124; `string`     | The store to read from                              |
| `$tupleKey`          | [`TupleKeyInterface`](Models/TupleKeyInterface.md) &#124; `null` | The tuple key to filter by (optional)               |
| `$continuationToken` | `string` &#124; `null`                                           | Token for pagination (optional)                     |
| `$pageSize`          | `int` &#124; `null`                                              | Maximum number of tuples to retrieve (null for all) |
| `$consistency`       | [`Consistency`](Models/Enums/Consistency.md) &#124; `null`       | Read consistency level (optional)                   |

#### Returns

[`FailureInterface`](Results/FailureInterface.md) &#124; [`SuccessInterface`](Results/SuccessInterface.md) — Success with tuples collection, or Failure with error details

#### write

```php
public function write(
    OpenFGA\Models\StoreInterface|string $store,
    string $user,
    string $relation,
    string $object,
): OpenFGA\Results\FailureInterface|OpenFGA\Results\SuccessInterface

```

Write a single relationship tuple. Creates a relationship between a user and an object with the specified relation. This is the most common operation for establishing permissions.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Services/TupleService.php#L208)

#### Parameters

| Name        | Type                                                         | Description                                                          |
| ----------- | ------------------------------------------------------------ | -------------------------------------------------------------------- |
| `$store`    | [`StoreInterface`](Models/StoreInterface.md) &#124; `string` | The store where the tuple will be written                            |
| `$user`     | `string`                                                     | The user identifier (e.g., &#039;user:anne&#039;)                    |
| `$relation` | `string`                                                     | The relationship type (e.g., &#039;reader&#039;, &#039;writer&#039;) |
| `$object`   | `string`                                                     | The object identifier (e.g., &#039;document:budget-2024&#039;)       |

#### Returns

[`FailureInterface`](Results/FailureInterface.md) &#124; [`SuccessInterface`](Results/SuccessInterface.md) — Success if written, or Failure with error details

#### writeBatch

```php
public function writeBatch(
    OpenFGA\Models\StoreInterface|string $store,
    OpenFGA\Models\AuthorizationModelInterface|string $model,
    ?OpenFGA\Models\Collections\TupleKeysInterface $writes = NULL,
    ?OpenFGA\Models\Collections\TupleKeysInterface $deletes = NULL,
    bool $transactional = true,
    int $maxParallelRequests = 1,
    int $maxTuplesPerChunk = 100,
    int $maxRetries = 0,
    float $retryDelaySeconds = 1.0,
    bool $stopOnFirstError = false,
): OpenFGA\Results\FailureInterface|OpenFGA\Results\SuccessInterface

```

Write multiple relationship tuples in a batch operation. Efficiently writes multiple tuples, with automatic chunking to respect API limits and optional duplicate filtering for performance optimization.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Services/TupleService.php#L244)

#### Parameters

| Name                   | Type                                                                                   | Description                                         |
| ---------------------- | -------------------------------------------------------------------------------------- | --------------------------------------------------- |
| `$store`               | [`StoreInterface`](Models/StoreInterface.md) &#124; `string`                           | The store where tuples will be written              |
| `$model`               | [`AuthorizationModelInterface`](Models/AuthorizationModelInterface.md) &#124; `string` | The authorization model to use                      |
| `$writes`              | [`TupleKeysInterface`](Models/Collections/TupleKeysInterface.md) &#124; `null`         | The tuples to write (optional)                      |
| `$deletes`             | [`TupleKeysInterface`](Models/Collections/TupleKeysInterface.md) &#124; `null`         | The tuples to delete (optional)                     |
| `$transactional`       | `bool`                                                                                 | Whether to use transactional writes (default: true) |
| `$maxParallelRequests` | `int`                                                                                  | Maximum parallel requests (default: 1)              |
| `$maxTuplesPerChunk`   | `int`                                                                                  | Maximum tuples per chunk (default: 100)             |
| `$maxRetries`          | `int`                                                                                  | Maximum retries (default: 0)                        |
| `$retryDelaySeconds`   | `float`                                                                                | Retry delay in seconds (default: 1.0)               |
| `$stopOnFirstError`    | `bool`                                                                                 | Whether to stop on first error (default: false)     |

#### Returns

[`FailureInterface`](Results/FailureInterface.md) &#124; [`SuccessInterface`](Results/SuccessInterface.md) — Success if all written, or Failure with error details

### List Operations

#### getStatistics

```php
public function getStatistics(OpenFGA\Models\StoreInterface|string $store): OpenFGA\Results\SuccessInterface

```

Get statistics about tuples in the store. Provides insights into the tuple distribution and counts by type and relation, useful for monitoring and capacity planning.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Services/TupleService.php#L142)

#### Parameters

| Name     | Type                                                         | Description          |
| -------- | ------------------------------------------------------------ | -------------------- |
| `$store` | [`StoreInterface`](Models/StoreInterface.md) &#124; `string` | The store to analyze |

#### Returns

[`SuccessInterface`](Results/SuccessInterface.md) — Success with statistics array, or Failure with error details

#### listChanges

```php
public function listChanges(
    OpenFGA\Models\StoreInterface|string $store,
    ?string $type = NULL,
    ?DateTimeImmutable $startTime = NULL,
    ?string $continuationToken = NULL,
    ?int $pageSize = NULL,
): OpenFGA\Results\FailureInterface|OpenFGA\Results\SuccessInterface

```

List changes to tuples over time for auditing purposes. Retrieves a chronological log of tuple changes (writes and deletes) within the specified time range, useful for compliance and debugging.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Services/TupleService.php#L162)

#### Parameters

| Name                 | Type                                                         | Description                                          |
| -------------------- | ------------------------------------------------------------ | ---------------------------------------------------- |
| `$store`             | [`StoreInterface`](Models/StoreInterface.md) &#124; `string` | The store to list changes from                       |
| `$type`              | `string` &#124; `null`                                       | Filter by object type (optional)                     |
| `$startTime`         | `DateTimeImmutable` &#124; `null`                            | Start time for changes (optional)                    |
| `$continuationToken` | `string` &#124; `null`                                       | Token for pagination (optional)                      |
| `$pageSize`          | `int` &#124; `null`                                          | Maximum number of changes to retrieve (default: 100) |

#### Returns

[`FailureInterface`](Results/FailureInterface.md) &#124; [`SuccessInterface`](Results/SuccessInterface.md) — Success with changes collection, or Failure with error details

### Utility

#### exists

```php
public function exists(
    OpenFGA\Models\StoreInterface|string $store,
    string $user,
    string $relation,
    string $object,
): OpenFGA\Results\SuccessInterface

```

Check if a specific tuple exists in the store. Efficiently verifies tuple existence without retrieving all matching tuples. Useful for validation before operations or conditional logic.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Services/TupleService.php#L124)

#### Parameters

| Name        | Type                                                         | Description           |
| ----------- | ------------------------------------------------------------ | --------------------- |
| `$store`    | [`StoreInterface`](Models/StoreInterface.md) &#124; `string` | The store to check    |
| `$user`     | `string`                                                     | The user identifier   |
| `$relation` | `string`                                                     | The relationship type |
| `$object`   | `string`                                                     | The object identifier |

#### Returns

[`SuccessInterface`](Results/SuccessInterface.md) — Success with true/false, or Failure with error details
