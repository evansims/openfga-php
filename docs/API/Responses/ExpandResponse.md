# ExpandResponse

Response containing the expanded userset tree for a relationship query. This response provides a hierarchical tree structure showing how a relationship is computed, including all the users, usersets, and computed relationships that contribute to the final authorization decision.

## Namespace
`OpenFGA\Responses`

## Source
[View source code](https://github.com/evansims/openfga-php/blob/main/src/Responses/ExpandResponse.php)

## Implements
* [`ExpandResponseInterface`](ExpandResponseInterface.md)
* [`ResponseInterface`](ResponseInterface.md)

## Related Classes
* [ExpandResponseInterface](Responses/ExpandResponseInterface.md) (interface)
* [ExpandRequest](Requests/ExpandRequest.md) (request)

## Methods

### List Operations
#### getTree

```php
public function getTree(): ?OpenFGA\Models\UsersetTreeInterface
```

Get the expansion tree for the queried relationship. Returns a hierarchical tree structure that represents all users and usersets that have the specified relationship with the target object. The tree shows both direct relationships and computed relationships through other relations.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Responses/ExpandResponse.php#L107)

#### Returns
[`UsersetTreeInterface`](Models/UsersetTreeInterface.md) &#124; `null` — The relationship expansion tree, or null if no relationships found
### Model Management
#### schema

*<small>Implements Responses\ExpandResponseInterface</small>*

```php
public function schema(): SchemaInterface
```

Get the schema definition for this response. Returns the schema that defines the structure and validation rules for relationship expansion response data, ensuring consistent parsing and validation of API responses.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Responses/ExpandResponseInterface.php#L34)

#### Returns
`SchemaInterface` — The schema definition for response validation
### Other
#### fromResponse

*<small>Implements Responses\ExpandResponseInterface</small>*

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
| Name         | Type                    | Description                                               |
| ------------ | ----------------------- | --------------------------------------------------------- |
| `$response`  | `HttpResponseInterface` | The raw HTTP response from the OpenFGA API                |
| `$request`   | `HttpRequestInterface`  | The original HTTP request that generated this response    |
| `$validator` | `SchemaValidator`       | Schema validator for parsing and validating response data |

#### Returns
`static` — The parsed and validated response instance containing the API response data
