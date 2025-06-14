# RequestManagerFactory

Factory for creating RequestManager instances. This factory encapsulates the creation of RequestManager instances with the appropriate configuration for different use cases (normal requests vs batch operations).

## Table of Contents

- [Namespace](#namespace)
- [Source](#source)
- [Methods](#methods)

- [CRUD Operations](#crud-operations)
  - [`create()`](#create)
  - [`createForBatch()`](#createforbatch)
  - [`createWithRetries()`](#createwithretries)

## Namespace

`OpenFGA\Network`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Network/RequestManagerFactory.php)

## Methods

#### create

```php
public function create(): OpenFGA\Network\RequestManager

```

Create a RequestManager for normal operations.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Network/RequestManagerFactory.php#L38)

#### Returns

[`RequestManager`](RequestManager.md)

#### createForBatch

```php
public function createForBatch(): OpenFGA\Network\RequestManager

```

Create a RequestManager for batch operations (no HTTP retries).

[View source](https://github.com/evansims/openfga-php/blob/main/src/Network/RequestManagerFactory.php#L58)

#### Returns

[`RequestManager`](RequestManager.md)

#### createWithRetries

```php
public function createWithRetries(int $maxRetries): OpenFGA\Network\RequestManager

```

Create a RequestManager with custom retry configuration.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Network/RequestManagerFactory.php#L80)

#### Parameters

| Name          | Type  | Description |
| ------------- | ----- | ----------- |
| `$maxRetries` | `int` |             |

#### Returns

[`RequestManager`](RequestManager.md)
