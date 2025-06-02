# ListTupleChangesResponse

Response containing a paginated list of tuple changes from the store. This response provides a collection of tuple changes (additions, deletions) along with pagination information for retrieving additional pages of results. Use this to track the history of relationship changes in your authorization store.

## Namespace
`OpenFGA\Responses`

## Implements
* [ListTupleChangesResponseInterface](ListTupleChangesResponseInterface.md)
* [ResponseInterface](ResponseInterface.md)



## Methods
### fromResponse

*<small>Implements Responses\ListTupleChangesResponseInterface</small>*  

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

### getChanges


```php
public function getChanges(): OpenFGA\Models\Collections\TupleChangesInterface
```

Get the collection of tuple changes. Returns a type-safe collection containing the tuple change objects from the current page of results. Each change represents a modification (insert or delete) to the relationship data, including timestamps and operation details.


#### Returns
OpenFGA\Models\Collections\TupleChangesInterface
 The collection of tuple changes

### getContinuationToken


```php
public function getContinuationToken(): ?string
```

Get the continuation token for pagination. Returns a token that can be used to retrieve the next page of results when the total number of tuple changes exceeds the page size limit. If null, there are no more results to fetch.


#### Returns
?string
 The continuation token for fetching more results, or null if no more pages exist

### schema

*<small>Implements Responses\ListTupleChangesResponseInterface</small>*  

```php
public function schema(): SchemaInterface
```

Get the schema definition for this response. Returns the schema that defines the structure and validation rules for tuple changes listing response data, ensuring consistent parsing and validation of API responses.


#### Returns
SchemaInterface
 The schema definition for response validation

