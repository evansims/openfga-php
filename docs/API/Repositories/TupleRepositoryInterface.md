# TupleRepositoryInterface

Repository contract for relationship tuple operations. This interface defines the contract for managing relationship tuples within an OpenFGA store. Tuples represent relationships between users and objects (for example &quot;user:anne is reader of document:budget&quot;), forming the core data that drives authorization decisions. The repository supports both transactional and non-transactional operations for different scale and consistency requirements. All methods return Result objects following the Result pattern, allowing for consistent error handling without exceptions.

## Namespace

`OpenFGA\Repositories`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Repositories/TupleRepositoryInterface.php)

## Methods

### CRUD Operations

#### delete

```php
public function delete(
    StoreInterface $store,
    AuthorizationModelInterface $model,
    TupleKeysInterface $tuples,
    bool $transactional = true,
    array<string, mixed> $options = [],
): FailureInterface|SuccessInterface

```

Delete relationship tuples from the store. Removes existing relationship tuples from the store. Like write operations, supports both transactional and non-transactional modes with the same constraints and options.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Repositories/TupleRepositoryInterface.php#L43)

#### Parameters

| Name             | Type                                                                   | Description                                       |
| ---------------- | ---------------------------------------------------------------------- | ------------------------------------------------- |
| `$store`         | [`StoreInterface`](Models/StoreInterface.md)                           | The store containing the tuples                   |
| `$model`         | [`AuthorizationModelInterface`](Models/AuthorizationModelInterface.md) | The authorization model to validate against       |
| `$tuples`        | [`TupleKeysInterface`](Models/Collections/TupleKeysInterface.md)       | The tuples to delete                              |
| `$transactional` | `bool`                                                                 | Whether to use transactional mode (default: true) |
| `$options`       | `array&lt;`string`, `mixed`&gt;`                                       |                                                   |

#### Returns

[`FailureInterface`](Results/FailureInterface.md) &#124; [`SuccessInterface`](Results/SuccessInterface.md) — Success with WriteTuplesResponse containing operation results, or Failure with error details

#### read

```php
public function read(
    StoreInterface $store,
    TupleKeyInterface $filter,
    string|null $continuationToken = NULL,
    int|null $pageSize = NULL,
): FailureInterface|SuccessInterface

```

Read relationship tuples from the store. Retrieves tuples matching the specified filter criteria. The filter uses partial matching - you can specify any combination of user, relation, and object to narrow results. Results are paginated for efficient retrieval of large datasets.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Repositories/TupleRepositoryInterface.php#L90)

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
    StoreInterface $store,
    AuthorizationModelInterface $model,
    TupleKeysInterface $tuples,
    bool $transactional = true,
    array<string, mixed> $options = [],
): FailureInterface|SuccessInterface

```

Write relationship tuples to the store. Creates new relationship tuples in the store. Supports both transactional mode (all-or-nothing, limited to 100 tuples) and non-transactional mode for larger batches with configurable parallelism and retry behavior.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Repositories/TupleRepositoryInterface.php#L118)

#### Parameters

| Name             | Type                                                                   | Description                                       |
| ---------------- | ---------------------------------------------------------------------- | ------------------------------------------------- |
| `$store`         | [`StoreInterface`](Models/StoreInterface.md)                           | The store to write tuples to                      |
| `$model`         | [`AuthorizationModelInterface`](Models/AuthorizationModelInterface.md) | The authorization model to validate against       |
| `$tuples`        | [`TupleKeysInterface`](Models/Collections/TupleKeysInterface.md)       | The tuples to write                               |
| `$transactional` | `bool`                                                                 | Whether to use transactional mode (default: true) |
| `$options`       | `array&lt;`string`, `mixed`&gt;`                                       |                                                   |

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

Write and delete relationship tuples in a single operation. Combines write and delete operations for efficiency, especially useful when you need to atomically replace relationships. In transactional mode, all operations succeed or fail together. In non-transactional mode, operations are batched for optimal performance.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Repositories/TupleRepositoryInterface.php#L144)

#### Parameters

| Name             | Type                                                                           | Description                                       |
| ---------------- | ------------------------------------------------------------------------------ | ------------------------------------------------- |
| `$store`         | [`StoreInterface`](Models/StoreInterface.md)                                   | The store to operate on                           |
| `$model`         | [`AuthorizationModelInterface`](Models/AuthorizationModelInterface.md)         | The authorization model to validate against       |
| `$writes`        | [`TupleKeysInterface`](Models/Collections/TupleKeysInterface.md) &#124; `null` | Tuples to write (optional)                        |
| `$deletes`       | [`TupleKeysInterface`](Models/Collections/TupleKeysInterface.md) &#124; `null` | Tuples to delete (optional)                       |
| `$transactional` | `bool`                                                                         | Whether to use transactional mode (default: true) |
| `$options`       | `array&lt;`string`, `mixed`&gt;`                                               |                                                   |

#### Returns

[`FailureInterface`](Results/FailureInterface.md) &#124; [`SuccessInterface`](Results/SuccessInterface.md) — Success with WriteTuplesResponse containing operation results, or Failure with error details

### List Operations

#### listChanges

```php
public function listChanges(
    StoreInterface $store,
    string|null $type = NULL,
    DateTimeImmutable|null $startTime = NULL,
    string|null $continuationToken = NULL,
    int|null $pageSize = NULL,
): FailureInterface|SuccessInterface

```

List changes to relationship tuples over time. Retrieves a chronological log of tuple changes (writes and deletes) within the store. Useful for auditing, synchronization, or understanding how relationships evolved. Results can be filtered by object type and time range.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Repositories/TupleRepositoryInterface.php#L67)

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
