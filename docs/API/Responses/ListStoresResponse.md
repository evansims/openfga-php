# ListStoresResponse

Response containing a paginated list of available stores. This response provides access to stores that the authenticated user or application can access, with pagination support for handling large numbers of stores. Each store includes its ID, name, and creation metadata.

## Namespace

`OpenFGA\Responses`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Responses/ListStoresResponse.php)

## Implements

* [`ListStoresResponseInterface`](ListStoresResponseInterface.md)
* [`ResponseInterface`](ResponseInterface.md)

## Related Classes

* [ListStoresResponseInterface](Responses/ListStoresResponseInterface.md) (interface)
* [ListStoresRequest](Requests/ListStoresRequest.md) (request)

## Methods

### List Operations

#### getContinuationToken

```php
public function getContinuationToken(): ?string

```

Get the continuation token for pagination. Returns a token that can be used to retrieve the next page of results when the total number of stores exceeds the page size limit. If null, there are no more results to fetch.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Responses/ListStoresResponse.php#L92)

#### Returns

`string` &#124; `null` — The continuation token for fetching more results, or null if no more pages exist

#### getStores

```php
public function getStores(): OpenFGA\Models\Collections\StoresInterface

```

Get the collection of stores. Returns a type-safe collection containing the store objects from the current page of results. Each store includes its metadata such as ID, name, and timestamps.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Responses/ListStoresResponse.php#L101)

#### Returns

[`StoresInterface`](Models/Collections/StoresInterface.md) — The collection of stores

### Model Management

#### schema

*<small>Implements Responses\ListStoresResponseInterface</small>*

```php
public function schema(): SchemaInterface

```

Get the schema definition for this response. Returns the schema that defines the structure and validation rules for store listing response data, ensuring consistent parsing and validation of API responses.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Responses/ListStoresResponseInterface.php#L33)

#### Returns

`SchemaInterface` — The schema definition for response validation

### Other

#### fromResponse

*<small>Implements Responses\ListStoresResponseInterface</small>*

```php
public function fromResponse(
    HttpResponseInterface $response,
    HttpRequestInterface $request,
    SchemaValidator $validator,
): static

```

Create a response instance from an HTTP response. This method transforms a raw HTTP response from the OpenFGA API into a structured response object, validating and parsing the response data according to the expected schema. It handles both successful responses by parsing and validating the data, and error responses by throwing appropriate exceptions.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Responses/ResponseInterface.php#L44)

#### Parameters

| Name         | Type                    | Description                                               |
| ------------ | ----------------------- | --------------------------------------------------------- |
| `$response`  | `HttpResponseInterface` | The raw HTTP response from the OpenFGA API                |
| `$request`   | `HttpRequestInterface`  | The original HTTP request that generated this response    |
| `$validator` | `SchemaValidator`       | Schema validator for parsing and validating response data |

#### Returns

`static` — The parsed and validated response instance containing the API response data
