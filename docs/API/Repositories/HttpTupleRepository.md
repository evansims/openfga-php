# HttpTupleRepository

HTTP implementation of the tuple repository. This repository handles tuple operations via HTTP requests to the OpenFGA API. It converts domain objects to API requests, sends them via the HTTP service, and transforms responses back to domain objects. Supports both transactional and non-transactional tuple operations with proper error handling.

## Namespace

`OpenFGA\Repositories`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Repositories/HttpTupleRepository.php)

## Implements

* [`TupleRepositoryInterface`](TupleRepositoryInterface.md)

## Methods

### CRUD Operations

#### delete

```php
public function delete(
    OpenFGA\Models\StoreInterface $store,
    OpenFGA\Models\AuthorizationModelInterface $model,
    OpenFGA\Models\Collections\TupleKeysInterface $tuples,
    bool $transactional = true,
    array $options = [],
): OpenFGA\Results\FailureInterface|OpenFGA\Results\SuccessInterface

```

Delete relationship tuples from the store. Removes existing relationship tuples from the store. Like write operations, supports both transactional and non-transactional modes with the same constraints and options.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Repositories/HttpTupleRepository.php#L62)

#### Parameters

| Name             | Type                                                                   | Description                                       |
| ---------------- | ---------------------------------------------------------------------- | ------------------------------------------------- |
| `$store`         | [`StoreInterface`](Models/StoreInterface.md)                           | The store containing the tuples                   |
| `$model`         | [`AuthorizationModelInterface`](Models/AuthorizationModelInterface.md) | The authorization model to validate against       |
| `$tuples`        | [`TupleKeysInterface`](Models/Collections/TupleKeysInterface.md)       | The tuples to delete                              |
| `$transactional` | `bool`                                                                 | Whether to use transactional mode (default: true) |
| `$options`       | `array`                                                                |                                                   |

#### Returns

[`FailureInterface`](Results/FailureInterface.md) &#124; [`SuccessInterface`](Results/SuccessInterface.md) — Success with WriteTuplesResponse containing operation results, or Failure with error details

#### read

```php
public function read(
    OpenFGA\Models\StoreInterface $store,
    OpenFGA\Models\TupleKeyInterface $filter,
    ?string $continuationToken = NULL,
    ?int $pageSize = NULL,
): OpenFGA\Results\FailureInterface|OpenFGA\Results\SuccessInterface

```

Read relationship tuples from the store. Retrieves tuples matching the specified filter criteria. The filter uses partial matching - you can specify any combination of user, relation, and object to narrow results. Results are paginated for efficient retrieval of large datasets.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Repositories/HttpTupleRepository.php#L156)

#### Parameters

| Name                 | Type                                               | Description                                 |
| -------------------- | -------------------------------------------------- | ------------------------------------------- |
| `$store`             | [`StoreInterface`](Models/StoreInterface.md)       | The store containing the tuples             |
| `$filter`            | [`TupleKeyInterface`](Models/TupleKeyInterface.md) | Filter criteria for tuple matching          |
| `$continuationToken` | `string` &#124; `null`                             | Token from previous response for pagination |
| `$pageSize`          | `int` &#124; `null`                                | Maximum number of tuples to return (1-100)  |

#### Returns

[`FailureInterface`](Results/FailureInterface.md) &#124; [`SuccessInterface`](Results/SuccessInterface.md) — Success with ReadTuplesResponse containing matching tuples, or Failure with error details

#### write

```php
public function write(
    OpenFGA\Models\StoreInterface $store,
    OpenFGA\Models\AuthorizationModelInterface $model,
    OpenFGA\Models\Collections\TupleKeysInterface $tuples,
    bool $transactional = true,
    array $options = [],
): OpenFGA\Results\FailureInterface|OpenFGA\Results\SuccessInterface

```

Write relationship tuples to the store. Creates new relationship tuples in the store. Supports both transactional mode (all-or-nothing, limited to 100 tuples) and non-transactional mode for larger batches with configurable parallelism and retry behavior.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Repositories/HttpTupleRepository.php#L196)

#### Parameters

| Name             | Type                                                                   | Description                                       |
| ---------------- | ---------------------------------------------------------------------- | ------------------------------------------------- |
| `$store`         | [`StoreInterface`](Models/StoreInterface.md)                           | The store to write tuples to                      |
| `$model`         | [`AuthorizationModelInterface`](Models/AuthorizationModelInterface.md) | The authorization model to validate against       |
| `$tuples`        | [`TupleKeysInterface`](Models/Collections/TupleKeysInterface.md)       | The tuples to write                               |
| `$transactional` | `bool`                                                                 | Whether to use transactional mode (default: true) |
| `$options`       | `array`                                                                |                                                   |

#### Returns

[`FailureInterface`](Results/FailureInterface.md) &#124; [`SuccessInterface`](Results/SuccessInterface.md) — Success with WriteTuplesResponse containing operation results, or Failure with error details

#### writeAndDelete

```php
public function writeAndDelete(
    StoreInterface $store,
    AuthorizationModelInterface $model,
    TupleKeysInterface|null $writes = NULL,
    TupleKeysInterface|null $deletes = NULL,
    bool $transactional = true,
    array<string, mixed> $options = [],
): FailureInterface|SuccessInterface

```

Write and delete tuples in a single operation.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Repositories/HttpTupleRepository.php#L257)

#### Parameters

| Name             | Type                                                                           | Description                                 |
| ---------------- | ------------------------------------------------------------------------------ | ------------------------------------------- |
| `$store`         | [`StoreInterface`](Models/StoreInterface.md)                                   | The store to operate on                     |
| `$model`         | [`AuthorizationModelInterface`](Models/AuthorizationModelInterface.md)         | The authorization model to validate against |
| `$writes`        | [`TupleKeysInterface`](Models/Collections/TupleKeysInterface.md) &#124; `null` | Tuples to write (optional)                  |
| `$deletes`       | [`TupleKeysInterface`](Models/Collections/TupleKeysInterface.md) &#124; `null` | Tuples to delete (optional)                 |
| `$transactional` | `bool`                                                                         | Whether to use transactional mode           |
| `$options`       | `array&lt;`string`, `mixed`&gt;`                                               |                                             |

#### Returns

[`FailureInterface`](Results/FailureInterface.md) &#124; [`SuccessInterface`](Results/SuccessInterface.md) — Result of the operation

### List Operations

#### listChanges

```php
public function listChanges(
    OpenFGA\Models\StoreInterface $store,
    ?string $type = NULL,
    ?DateTimeImmutable $startTime = NULL,
    ?string $continuationToken = NULL,
    ?int $pageSize = NULL,
): OpenFGA\Results\FailureInterface|OpenFGA\Results\SuccessInterface

```

List changes to relationship tuples over time. Retrieves a chronological log of tuple changes (writes and deletes) within the store. Useful for auditing, synchronization, or understanding how relationships evolved. Results can be filtered by object type and time range.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Repositories/HttpTupleRepository.php#L115)

#### Parameters

| Name                 | Type                                         | Description                                              |
| -------------------- | -------------------------------------------- | -------------------------------------------------------- |
| `$store`             | [`StoreInterface`](Models/StoreInterface.md) | The store to query                                       |
| `$type`              | `string` &#124; `null`                       | Filter by object type (for example &quot;document&quot;) |
| `$startTime`         | `DateTimeImmutable` &#124; `null`            | Filter changes after this time                           |
| `$continuationToken` | `string` &#124; `null`                       | Token from previous response for pagination              |
| `$pageSize`          | `int` &#124; `null`                          | Maximum number of changes to return                      |

#### Returns

[`FailureInterface`](Results/FailureInterface.md) &#124; [`SuccessInterface`](Results/SuccessInterface.md) — Success with ListTupleChangesResponse containing change history, or Failure with error details
