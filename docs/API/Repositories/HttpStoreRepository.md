# HttpStoreRepository

HTTP implementation of the store repository. This repository provides a domain-focused abstraction for store operations, handling all HTTP communication through the injected HttpService. It converts domain objects to API requests, sends them via HTTP, and transforms responses back to domain objects while maintaining proper error handling. The repository encapsulates all HTTP-specific concerns including request/response transformation, pagination handling, and API error mapping. It follows the SDK&#039;s Result pattern to provide safe error handling without exceptions for control flow. ## Implementation Details - Uses HttpService for all HTTP operations - Validates responses using SchemaValidator - Transforms API responses to domain objects - Handles pagination for list operations - Provides consistent error handling via Result pattern

## Namespace

`OpenFGA\Repositories`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Repositories/HttpStoreRepository.php)

## Implements

* [`StoreRepositoryInterface`](StoreRepositoryInterface.md)

## Methods

### CRUD Operations

#### create

```php
public function create(string $name): OpenFGA\Results\ResultInterface

```

Create a new store with the specified name. Creates a new OpenFGA store which serves as a container for authorization models and relationship tuples. Each store is isolated from others, allowing you to manage multiple authorization configurations in a single OpenFGA instance.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Repositories/HttpStoreRepository.php#L66)

#### Parameters

| Name    | Type     | Description                |
| ------- | -------- | -------------------------- |
| `$name` | `string` | The name for the new store |

#### Returns

[`ResultInterface`](Results/ResultInterface.md) — Success containing the created Store, or Failure with error details

#### delete

```php
public function delete(string $storeId): OpenFGA\Results\ResultInterface

```

Delete an existing store by ID. Permanently removes a store and all its associated data including authorization models and relationship tuples. This operation cannot be undone, so use with caution in production environments.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Repositories/HttpStoreRepository.php#L109)

#### Parameters

| Name       | Type     | Description                   |
| ---------- | -------- | ----------------------------- |
| `$storeId` | `string` | The ID of the store to delete |

#### Returns

[`ResultInterface`](Results/ResultInterface.md) — Success with null value, or Failure with error details

### List Operations

#### get

```php
public function get(string $storeId): OpenFGA\Results\ResultInterface

```

Get a store by ID. Retrieves the details of an existing store including its name and timestamps. Use this to verify a store exists or to get its current metadata.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Repositories/HttpStoreRepository.php#L139)

#### Parameters

| Name       | Type     | Description                     |
| ---------- | -------- | ------------------------------- |
| `$storeId` | `string` | The ID of the store to retrieve |

#### Returns

[`ResultInterface`](Results/ResultInterface.md) — Success containing the Store, or Failure with error details

#### list

```php
public function list(?string $continuationToken = NULL, ?int $pageSize = NULL): OpenFGA\Results\ResultInterface

```

List available stores with optional pagination. Retrieves a paginated list of all stores accessible to the authenticated client. Use the continuation token from a previous response to fetch subsequent pages when dealing with large numbers of stores.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Repositories/HttpStoreRepository.php#L169)

#### Parameters

| Name                 | Type                   | Description                                   |
| -------------------- | ---------------------- | --------------------------------------------- |
| `$continuationToken` | `string` &#124; `null` | Token from previous response to get next page |
| `$pageSize`          | `int` &#124; `null`    | Maximum number of stores to return (1-100)    |

#### Returns

[`ResultInterface`](Results/ResultInterface.md) — Success containing Stores collection, or Failure with error details
