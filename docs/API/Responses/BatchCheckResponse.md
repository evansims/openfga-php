# BatchCheckResponse

Response containing the results of a batch authorization check. This response contains a map of correlation IDs to check results, allowing you to match each result back to the original check request using the correlation ID that was provided in the batch request.

## Namespace

`OpenFGA\Responses`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Responses/BatchCheckResponse.php)

## Implements

* [`BatchCheckResponseInterface`](BatchCheckResponseInterface.md)
* [`ResponseInterface`](ResponseInterface.md)

## Related Classes

* [BatchCheckResponseInterface](Responses/BatchCheckResponseInterface.md) (interface)
* [BatchCheckRequest](Requests/BatchCheckRequest.md) (request)

## Methods

### List Operations

#### getResult

```php
public function getResult(): array

```

Get the results map from correlation IDs to check results. Each key in the map is a correlation ID from the original request, and each value is the result of that specific check.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Responses/BatchCheckResponse.php#L102)

#### Returns

`array` — Map of correlation ID to check result

#### getResultForCorrelationId

```php
public function getResultForCorrelationId(string $correlationId): ?OpenFGA\Models\BatchCheckSingleResultInterface

```

Get the result for a specific correlation ID. Returns the check result for the given correlation ID, or null if no result exists for that ID.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Responses/BatchCheckResponse.php#L111)

#### Parameters

| Name             | Type     | Description                   |
| ---------------- | -------- | ----------------------------- |
| `$correlationId` | `string` | The correlation ID to look up |

#### Returns

[`BatchCheckSingleResultInterface`](Models/BatchCheckSingleResultInterface.md) &#124; `null`

### Other

#### fromResponse

*<small>Implements Responses\BatchCheckResponseInterface</small>*

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
