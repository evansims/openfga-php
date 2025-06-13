# StoreRepositoryInterface

Repository interface for store operations. This interface defines the contract for store repository implementations, providing a domain-focused abstraction for store management operations. All methods follow the Result pattern, returning either Success or Failure objects to enable safe error handling without exceptions for control flow. Implementations should handle all infrastructure concerns such as HTTP communication, data persistence, or caching while presenting a clean domain interface to the application layer.

## Table of Contents

* [Namespace](#namespace)
* [Source](#source)
* [Methods](#methods)

* [CRUD Operations](#crud-operations)
    * [`create()`](#create)
    * [`delete()`](#delete)
* [List Operations](#list-operations)
    * [`get()`](#get)
    * [`list()`](#list)

## Namespace

`OpenFGA\Repositories`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Repositories/StoreRepositoryInterface.php)

## Methods

### CRUD Operations

#### create

```php
public function create(string $name): ResultInterface

```

Create a new store with the specified name. Creates a new OpenFGA store which serves as a container for authorization models and relationship tuples. Each store is isolated from others, allowing you to manage multiple authorization configurations in a single OpenFGA instance.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Repositories/StoreRepositoryInterface.php#L35)

#### Parameters

| Name    | Type     | Description                |
| ------- | -------- | -------------------------- |
| `$name` | `string` | The name for the new store |

#### Returns

[`ResultInterface`](Results/ResultInterface.md) — Success containing the created Store, or Failure with error details

#### delete

```php
public function delete(string $storeId): ResultInterface

```

Delete an existing store by ID. Permanently removes a store and all its associated data including authorization models and relationship tuples. This operation cannot be undone, so use with caution in production environments.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Repositories/StoreRepositoryInterface.php#L47)

#### Parameters

| Name       | Type     | Description                   |
| ---------- | -------- | ----------------------------- |
| `$storeId` | `string` | The ID of the store to delete |

#### Returns

[`ResultInterface`](Results/ResultInterface.md) — Success with null value, or Failure with error details

### List Operations

#### get

```php
public function get(string $storeId): ResultInterface

```

Get a store by ID. Retrieves the details of an existing store including its name and timestamps. Use this to verify a store exists or to get its current metadata.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Repositories/StoreRepositoryInterface.php#L58)

#### Parameters

| Name       | Type     | Description                     |
| ---------- | -------- | ------------------------------- |
| `$storeId` | `string` | The ID of the store to retrieve |

#### Returns

[`ResultInterface`](Results/ResultInterface.md) — Success containing the Store, or Failure with error details

#### list

```php
public function list(string|null $continuationToken = NULL, int|null $pageSize = NULL): ResultInterface

```

List available stores with optional pagination. Retrieves a paginated list of all stores accessible to the authenticated client. Use the continuation token from a previous response to fetch subsequent pages when dealing with large numbers of stores.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Repositories/StoreRepositoryInterface.php#L71)

#### Parameters

| Name                 | Type                   | Description                                   |
| -------------------- | ---------------------- | --------------------------------------------- |
| `$continuationToken` | `string` &#124; `null` | Token from previous response to get next page |
| `$pageSize`          | `int` &#124; `null`    | Maximum number of stores to return (1-100)    |

#### Returns

[`ResultInterface`](Results/ResultInterface.md) — Success containing Stores collection, or Failure with error details
