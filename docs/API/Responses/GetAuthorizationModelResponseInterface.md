# GetAuthorizationModelResponseInterface

Interface for authorization model retrieval response objects. This interface defines the contract for responses returned when retrieving authorization models from OpenFGA. An authorization model defines the relationship types, object types, and permission logic that govern how authorization decisions are made within a store. Authorization models are versioned, allowing you to evolve your permission structure over time while maintaining consistency for existing authorization checks.

## Namespace

`OpenFGA\Responses`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Responses/GetAuthorizationModelResponseInterface.php)

## Implements

* [`ResponseInterface`](ResponseInterface.md)

## Related Classes

* [GetAuthorizationModelResponse](Responses/GetAuthorizationModelResponse.md) (implementation)

* [GetAuthorizationModelRequestInterface](Requests/GetAuthorizationModelRequestInterface.md) (request)

## Methods

#### getModel

```php
public function getModel(): AuthorizationModelInterface|null

```

Get the retrieved authorization model. Returns the complete authorization model including its type definitions, schema version, and any conditions. The model defines the relationship types and permission logic that govern authorization decisions within the store.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Responses/GetAuthorizationModelResponseInterface.php#L44)

#### Returns

[`AuthorizationModelInterface`](Models/AuthorizationModelInterface.md) &#124; `null` — The authorization model, or null if not found
