# BatchCheckResponseInterface

Response containing the results of a batch authorization check. This response contains a map of correlation IDs to check results, allowing you to match each result back to the original check request using the correlation ID that was provided in the batch request.

## Namespace
`OpenFGA\Responses`

## Source
[View source code](https://github.com/evansims/openfga-php/blob/main/src/Responses/BatchCheckResponseInterface.php)

## Implements
* [ResponseInterface](ResponseInterface.md)



## Methods
### getResult


```php
public function getResult(): array<string, BatchCheckSingleResultInterface>
```

Get the results map from correlation IDs to check results. Each key in the map is a correlation ID from the original request, and each value is the result of that specific check.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Responses/BatchCheckResponseInterface.php#L29)


#### Returns
array&lt;string, BatchCheckSingleResultInterface&gt;
 Map of correlation ID to check result

### getResultForCorrelationId


```php
public function getResultForCorrelationId(string $correlationId): ?OpenFGA\Models\BatchCheckSingleResultInterface
```

Get the result for a specific correlation ID. Returns the check result for the given correlation ID, or null if no result exists for that ID.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Responses/BatchCheckResponseInterface.php#L40)

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$correlationId` | string | The correlation ID to look up |

#### Returns
?OpenFGA\Models\BatchCheckSingleResultInterface

