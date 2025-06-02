# ReadTuplesResponse

Response containing a paginated list of relationship tuples. This response provides access to relationship tuples that match the query criteria, with pagination support for handling large result sets. Each tuple represents a specific relationship between a user and an object.

## Namespace
`OpenFGA\Responses`

## Source
[View source code](https://github.com/evansims/openfga-php/blob/main/src/Responses/ReadTuplesResponse.php)

## Implements
* [`ReadTuplesResponseInterface`](ReadTuplesResponseInterface.md)
* [`ResponseInterface`](ResponseInterface.md)

## Related Classes
* [ReadTuplesResponseInterface](Responses/ReadTuplesResponseInterface.md) (interface)
* [ReadTuplesRequest](Requests/ReadTuplesRequest.md) (request)



## Methods

                                                                                    
### List Operations
#### getContinuationToken


```php
public function getContinuationToken(): ?string
```

Get the continuation token for pagination. Returns a token that can be used to retrieve the next page of results when the total number of matching tuples exceeds the page size limit. If null, there are no more results to fetch.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Responses/ReadTuplesResponse.php#L95)


#### Returns
`string` &#124; `null` — The continuation token for fetching more results, or null if no more pages exist
#### getTuples


```php
public function getTuples(): OpenFGA\Models\Collections\TuplesInterface
```

Get the collection of relationship tuples. Returns a type-safe collection containing the tuple objects that match the read query criteria. Each tuple represents a relationship between a user and an object through a specific relation.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Responses/ReadTuplesResponse.php#L104)


#### Returns
[`TuplesInterface`](Models/Collections/TuplesInterface.md) — The collection of relationship tuples
### Model Management
#### schema

*<small>Implements Responses\ReadTuplesResponseInterface</small>*  

```php
public function schema(): SchemaInterface
```

Get the schema definition for this response. Returns the schema that defines the structure and validation rules for tuple reading response data, ensuring consistent parsing and validation of API responses.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Responses/ReadTuplesResponseInterface.php#L34)


#### Returns
`SchemaInterface` — The schema definition for response validation
### Other
#### fromResponse

*<small>Implements Responses\ReadTuplesResponseInterface</small>*  

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
| Name | Type | Description |
|------|------|-------------|
| `$response` | `HttpResponseInterface` | The raw HTTP response from the OpenFGA API |
| `$request` | `HttpRequestInterface` | The original HTTP request that generated this response |
| `$validator` | `SchemaValidator` | Schema validator for parsing and validating response data |

#### Returns
`static` — The parsed and validated response instance containing the API response data
