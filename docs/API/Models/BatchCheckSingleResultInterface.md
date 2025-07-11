# BatchCheckSingleResultInterface

Represents the result of a single check within a batch check response. Each result contains whether the check was allowed and any error information if the check failed to complete successfully.

<details>
<summary><strong>Table of Contents</strong></summary>

- [Namespace](#namespace)
- [Source](#source)
- [Implements](#implements)
- [Related Classes](#related-classes)
- [Methods](#methods)

- [`getAllowed()`](#getallowed)
  - [`getError()`](#geterror)
  - [`jsonSerialize()`](#jsonserialize)

</details>

## Namespace

`OpenFGA\Models`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Models/BatchCheckSingleResultInterface.php)

## Implements

- [`ModelInterface`](ModelInterface.md)
- `JsonSerializable`

## Related Classes

- [BatchCheckSingleResult](Models/BatchCheckSingleResult.md) (implementation)

## Methods

### getAllowed

```php
public function getAllowed(): ?bool

```

Get whether this check was allowed. Returns true if the user has the specified relationship with the object, false if they don&#039;t, or null if the check encountered an error.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/BatchCheckSingleResultInterface.php#L25)

#### Returns

`bool` &#124; `null`

### getError

```php
public function getError(): ?object

```

Get any error that occurred during this check. Returns error information if the check failed to complete successfully, or null if the check completed without errors.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/BatchCheckSingleResultInterface.php#L35)

#### Returns

`object` &#124; `null`

### jsonSerialize

```php
public function jsonSerialize()

```

