# ListAuthorizationModelsResponse

Response containing a paginated list of authorization models. This response provides access to authorization models within a store, including pagination support for handling large numbers of models. Each model includes its ID, schema version, and complete type definitions.

## Namespace
`OpenFGA\Responses`

## Implements
* [ListAuthorizationModelsResponseInterface](ListAuthorizationModelsResponseInterface.md)
* [ResponseInterface](ResponseInterface.md)



## Methods
### fromResponse

*<small>Implements Responses\ListAuthorizationModelsResponseInterface</small>*  

```php
public function fromResponse(HttpResponseInterface $response, HttpRequestInterface $request, SchemaValidator $validator): static
```

Create a response instance from an HTTP response. This method transforms a raw HTTP response from the OpenFGA API into a structured response object, validating and parsing the response data according to the expected schema. It handles both successful responses by parsing and validating the data, and error responses by throwing appropriate exceptions.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$response` | HttpResponseInterface | The raw HTTP response from the OpenFGA API |
| `$request` | HttpRequestInterface | The original HTTP request that generated this response |
| `$validator` | SchemaValidator | Schema validator for parsing and validating response data |

#### Returns
static
 The parsed and validated response instance containing the API response data

### getContinuationToken


```php
public function getContinuationToken(): ?string
```

Get the continuation token for pagination. Returns a token that can be used to retrieve the next page of results when the total number of authorization models exceeds the page size limit. If null, there are no more results to fetch.


#### Returns
?string
 The continuation token for fetching more results, or null if no more pages exist

### getModels


```php
public function getModels(): OpenFGA\Models\Collections\AuthorizationModelsInterface
```

Get the collection of authorization models. Returns a type-safe collection containing the authorization model objects from the current page of results. Each model includes its ID, type definitions, schema version, and any conditions.


#### Returns
OpenFGA\Models\Collections\AuthorizationModelsInterface
 The collection of authorization models

### schema

*<small>Implements Responses\ListAuthorizationModelsResponseInterface</small>*  

```php
public function schema(): SchemaInterface
```

Get the schema definition for this response. Returns the schema that defines the structure and validation rules for authorization models listing response data, ensuring consistent parsing and validation of API responses.


#### Returns
SchemaInterface
 The schema definition for response validation

