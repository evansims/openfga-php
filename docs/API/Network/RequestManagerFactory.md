# RequestManagerFactory

Factory for creating RequestManager instances. This factory encapsulates the creation of RequestManager instances with the appropriate configuration for different use cases (normal requests vs batch operations).

<details>
<summary><strong>Table of Contents</strong></summary>

- [Namespace](#namespace)
- [Source](#source)
- [Methods](#methods)

- [`create()`](#create)
  - [`createForBatch()`](#createforbatch)
  - [`createWithRetries()`](#createwithretries)

</details>

## Namespace

`OpenFGA\Network`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Network/RequestManagerFactory.php)

## Methods

### create

```php
public function create(): RequestManager

```

Create a RequestManager for normal operations.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Network/RequestManagerFactory.php#L55)

#### Returns

[`RequestManager`](RequestManager.md) — A RequestManager configured with default retry settings

### createForBatch

```php
public function createForBatch(): RequestManager

```

Create a RequestManager for batch operations (no HTTP retries).

[View source](https://github.com/evansims/openfga-php/blob/main/src/Network/RequestManagerFactory.php#L77)

#### Returns

[`RequestManager`](RequestManager.md) — A RequestManager configured with retries disabled

### createWithRetries

```php
public function createWithRetries(int $maxRetries): RequestManager

```

Create a RequestManager with custom retry configuration.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Network/RequestManagerFactory.php#L101)

#### Parameters

| Name          | Type  | Description                      |
| ------------- | ----- | -------------------------------- |
| `$maxRetries` | `int` | Maximum number of retry attempts |

#### Returns

[`RequestManager`](RequestManager.md) — A RequestManager configured with the specified retry limit
