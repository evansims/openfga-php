# StoreServiceInterface

Service interface for high-level store operations. This interface provides a business-focused abstraction over store management, offering convenience methods and enhanced validation beyond basic CRUD operations. It simplifies common store workflows while maintaining the Result pattern for consistent error handling across the SDK.

## Namespace

`OpenFGA\Services`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Services/StoreServiceInterface.php)

## Related Classes

* [StoreService](Services/StoreService.md) (implementation)

## Methods

### CRUD Operations

#### createStore

```php
public function createStore(string $name): FailureInterface|SuccessInterface

```

Creates a new store with validation. This method creates a new OpenFGA store after validating the provided name. It ensures the name meets requirements before attempting creation, providing clearer error messages than the raw API when validation fails.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Services/StoreServiceInterface.php#L33)

#### Parameters

| Name    | Type     | Description                                    |
| ------- | -------- | ---------------------------------------------- |
| `$name` | `string` | The name for the new store (must not be empty) |

#### Returns

[`FailureInterface`](Results/FailureInterface.md) &#124; [`SuccessInterface`](Results/SuccessInterface.md) — Success containing the created Store, or Failure with error details

#### deleteStore

```php
public function deleteStore(string $storeId, bool $confirmExists = true): FailureInterface|SuccessInterface

```

Deletes a store with optional confirmation. This method deletes a store after optionally verifying it exists first. When confirmation is enabled, it provides clearer error messages if the store doesn&#039;t exist, preventing confusion about failed delete operations.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Services/StoreServiceInterface.php#L48)

#### Parameters

| Name             | Type     | Description                                        |
| ---------------- | -------- | -------------------------------------------------- |
| `$storeId`       | `string` | The ID of the store to delete                      |
| `$confirmExists` | `bool`   | Whether to verify the store exists before deletion |

#### Returns

[`FailureInterface`](Results/FailureInterface.md) &#124; [`SuccessInterface`](Results/SuccessInterface.md) — Success with null value, or Failure with error details

#### getOrCreateStore

```php
public function getOrCreateStore(string $name): FailureInterface|SuccessInterface

```

Gets an existing store or creates a new one with the given name. This convenience method first attempts to find a store by name among existing stores. If no store with the given name exists, it creates a new one. This is useful for idempotent store setup in development or testing scenarios. Note: This method lists all stores to find matches by name, which may be inefficient with large numbers of stores.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Services/StoreServiceInterface.php#L90)

#### Parameters

| Name    | Type     | Description                             |
| ------- | -------- | --------------------------------------- |
| `$name` | `string` | The name of the store to find or create |

#### Returns

[`FailureInterface`](Results/FailureInterface.md) &#124; [`SuccessInterface`](Results/SuccessInterface.md) — Success containing the Store (existing or new), or Failure with error details

### List Operations

#### findStore

```php
public function findStore(string $storeId): FailureInterface|SuccessInterface

```

Finds a store by ID with enhanced error handling. This method retrieves a store by its ID, providing more descriptive error messages when the store is not found or when other errors occur. It helps distinguish between &quot;not found&quot; and other types of failures.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Services/StoreServiceInterface.php#L62)

#### Parameters

| Name       | Type     | Description                 |
| ---------- | -------- | --------------------------- |
| `$storeId` | `string` | The ID of the store to find |

#### Returns

[`FailureInterface`](Results/FailureInterface.md) &#124; [`SuccessInterface`](Results/SuccessInterface.md) — Success containing the Store, or Failure with detailed error context

#### findStoresByName

```php
public function findStoresByName(string $pattern, int|null $maxItems = NULL): FailureInterface|SuccessInterface

```

Finds stores by name pattern. This method searches for stores whose names match a given pattern, supporting basic wildcard matching. It&#039;s useful for finding stores in multi-tenant scenarios or when working with naming conventions.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Services/StoreServiceInterface.php#L75)

#### Parameters

| Name        | Type                | Description                                        |
| ----------- | ------------------- | -------------------------------------------------- |
| `$pattern`  | `string`            | The name pattern to match (supports * as wildcard) |
| `$maxItems` | `int` &#124; `null` | Maximum number of matching stores to return        |

#### Returns

[`FailureInterface`](Results/FailureInterface.md) &#124; [`SuccessInterface`](Results/SuccessInterface.md) — Success containing Stores collection of matches, or Failure with error details

#### listAllStores

```php
public function listAllStores(int|null $maxItems = NULL): FailureInterface|SuccessInterface

```

Lists all stores with simplified pagination. This method retrieves all accessible stores, automatically handling pagination to return a complete collection. It abstracts away the complexity of dealing with continuation tokens for most use cases.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Services/StoreServiceInterface.php#L104)

#### Parameters

| Name        | Type                | Description                                         |
| ----------- | ------------------- | --------------------------------------------------- |
| `$maxItems` | `int` &#124; `null` | Maximum number of stores to retrieve (null for all) |

#### Returns

[`FailureInterface`](Results/FailureInterface.md) &#124; [`SuccessInterface`](Results/SuccessInterface.md) — Success containing Stores collection, or Failure with error details

#### listStores

```php
public function listStores(
    string|null $continuationToken = NULL,
    int|null $pageSize = NULL,
): FailureInterface|SuccessInterface

```

Lists stores with pagination support. This method retrieves stores with explicit pagination control, allowing you to specify continuation tokens for iterating through large result sets.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Services/StoreServiceInterface.php#L118)

#### Parameters

| Name                 | Type                   | Description                                   |
| -------------------- | ---------------------- | --------------------------------------------- |
| `$continuationToken` | `string` &#124; `null` | Token from previous response to get next page |
| `$pageSize`          | `int` &#124; `null`    | Maximum number of stores to return per page   |

#### Returns

[`FailureInterface`](Results/FailureInterface.md) &#124; [`SuccessInterface`](Results/SuccessInterface.md) — Success containing Stores collection, or Failure with error details
