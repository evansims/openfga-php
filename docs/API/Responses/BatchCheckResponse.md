# BatchCheckResponse

Response containing the results of a batch authorization check. This response contains a map of correlation IDs to check results, allowing you to match each result back to the original check request using the correlation ID that was provided in the batch request.

## Namespace
`OpenFGA\Responses`

## Implements
* [BatchCheckResponseInterface](Responses/BatchCheckResponseInterface.md)
* [ResponseInterface](Responses/ResponseInterface.md)



## Methods
### fromResponse

*<small>Implements Responses\BatchCheckResponseInterface</small>*  

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

### getResult


```php
public function getResult(): array
```

Get the results map from correlation IDs to check results. Each key in the map is a correlation ID from the original request, and each value is the result of that specific check.


#### Returns
array
 Map of correlation ID to check result

### getResultForCorrelationId


```php
public function getResultForCorrelationId(string $correlationId): ?OpenFGA\Models\BatchCheckSingleResultInterface
```

Get the result for a specific correlation ID. Returns the check result for the given correlation ID, or null if no result exists for that ID.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$correlationId` | string | The correlation ID to look up |

#### Returns
?[BatchCheckSingleResultInterface](Models/BatchCheckSingleResultInterface.md)

