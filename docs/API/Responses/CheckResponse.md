# CheckResponse

Response containing the result of an authorization check. This response indicates whether a user has a specific relationship with an object, along with optional resolution details explaining how the decision was reached. Use this to make authorization decisions in your application.

## Namespace
`OpenFGA\Responses`

## Implements
* [CheckResponseInterface](Responses/CheckResponseInterface.md)
* [ResponseInterface](Responses/ResponseInterface.md)



## Methods
### fromResponse

*<small>Implements Responses\CheckResponseInterface</small>*  

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

### getAllowed


```php
public function getAllowed(): ?bool
```

Get whether the permission check was allowed. This is the primary result of the permission check operation, indicating whether the specified user has the requested permission on the given object according to the authorization model and current relationship data.


#### Returns
?bool
 True if permission is granted, false if denied, or null if the result is indeterminate

### getResolution


```php
public function getResolution(): ?string
```

Get the resolution details for the permission decision. This provides additional information about how the permission decision was reached, which can be useful for understanding complex authorization logic or debugging permission issues.


#### Returns
?string
 The resolution details explaining the permission decision, or null if not provided

### schema

*<small>Implements Responses\CheckResponseInterface</small>*  

```php
public function schema(): SchemaInterface
```

Get the schema definition for this response. This method returns the schema that defines the structure and validation rules for check response data, ensuring consistent parsing and validation.


#### Returns
SchemaInterface
 The schema definition for check response validation

