# AssertionRepositoryInterface

Repository interface for managing OpenFGA authorization model assertions. This interface provides data access operations for working with assertions, which are test cases that validate the behavior of authorization models. Implementations handle the underlying storage and retrieval mechanisms.

## Namespace

`OpenFGA\Repositories`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Repositories/AssertionRepositoryInterface.php)

## Methods

#### read

```php
public function read(string $authorizationModelId): FailureInterface|SuccessInterface

```

Read assertions from an authorization model. Retrieves all test assertions defined for the specified authorization model. Assertions validate that the model behaves correctly for specific scenarios.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Repositories/AssertionRepositoryInterface.php#L30)

#### Parameters

| Name                    | Type     | Description                                      |
| ----------------------- | -------- | ------------------------------------------------ |
| `$authorizationModelId` | `string` | The authorization model ID containing assertions |

#### Returns

[`FailureInterface`](Results/FailureInterface.md) &#124; [`SuccessInterface`](Results/SuccessInterface.md) — Success with assertions collection, or Failure with error details

#### write

```php
public function write(string $authorizationModelId, AssertionsInterface $assertions): FailureInterface|SuccessInterface

```

Write assertions to an authorization model. Updates the test assertions for the specified authorization model. This replaces any existing assertions with the provided collection.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Repositories/AssertionRepositoryInterface.php#L42)

#### Parameters

| Name                    | Type                                                               | Description                          |
| ----------------------- | ------------------------------------------------------------------ | ------------------------------------ |
| `$authorizationModelId` | `string`                                                           | The authorization model ID to update |
| `$assertions`           | [`AssertionsInterface`](Models/Collections/AssertionsInterface.md) | The assertions to write              |

#### Returns

[`FailureInterface`](Results/FailureInterface.md) &#124; [`SuccessInterface`](Results/SuccessInterface.md) — Success if written, or Failure with error details
