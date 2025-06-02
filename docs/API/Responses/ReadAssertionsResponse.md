# ReadAssertionsResponse

Response containing test assertions associated with an authorization model. This response provides access to test assertions that validate authorization model behavior. These assertions define expected outcomes for specific authorization scenarios and help ensure model correctness.

## Namespace
`OpenFGA\Responses`

## Implements
* [ReadAssertionsResponseInterface](ReadAssertionsResponseInterface.md)
* [ResponseInterface](ResponseInterface.md)



## Methods
### fromResponse

*<small>Implements Responses\ReadAssertionsResponseInterface</small>*  

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

### getAssertions


```php
public function getAssertions(): ?OpenFGA\Models\Collections\AssertionsInterface
```

Get the collection of assertions from the authorization model. Returns a type-safe collection containing the assertion objects associated with the authorization model. Each assertion defines a test case with expected permission check results for validating model behavior.


#### Returns
?OpenFGA\Models\Collections\AssertionsInterface
 The collection of assertions, or null if no assertions are defined

### getModel


```php
public function getModel(): string
```

Get the authorization model identifier for these assertions. Returns the unique identifier of the authorization model that contains these assertions. This ties the assertions to a specific model version for validation and testing purposes.


#### Returns
string
 The authorization model identifier

### schema

*<small>Implements Responses\ReadAssertionsResponseInterface</small>*  

```php
public function schema(): SchemaInterface
```

Get the schema definition for this response. Returns the schema that defines the structure and validation rules for assertions reading response data, ensuring consistent parsing and validation of API responses.


#### Returns
SchemaInterface
 The schema definition for response validation

