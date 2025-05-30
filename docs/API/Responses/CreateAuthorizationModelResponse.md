# CreateAuthorizationModelResponse

Response confirming successful creation of a new authorization model. This response provides the unique identifier of the newly created authorization model, which can be used for subsequent operations like checks, expansions, and model management activities.

## Namespace
`OpenFGA\Responses`

## Implements
* [CreateAuthorizationModelResponseInterface](Responses/CreateAuthorizationModelResponseInterface.md)
* [ResponseInterface](Responses/ResponseInterface.md)



## Methods
### fromResponse

*<small>Implements Responses\CreateAuthorizationModelResponseInterface</small>*  

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

### getModel


```php
public function getModel(): string
```

Get the unique identifier of the created authorization model. Returns the system-generated unique identifier for the newly created authorization model. This ID is used in subsequent API operations to reference this specific model version for authorization checks and other operations.


#### Returns
string
 The unique authorization model identifier

### schema

*<small>Implements Responses\CreateAuthorizationModelResponseInterface</small>*  

```php
public function schema(): SchemaInterface
```

Get the schema definition for this response. Returns the schema that defines the structure and validation rules for authorization model creation response data, ensuring consistent parsing and validation of API responses.


#### Returns
SchemaInterface
 The schema definition for response validation

