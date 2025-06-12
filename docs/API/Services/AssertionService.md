# AssertionService

Service implementation for managing OpenFGA authorization model assertions. Provides business-focused operations for working with assertions, which are test cases that validate the behavior of authorization models. This service abstracts the underlying repository implementation and adds value through validation, convenience methods, and enhanced error handling.

## Namespace

`OpenFGA\Services`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Services/AssertionService.php)

## Implements

* [`AssertionServiceInterface`](AssertionServiceInterface.md)

## Related Classes

* [AssertionServiceInterface](Services/AssertionServiceInterface.md) (interface)

## Methods

### Authorization

#### validateAssertions

```php
public function validateAssertions(
    OpenFGA\Models\Collections\AssertionsInterface $assertions,
    string $authorizationModelId,
): OpenFGA\Results\Failure|OpenFGA\Results\Success|OpenFGA\Results\SuccessInterface

```

Validate assertion syntax and logic. Checks that assertions are properly formatted and reference valid types and relations from the authorization model. This helps catch errors before deploying assertions to production.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Services/AssertionService.php#L167)

#### Parameters

| Name                    | Type                                                               | Description                                 |
| ----------------------- | ------------------------------------------------------------------ | ------------------------------------------- |
| `$assertions`           | [`AssertionsInterface`](Models/Collections/AssertionsInterface.md) | The assertions to validate                  |
| `$authorizationModelId` | `string`                                                           | The authorization model to validate against |

#### Returns

[`Failure`](Results/Failure.md) &#124; [`Success`](Results/Success.md) &#124; [`SuccessInterface`](Results/SuccessInterface.md) — Success if valid, or Failure with validation errors

### CRUD Operations

#### readAssertions

```php
public function readAssertions(
    string $authorizationModelId,
): OpenFGA\Results\FailureInterface|OpenFGA\Results\SuccessInterface

```

Read assertions from an authorization model. Retrieves all test assertions defined in the specified authorization model. Assertions validate that the model behaves correctly for specific scenarios.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Services/AssertionService.php#L152)

#### Parameters

| Name                    | Type     | Description                                      |
| ----------------------- | -------- | ------------------------------------------------ |
| `$authorizationModelId` | `string` | The authorization model ID containing assertions |

#### Returns

[`FailureInterface`](Results/FailureInterface.md) &#124; [`SuccessInterface`](Results/SuccessInterface.md) — Success with assertions collection, or Failure with error details

#### writeAssertions

```php
public function writeAssertions(
    string $authorizationModelId,
    OpenFGA\Models\Collections\AssertionsInterface $assertions,
): OpenFGA\Results\FailureInterface|OpenFGA\Results\SuccessInterface

```

Write assertions to an authorization model. Updates the test assertions for the specified authorization model. Assertions help validate that your authorization model works as expected by defining specific test cases and their expected outcomes.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Services/AssertionService.php#L202)

#### Parameters

| Name                    | Type                                                               | Description                          |
| ----------------------- | ------------------------------------------------------------------ | ------------------------------------ |
| `$authorizationModelId` | `string`                                                           | The authorization model ID to update |
| `$assertions`           | [`AssertionsInterface`](Models/Collections/AssertionsInterface.md) | The assertions to write              |

#### Returns

[`FailureInterface`](Results/FailureInterface.md) &#124; [`SuccessInterface`](Results/SuccessInterface.md) — Success if written, or Failure with error details

### List Operations

#### getAssertionStatistics

```php
public function getAssertionStatistics(
    OpenFGA\Models\StoreInterface|string $store,
    string $authorizationModelId,
): OpenFGA\Results\FailureInterface|OpenFGA\Results\SuccessInterface

```

Get assertion execution statistics. Provides insights into assertion test results, including pass/fail counts, execution times, and common failure patterns. Useful for monitoring authorization model health and test coverage.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Services/AssertionService.php#L114)

#### Parameters

| Name                    | Type                                                         | Description                        |
| ----------------------- | ------------------------------------------------------------ | ---------------------------------- |
| `$store`                | [`StoreInterface`](Models/StoreInterface.md) &#124; `string` | The store to analyze               |
| `$authorizationModelId` | `string`                                                     | The authorization model to analyze |

#### Returns

[`FailureInterface`](Results/FailureInterface.md) &#124; [`SuccessInterface`](Results/SuccessInterface.md) — Success with statistics, or Failure with error details

### Utility

#### clearAssertions

```php
public function clearAssertions(
    string $authorizationModelId,
): OpenFGA\Results\FailureInterface|OpenFGA\Results\SuccessInterface

```

Clear all assertions from an authorization model. Removes all test assertions from the specified authorization model. This is useful when completely restructuring test cases or during development iterations.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Services/AssertionService.php#L49)

#### Parameters

| Name                    | Type     | Description                      |
| ----------------------- | -------- | -------------------------------- |
| `$authorizationModelId` | `string` | The authorization model to clear |

#### Returns

[`FailureInterface`](Results/FailureInterface.md) &#124; [`SuccessInterface`](Results/SuccessInterface.md) — Success if cleared, or Failure with error details

#### executeAssertions

```php
public function executeAssertions(
    string $authorizationModelId,
    OpenFGA\Models\Collections\AssertionsInterface $assertions,
): OpenFGA\Results\Failure|OpenFGA\Results\Success|OpenFGA\Results\SuccessInterface

```

Execute assertions against the authorization model. Runs the specified assertions and returns the results, comparing expected outcomes with actual authorization check results. This helps verify that your authorization model works correctly.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Services/AssertionService.php#L64)

#### Parameters

| Name                    | Type                                                               | Description                     |
| ----------------------- | ------------------------------------------------------------------ | ------------------------------- |
| `$authorizationModelId` | `string`                                                           | The authorization model to test |
| `$assertions`           | [`AssertionsInterface`](Models/Collections/AssertionsInterface.md) | The assertions to execute       |

#### Returns

[`Failure`](Results/Failure.md) &#124; [`Success`](Results/Success.md) &#124; [`SuccessInterface`](Results/SuccessInterface.md) — Success with test results, or Failure with execution errors
