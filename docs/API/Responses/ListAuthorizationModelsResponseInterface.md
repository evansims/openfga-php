# ListAuthorizationModelsResponseInterface

Interface for authorization models listing response objects. This interface defines the contract for responses returned when listing authorization models from an OpenFGA store. The response includes a collection of authorization models and pagination support for handling large numbers of models efficiently. Authorization model listing is useful for administrative operations, model versioning management, and allowing users to select from available model versions.

## Namespace
`OpenFGA\Responses`

## Source
[View source code](https://github.com/evansims/openfga-php/blob/main/src/Responses/ListAuthorizationModelsResponseInterface.php)

## Implements
* [`ResponseInterface`](ResponseInterface.md)

## Related Classes
* [ListAuthorizationModelsResponse](Responses/ListAuthorizationModelsResponse.md) (implementation)
* [ListAuthorizationModelsRequestInterface](Requests/ListAuthorizationModelsRequestInterface.md) (request)

## Methods

#### getContinuationToken

```php
public function getContinuationToken(): string|null
```

Get the continuation token for pagination. Returns a token that can be used to retrieve the next page of results when the total number of authorization models exceeds the page size limit. If null, there are no more results to fetch.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Responses/ListAuthorizationModelsResponseInterface.php#L45)

#### Returns
`string` &#124; `null` — The continuation token for fetching more results, or null if no more pages exist
#### getModels

```php
public function getModels(): AuthorizationModelsInterface<AuthorizationModelInterface>
```

Get the collection of authorization models. Returns a type-safe collection containing the authorization model objects from the current page of results. Each model includes its ID, type definitions, schema version, and any conditions.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Responses/ListAuthorizationModelsResponseInterface.php#L56)

#### Returns
[`AuthorizationModelsInterface`](Models/Collections/AuthorizationModelsInterface.md)&lt;[`AuthorizationModelInterface`](Models/AuthorizationModelInterface.md)&gt; — The collection of authorization models
