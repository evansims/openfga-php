# HttpAssertionRepository

HTTP implementation of assertion repository for OpenFGA API communication. This repository handles assertion operations by communicating with the OpenFGA HTTP API. It transforms business operations into HTTP requests and responses, handling serialization, deserialization, and error management.

## Table of Contents

- [Namespace](#namespace)
- [Source](#source)
- [Implements](#implements)
- [Methods](#methods)

- [CRUD Operations](#crud-operations)
  - [`read()`](#read)
  - [`write()`](#write)

## Namespace

`OpenFGA\Repositories`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Repositories/HttpAssertionRepository.php)

## Implements

- [`AssertionRepositoryInterface`](AssertionRepositoryInterface.md)

## Methods

#### read

```php
public function read(
    string $authorizationModelId,
): OpenFGA\Results\Failure|OpenFGA\Results\Success|OpenFGA\Results\SuccessInterface

```

Read assertions from an authorization model. Retrieves all test assertions defined for the specified authorization model. Assertions validate that the model behaves correctly for specific scenarios.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Repositories/HttpAssertionRepository.php#L48)

#### Parameters

| Name                    | Type     | Description                                      |
| ----------------------- | -------- | ------------------------------------------------ |
| `$authorizationModelId` | `string` | The authorization model ID containing assertions |

#### Returns

[`Failure`](Results/Failure.md) &#124; [`Success`](Results/Success.md) &#124; [`SuccessInterface`](Results/SuccessInterface.md) — Success with assertions collection, or Failure with error details

#### write

```php
public function write(
    string $authorizationModelId,
    OpenFGA\Models\Collections\AssertionsInterface $assertions,
): OpenFGA\Results\Failure|OpenFGA\Results\Success|OpenFGA\Results\SuccessInterface

```

Write assertions to an authorization model. Updates the test assertions for the specified authorization model. This replaces any existing assertions with the provided collection.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Repositories/HttpAssertionRepository.php#L80)

#### Parameters

| Name                    | Type                                                               | Description                          |
| ----------------------- | ------------------------------------------------------------------ | ------------------------------------ |
| `$authorizationModelId` | `string`                                                           | The authorization model ID to update |
| `$assertions`           | [`AssertionsInterface`](Models/Collections/AssertionsInterface.md) | The assertions to write              |

#### Returns

[`Failure`](Results/Failure.md) &#124; [`Success`](Results/Success.md) &#124; [`SuccessInterface`](Results/SuccessInterface.md) — Success if written, or Failure with error details
