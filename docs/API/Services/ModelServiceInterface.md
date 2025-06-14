# ModelServiceInterface

Service interface for managing OpenFGA authorization models. This service provides business-focused operations for working with authorization models, abstracting away the underlying repository implementation details and providing enhanced functionality like validation, cloning, and convenience methods. Authorization models define the permission structure for your application, including object types, relationships, and computation rules. Models are immutable once created, ensuring consistent authorization behavior. ## Core Operations The service supports model management with enhanced functionality: - Create models with comprehensive validation - Retrieve models with improved error handling - Clone models between stores for multi-tenant scenarios - Find the latest model version automatically ## Usage Example ```php $modelService = new ModelService($modelRepository); Create a new model with validation $result = $modelService-&gt;createModel( $store, $typeDefinitions, $conditions ); Get the latest model for a store $latest = $modelService-&gt;getLatestModel($store)-&gt;unwrap(); Clone a model to another store $cloned = $modelService-&gt;cloneModel( $sourceStore, $modelId, $targetStore )-&gt;unwrap(); ```

## Table of Contents

- [Namespace](#namespace)
- [Source](#source)
- [Related Classes](#related-classes)
- [Methods](#methods)

- [Authorization](#authorization)
  - [`validateModel()`](#validatemodel)
- [CRUD Operations](#crud-operations)
  - [`createModel()`](#createmodel)
- [List Operations](#list-operations)
  - [`findModel()`](#findmodel)
  - [`getLatestModel()`](#getlatestmodel)
  - [`listAllModels()`](#listallmodels)
- [Model Management](#model-management)
  - [`cloneModel()`](#clonemodel)

## Namespace

`OpenFGA\Services`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Services/ModelServiceInterface.php)

## Related Classes

- [ModelService](Services/ModelService.md) (implementation)

## Methods

### Authorization

#### validateModel

```php
public function validateModel(
    TypeDefinitionsInterface $typeDefinitions,
    SchemaVersion $schemaVersion = OpenFGA\Models\Enums\SchemaVersion::V1_1,
): FailureInterface|SuccessInterface

```

Validate type definitions before creating a model. Performs validation on type definitions to catch errors before attempting to create a model. This is useful for providing immediate feedback in user interfaces or validation pipelines.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Services/ModelServiceInterface.php#L148)

#### Parameters

| Name               | Type                                                                         | Description                            |
| ------------------ | ---------------------------------------------------------------------------- | -------------------------------------- |
| `$typeDefinitions` | [`TypeDefinitionsInterface`](Models/Collections/TypeDefinitionsInterface.md) | The type definitions to validate       |
| `$schemaVersion`   | [`SchemaVersion`](Models/Enums/SchemaVersion.md)                             | The schema version to validate against |

#### Returns

[`FailureInterface`](Results/FailureInterface.md) &#124; [`SuccessInterface`](Results/SuccessInterface.md) — Success if valid, or Failure with validation errors

### CRUD Operations

#### createModel

```php
public function createModel(
    TypeDefinitionsInterface $typeDefinitions,
    ConditionsInterface|null $conditions = NULL,
    SchemaVersion $schemaVersion = OpenFGA\Models\Enums\SchemaVersion::V1_1,
): FailureInterface|SuccessInterface

```

Create a new authorization model with validation. Creates an immutable authorization model from the provided type definitions and optional conditions. The model is validated before creation to ensure it conforms to OpenFGA&#039;s schema requirements.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Services/ModelServiceInterface.php#L89)

#### Parameters

| Name               | Type                                                                             | Description                                            |
| ------------------ | -------------------------------------------------------------------------------- | ------------------------------------------------------ |
| `$typeDefinitions` | [`TypeDefinitionsInterface`](Models/Collections/TypeDefinitionsInterface.md)     | The type definitions for the model                     |
| `$conditions`      | [`ConditionsInterface`](Models/Collections/ConditionsInterface.md) &#124; `null` | Optional conditions for attribute-based access control |
| `$schemaVersion`   | [`SchemaVersion`](Models/Enums/SchemaVersion.md)                                 | The OpenFGA schema version to use                      |

#### Returns

[`FailureInterface`](Results/FailureInterface.md) &#124; [`SuccessInterface`](Results/SuccessInterface.md) — Success with the created model, or Failure with validation/creation errors

### List Operations

#### findModel

```php
public function findModel(string $modelId): FailureInterface|SuccessInterface

```

Find a specific authorization model by ID. Retrieves a model with enhanced error handling, providing clear messages when models are not found or other errors occur.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Services/ModelServiceInterface.php#L104)

#### Parameters

| Name       | Type     | Description                        |
| ---------- | -------- | ---------------------------------- |
| `$modelId` | `string` | The unique identifier of the model |

#### Returns

[`FailureInterface`](Results/FailureInterface.md) &#124; [`SuccessInterface`](Results/SuccessInterface.md) — Success with the model, or Failure with detailed error information

#### getLatestModel

```php
public function getLatestModel(StoreInterface|string $store): FailureInterface|SuccessInterface

```

Get the most recent authorization model for a store. Retrieves the latest model version, which is typically the active model being used for authorization decisions. This is a convenience method that avoids needing to list all models and manually find the newest one.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Services/ModelServiceInterface.php#L118)

#### Parameters

| Name     | Type                                                         | Description                            |
| -------- | ------------------------------------------------------------ | -------------------------------------- |
| `$store` | [`StoreInterface`](Models/StoreInterface.md) &#124; `string` | The store to get the latest model from |

#### Returns

[`FailureInterface`](Results/FailureInterface.md) &#124; [`SuccessInterface`](Results/SuccessInterface.md) — Success with the latest model, or Failure if no models exist

#### listAllModels

```php
public function listAllModels(
    string|null $continuationToken = NULL,
    int|null $pageSize = NULL,
): FailureInterface|SuccessInterface

```

List all authorization models for a store. Retrieves all models with automatic pagination handling. This method aggregates results across multiple pages up to the specified limit.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Services/ModelServiceInterface.php#L132)

#### Parameters

| Name                 | Type                   | Description                                                    |
| -------------------- | ---------------------- | -------------------------------------------------------------- |
| `$continuationToken` | `string` &#124; `null` | Pagination token from a previous response                      |
| `$pageSize`          | `int` &#124; `null`    | Maximum number of models to retrieve (null for server default) |

#### Returns

[`FailureInterface`](Results/FailureInterface.md) &#124; [`SuccessInterface`](Results/SuccessInterface.md) — Success with the models collection, or Failure with error details

### Model Management

#### cloneModel

```php
public function cloneModel(string $modelId): FailureInterface|SuccessInterface

```

Clone an authorization model to another store. Copies a model from one store to another, useful for multi-tenant scenarios where you want to replicate a permission structure. The cloned model gets a new ID in the target store.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Services/ModelServiceInterface.php#L73)

#### Parameters

| Name       | Type     | Description                  |
| ---------- | -------- | ---------------------------- |
| `$modelId` | `string` | The ID of the model to clone |

#### Returns

[`FailureInterface`](Results/FailureInterface.md) &#124; [`SuccessInterface`](Results/SuccessInterface.md) — Success with the cloned model, or Failure with error details
