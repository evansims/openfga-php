# AssertionServiceInterface

Service interface for managing OpenFGA authorization model assertions. This service provides business-focused operations for working with assertions, which are test cases that validate the behavior of authorization models. Assertions help ensure that your authorization model works as expected by defining specific scenarios and their expected outcomes. ## Core Operations The service supports assertion management with enhanced functionality: - Read existing assertions from authorization models - Write new assertions to validate model behavior - Validate assertion syntax and logic - Batch operations for managing multiple assertions ## Assertion Validation Assertions define test cases like: - &quot;user:anne should have reader access to document:budget-2024&quot; - &quot;user:bob should NOT have admin access to folder:public&quot; - &quot;group:finance#member should have write access to report:quarterly&quot; ## Usage Example ```php $assertionService = new AssertionService($assertionRepository); Read existing assertions $assertions = $assertionService-&gt;readAssertions( $store, $authorizationModel )-&gt;unwrap(); Write new assertions $newAssertions = new Assertions([ new Assertion( new TupleKey(&#039;user:anne&#039;, &#039;reader&#039;, &#039;document:budget&#039;), true // expected result ) ]); $result = $assertionService-&gt;writeAssertions( $store, $authorizationModel, $newAssertions )-&gt;unwrap(); ```

## Table of Contents

* [Namespace](#namespace)
* [Source](#source)
* [Related Classes](#related-classes)
* [Methods](#methods)

* [Authorization](#authorization)
    * [`validateAssertions()`](#validateassertions)
* [CRUD Operations](#crud-operations)
    * [`readAssertions()`](#readassertions)
    * [`writeAssertions()`](#writeassertions)
* [List Operations](#list-operations)
    * [`getAssertionStatistics()`](#getassertionstatistics)
* [Utility](#utility)
    * [`clearAssertions()`](#clearassertions)
    * [`executeAssertions()`](#executeassertions)

## Namespace

`OpenFGA\Services`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Services/AssertionServiceInterface.php)

## Related Classes

* [AssertionService](Services/AssertionService.md) (implementation)

## Methods

### Authorization

#### validateAssertions

```php
public function validateAssertions(
    AssertionsInterface $assertions,
    string $authorizationModelId,
): FailureInterface|SuccessInterface

```

Validate assertion syntax and logic. Checks that assertions are properly formatted and reference valid types and relations from the authorization model. This helps catch errors before deploying assertions to production.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Services/AssertionServiceInterface.php#L135)

#### Parameters

| Name                    | Type                                                               | Description                                 |
| ----------------------- | ------------------------------------------------------------------ | ------------------------------------------- |
| `$assertions`           | [`AssertionsInterface`](Models/Collections/AssertionsInterface.md) | The assertions to validate                  |
| `$authorizationModelId` | `string`                                                           | The authorization model to validate against |

#### Returns

[`FailureInterface`](Results/FailureInterface.md) &#124; [`SuccessInterface`](Results/SuccessInterface.md) — Success if valid, or Failure with validation errors

### CRUD Operations

#### readAssertions

```php
public function readAssertions(string $authorizationModelId): FailureInterface|SuccessInterface

```

Read assertions from an authorization model. Retrieves all test assertions defined in the specified authorization model. Assertions validate that the model behaves correctly for specific scenarios.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Services/AssertionServiceInterface.php#L120)

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
    AssertionsInterface $assertions,
): FailureInterface|SuccessInterface

```

Write assertions to an authorization model. Updates the test assertions for the specified authorization model. Assertions help validate that your authorization model works as expected by defining specific test cases and their expected outcomes.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Services/AssertionServiceInterface.php#L151)

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
    StoreInterface|string $store,
    string $authorizationModelId,
): FailureInterface|SuccessInterface

```

Get assertion execution statistics. Provides insights into assertion test results, including pass/fail counts, execution times, and common failure patterns. Useful for monitoring authorization model health and test coverage.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Services/AssertionServiceInterface.php#L106)

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
public function clearAssertions(string $authorizationModelId): FailureInterface|SuccessInterface

```

Clear all assertions from an authorization model. Removes all test assertions from the specified authorization model. This is useful when completely restructuring test cases or during development iterations.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Services/AssertionServiceInterface.php#L75)

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
    AssertionsInterface $assertions,
): FailureInterface|SuccessInterface

```

Execute assertions against the authorization model. Runs the specified assertions and returns the results, comparing expected outcomes with actual authorization check results. This helps verify that your authorization model works correctly.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Services/AssertionServiceInterface.php#L90)

#### Parameters

| Name                    | Type                                                               | Description                     |
| ----------------------- | ------------------------------------------------------------------ | ------------------------------- |
| `$authorizationModelId` | `string`                                                           | The authorization model to test |
| `$assertions`           | [`AssertionsInterface`](Models/Collections/AssertionsInterface.md) | The assertions to execute       |

#### Returns

[`FailureInterface`](Results/FailureInterface.md) &#124; [`SuccessInterface`](Results/SuccessInterface.md) — Success with test results, or Failure with execution errors
