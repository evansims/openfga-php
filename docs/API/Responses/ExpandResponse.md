# ExpandResponse

Response containing the expanded userset tree for a relationship query. This response provides a hierarchical tree structure showing how a relationship is computed, including all the users, usersets, and computed relationships that contribute to the final authorization decision.

## Namespace
`OpenFGA\Responses`

## Implements
* [ExpandResponseInterface](Responses/ExpandResponseInterface.md)
* [ResponseInterface](Responses/ResponseInterface.md)



## Methods
### fromResponse

*<small>Implements Responses\ExpandResponseInterface</small>*  

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

### getTree


```php
public function getTree(): ?OpenFGA\Models\UsersetTreeInterface
```

Get the expansion tree for the queried relationship. Returns a hierarchical tree structure that represents all users and usersets that have the specified relationship with the target object. The tree shows both direct relationships and computed relationships through other relations.


#### Returns
?[UsersetTreeInterface](Models/UsersetTreeInterface.md)
 The relationship expansion tree, or null if no relationships found

### schema

*<small>Implements Responses\ExpandResponseInterface</small>*  

```php
public function schema(): SchemaInterface
```

Get the schema definition for this response. Returns the schema that defines the structure and validation rules for relationship expansion response data, ensuring consistent parsing and validation of API responses.


#### Returns
SchemaInterface
 The schema definition for response validation

