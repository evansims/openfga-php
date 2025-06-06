# HttpModelRepository

HTTP implementation of the model repository. This repository handles authorization model operations via HTTP requests to the OpenFGA API. It converts domain objects to API requests, sends them via the HTTP service, and transforms responses back to domain objects. Supports creating, retrieving, and listing authorization models within a store. Authorization models define the permission structure for your application - the types of objects, the relationships between them, and the rules that govern access. Models are immutable once created; to update permissions, you create a new model version.

## Namespace

`OpenFGA\Repositories`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Repositories/HttpModelRepository.php)

## Implements

* [`ModelRepositoryInterface`](ModelRepositoryInterface.md)

## Methods

### CRUD Operations

#### create

```php
public function create(
    OpenFGA\Models\Collections\TypeDefinitionsInterface $typeDefinitions,
    OpenFGA\Models\Enums\SchemaVersion $schemaVersion = OpenFGA\Models\Enums\SchemaVersion::V1_1,
    ?OpenFGA\Models\Collections\ConditionsInterface $conditions = NULL,
): OpenFGA\Results\FailureInterface|OpenFGA\Results\SuccessInterface

```

Create a new authorization model in the store. Creates an immutable authorization model that defines your application&#039;s permission structure. The model includes type definitions for objects and the relationships between them, and optionally conditions for dynamic permissions.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Repositories/HttpModelRepository.php#L68)

#### Parameters

| Name               | Type                                                                             | Description                                        |
| ------------------ | -------------------------------------------------------------------------------- | -------------------------------------------------- |
| `$typeDefinitions` | [`TypeDefinitionsInterface`](Models/Collections/TypeDefinitionsInterface.md)     | Object types and their relationship definitions    |
| `$schemaVersion`   | [`SchemaVersion`](Models/Enums/SchemaVersion.md)                                 | The schema version for the model (defaults to 1.1) |
| `$conditions`      | [`ConditionsInterface`](Models/Collections/ConditionsInterface.md) &#124; `null` | Optional conditions for dynamic permissions        |

#### Returns

[`FailureInterface`](Results/FailureInterface.md) &#124; [`SuccessInterface`](Results/SuccessInterface.md) — Success with the created AuthorizationModelInterface, or Failure with error details

### List Operations

#### get

```php
public function get(string $modelId): OpenFGA\Results\FailureInterface|OpenFGA\Results\SuccessInterface

```

Get a specific authorization model by ID. Retrieves the complete authorization model including all type definitions, relationships, and conditions. Models are immutable, so the returned model will never change once created.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Repositories/HttpModelRepository.php#L106)

#### Parameters

| Name       | Type     | Description                                      |
| ---------- | -------- | ------------------------------------------------ |
| `$modelId` | `string` | The unique identifier of the authorization model |

#### Returns

[`FailureInterface`](Results/FailureInterface.md) &#124; [`SuccessInterface`](Results/SuccessInterface.md) — Success with the AuthorizationModelInterface, or Failure with error details

#### list

```php
public function list(
    ?int $pageSize = NULL,
    ?string $continuationToken = NULL,
): OpenFGA\Results\FailureInterface|OpenFGA\Results\SuccessInterface

```

List authorization models in the store. Returns a paginated list of authorization models, ordered by creation time (newest first). Use pagination parameters to retrieve large lists efficiently.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Repositories/HttpModelRepository.php#L139)

#### Parameters

| Name                 | Type                   | Description                                 |
| -------------------- | ---------------------- | ------------------------------------------- |
| `$pageSize`          | `int` &#124; `null`    | Maximum number of models to return (1-100)  |
| `$continuationToken` | `string` &#124; `null` | Token from previous response for pagination |

#### Returns

[`FailureInterface`](Results/FailureInterface.md) &#124; [`SuccessInterface`](Results/SuccessInterface.md) — Success with AuthorizationModelsInterface collection, or Failure with error details
