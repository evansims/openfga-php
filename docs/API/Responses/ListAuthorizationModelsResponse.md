# ListAuthorizationModelsResponse

Response containing a paginated list of authorization models. This response provides access to authorization models within a store, including pagination support for handling large numbers of models. Each model includes its ID, schema version, and complete type definitions.

## Table of Contents

* [Namespace](#namespace)
* [Source](#source)
* [Implements](#implements)
* [Related Classes](#related-classes)
* [Methods](#methods)

* [List Operations](#list-operations)
    * [`getContinuationToken()`](#getcontinuationtoken)
    * [`getModels()`](#getmodels)
* [Model Management](#model-management)
    * [`schema()`](#schema)
* [Other](#other)
    * [`fromResponse()`](#fromresponse)

## Namespace

`OpenFGA\Responses`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Responses/ListAuthorizationModelsResponse.php)

## Implements

* [`ListAuthorizationModelsResponseInterface`](ListAuthorizationModelsResponseInterface.md)
* [`ResponseInterface`](ResponseInterface.md)

## Related Classes

* [ListAuthorizationModelsResponseInterface](Responses/ListAuthorizationModelsResponseInterface.md) (interface)
* [ListAuthorizationModelsRequest](Requests/ListAuthorizationModelsRequest.md) (request)

## Methods

### List Operations

#### getContinuationToken

```php
public function getContinuationToken(): ?string

```

Get the continuation token for pagination. Returns a token that can be used to retrieve the next page of results when the total number of authorization models exceeds the page size limit. If null, there are no more results to fetch.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Responses/ListAuthorizationModelsResponse.php#L102)

#### Returns

`string` &#124; `null` — The continuation token for fetching more results, or null if no more pages exist

#### getModels

```php
public function getModels(): OpenFGA\Models\Collections\AuthorizationModelsInterface

```

Get the collection of authorization models. Returns a type-safe collection containing the authorization model objects from the current page of results. Each model includes its ID, type definitions, schema version, and any conditions.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Responses/ListAuthorizationModelsResponse.php#L111)

#### Returns

[`AuthorizationModelsInterface`](Models/Collections/AuthorizationModelsInterface.md) — The collection of authorization models

### Model Management

#### schema

*<small>Implements Responses\ListAuthorizationModelsResponseInterface</small>*

```php
public function schema(): SchemaInterface

```

Get the schema definition for this response. Returns the schema that defines the structure and validation rules for authorization models listing response data, ensuring consistent parsing and validation of API responses.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Responses/ListAuthorizationModelsResponseInterface.php#L33)

#### Returns

`SchemaInterface` — The schema definition for response validation

### Other

#### fromResponse

*<small>Implements Responses\ListAuthorizationModelsResponseInterface</small>*

```php
public function fromResponse(
    HttpResponseInterface $response,
    HttpRequestInterface $request,
    SchemaValidatorInterface $validator,
): static

```

Create a response instance from an HTTP response. This method transforms a raw HTTP response from the OpenFGA API into a structured response object, validating and parsing the response data according to the expected schema. It handles both successful responses by parsing and validating the data, and error responses by throwing appropriate exceptions.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Responses/ResponseInterface.php#L44)

#### Parameters

| Name         | Type                       | Description                                               |
| ------------ | -------------------------- | --------------------------------------------------------- |
| `$response`  | `HttpResponseInterface`    | The raw HTTP response from the OpenFGA API                |
| `$request`   | `HttpRequestInterface`     | The original HTTP request that generated this response    |
| `$validator` | `SchemaValidatorInterface` | Schema validator for parsing and validating response data |

#### Returns

`static` — The parsed and validated response instance containing the API response data
