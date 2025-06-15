# ModelService

Service implementation for managing OpenFGA authorization models. Provides business-focused operations for working with authorization models, including validation, convenience methods, and enhanced error handling. This service abstracts the underlying repository implementation and adds value through additional functionality.

<details>
<summary><strong>Table of Contents</strong></summary>

- [Namespace](#namespace)
- [Source](#source)
- [Implements](#implements)
- [Related Classes](#related-classes)
- [Methods](#methods)

- [`cloneModel()`](#clonemodel)
  - [`createModel()`](#createmodel)
  - [`findModel()`](#findmodel)
  - [`getLatestModel()`](#getlatestmodel)
  - [`listAllModels()`](#listallmodels)
  - [`validateModel()`](#validatemodel)

</details>

## Namespace

`OpenFGA\Services`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Services/ModelService.php)

## Implements

- [`ModelServiceInterface`](ModelServiceInterface.md)

## Related Classes

- [ModelServiceInterface](Services/ModelServiceInterface.md) (interface)

## Methods

### cloneModel

```php
public function cloneModel(string $modelId): OpenFGA\Results\FailureInterface|OpenFGA\Results\SuccessInterface

```

Clone an authorization model to another store. Copies a model from one store to another, useful for multi-tenant scenarios where you want to replicate a permission structure. The cloned model gets a new ID in the target store.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Services/ModelService.php#L52)

#### Parameters

| Name       | Type     | Description                  |
| ---------- | -------- | ---------------------------- |
| `$modelId` | `string` | The ID of the model to clone |

#### Returns

[`FailureInterface`](Results/FailureInterface.md) &#124; [`SuccessInterface`](Results/SuccessInterface.md) — Success with the cloned model, or Failure with error details

### createModel

```php
public function createModel(
    OpenFGA\Models\Collections\TypeDefinitionsInterface $typeDefinitions,
    ?OpenFGA\Models\Collections\ConditionsInterface $conditions = NULL,
    OpenFGA\Models\Enums\SchemaVersion $schemaVersion = OpenFGA\Models\Enums\SchemaVersion::V1_1,
): OpenFGA\Results\FailureInterface|OpenFGA\Results\SuccessInterface

```

Create a new authorization model with validation. Creates an immutable authorization model from the provided type definitions and optional conditions. The model is validated before creation to ensure it conforms to OpenFGA&#039;s schema requirements.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Services/ModelService.php#L78)

#### Parameters

| Name               | Type                                                                             | Description                                            |
| ------------------ | -------------------------------------------------------------------------------- | ------------------------------------------------------ |
| `$typeDefinitions` | [`TypeDefinitionsInterface`](Models/Collections/TypeDefinitionsInterface.md)     | The type definitions for the model                     |
| `$conditions`      | [`ConditionsInterface`](Models/Collections/ConditionsInterface.md) &#124; `null` | Optional conditions for attribute-based access control |
| `$schemaVersion`   | [`SchemaVersion`](Models/Enums/SchemaVersion.md)                                 | The OpenFGA schema version to use                      |

#### Returns

[`FailureInterface`](Results/FailureInterface.md) &#124; [`SuccessInterface`](Results/SuccessInterface.md) — Success with the created model, or Failure with validation/creation errors

### findModel

```php
public function findModel(string $modelId): OpenFGA\Results\FailureInterface|OpenFGA\Results\SuccessInterface

```

Find a specific authorization model by ID. Retrieves a model with enhanced error handling, providing clear messages when models are not found or other errors occur.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Services/ModelService.php#L98)

#### Parameters

| Name       | Type     | Description                        |
| ---------- | -------- | ---------------------------------- |
| `$modelId` | `string` | The unique identifier of the model |

#### Returns

[`FailureInterface`](Results/FailureInterface.md) &#124; [`SuccessInterface`](Results/SuccessInterface.md) — Success with the model, or Failure with detailed error information

### getLatestModel

```php
public function getLatestModel(OpenFGA\Models\StoreInterface|string $store): FailureInterface|Success

```

Get the most recent authorization model for a store. Retrieves the latest model version, which is typically the active model being used for authorization decisions. This is a convenience method that avoids needing to list all models and manually find the newest one.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Services/ModelService.php#L116)

#### Parameters

| Name     | Type                                                         | Description                            |
| -------- | ------------------------------------------------------------ | -------------------------------------- |
| `$store` | [`StoreInterface`](Models/StoreInterface.md) &#124; `string` | The store to get the latest model from |

#### Returns

[`FailureInterface`](Results/FailureInterface.md) &#124; [`Success`](Results/Success.md) — Success with the latest model, or Failure if no models exist

### listAllModels

```php
public function listAllModels(
    ?string $continuationToken = NULL,
    ?int $pageSize = NULL,
): OpenFGA\Results\FailureInterface|OpenFGA\Results\SuccessInterface

```

List all authorization models for a store. Retrieves all models with automatic pagination handling. This method aggregates results across multiple pages up to the specified limit.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Services/ModelService.php#L147)

#### Parameters

| Name                 | Type                   | Description                                                    |
| -------------------- | ---------------------- | -------------------------------------------------------------- |
| `$continuationToken` | `string` &#124; `null` | Pagination token from a previous response                      |
| `$pageSize`          | `int` &#124; `null`    | Maximum number of models to retrieve (null for server default) |

#### Returns

[`FailureInterface`](Results/FailureInterface.md) &#124; [`SuccessInterface`](Results/SuccessInterface.md) — Success with the models collection, or Failure with error details

### validateModel

```php
public function validateModel(
    OpenFGA\Models\Collections\TypeDefinitionsInterface $typeDefinitions,
    OpenFGA\Models\Enums\SchemaVersion $schemaVersion = OpenFGA\Models\Enums\SchemaVersion::V1_1,
): OpenFGA\Results\FailureInterface|OpenFGA\Results\SuccessInterface

```

Validate type definitions before creating a model. Performs validation on type definitions to catch errors before attempting to create a model. This is useful for providing immediate feedback in user interfaces or validation pipelines.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Services/ModelService.php#L160)

#### Parameters

| Name               | Type                                                                         | Description                            |
| ------------------ | ---------------------------------------------------------------------------- | -------------------------------------- |
| `$typeDefinitions` | [`TypeDefinitionsInterface`](Models/Collections/TypeDefinitionsInterface.md) | The type definitions to validate       |
| `$schemaVersion`   | [`SchemaVersion`](Models/Enums/SchemaVersion.md)                             | The schema version to validate against |

#### Returns

[`FailureInterface`](Results/FailureInterface.md) &#124; [`SuccessInterface`](Results/SuccessInterface.md) — Success if valid, or Failure with validation errors
