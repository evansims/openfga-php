# CreateAuthorizationModelResponseInterface

Interface for authorization model creation response objects. This interface defines the contract for responses returned when creating new authorization models in OpenFGA. An authorization model creation response contains the unique identifier of the newly created model, which can be used for subsequent operations. Authorization models define the relationship types, object types, and permission logic that govern how authorization decisions are made within a store. They are versioned, allowing you to evolve your permission structure over time.

## Table of Contents

* [Namespace](#namespace)
* [Source](#source)
* [Implements](#implements)
* [Related Classes](#related-classes)
* [Methods](#methods)

* [List Operations](#list-operations)
    * [`getModel()`](#getmodel)

## Namespace

`OpenFGA\Responses`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Responses/CreateAuthorizationModelResponseInterface.php)

## Implements

* [`ResponseInterface`](ResponseInterface.md)

## Related Classes

* [CreateAuthorizationModelResponse](Responses/CreateAuthorizationModelResponse.md) (implementation)
* [CreateAuthorizationModelRequestInterface](Requests/CreateAuthorizationModelRequestInterface.md) (request)

## Methods

#### getModel

```php
public function getModel(): string

```

Get the unique identifier of the created authorization model. Returns the system-generated unique identifier for the newly created authorization model. This ID is used in subsequent API operations to reference this specific model version for authorization checks and other operations.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Responses/CreateAuthorizationModelResponseInterface.php#L43)

#### Returns

`string` — The unique authorization model identifier
