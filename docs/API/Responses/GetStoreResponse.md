# GetStoreResponse

Response containing detailed information about a specific store. This response provides comprehensive store metadata including its unique identifier, name, and timestamps for creation, updates, and deletion (if applicable). Use this to retrieve information about an authorization store.

## Namespace
`OpenFGA\Responses`

## Implements
* [GetStoreResponseInterface](GetStoreResponseInterface.md)
* [ResponseInterface](ResponseInterface.md)



## Methods
### fromResponse

*<small>Implements Responses\GetStoreResponseInterface</small>*  

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

### getCreatedAt


```php
public function getCreatedAt(): DateTimeImmutable
```

Get the timestamp when the store was created. Returns the exact moment when the store was successfully created in the OpenFGA system. This timestamp is immutable and set by the server upon store creation.


#### Returns
DateTimeImmutable
 The creation timestamp of the store

### getDeletedAt


```php
public function getDeletedAt(): ?DateTimeImmutable
```

Get the timestamp when the store was deleted, if applicable. Returns the deletion timestamp for soft-deleted stores, or null if the store is active. This is used for stores that have been marked for deletion but may still be accessible for a grace period.


#### Returns
?DateTimeImmutable
 The deletion timestamp, or null if the store is not deleted

### getId


```php
public function getId(): string
```

Get the unique identifier of the store. Returns the system-generated unique identifier for the store. This ID is used in all API operations to reference this specific store.


#### Returns
string
 The unique store identifier

### getName


```php
public function getName(): string
```

Get the human-readable name of the store. Returns the descriptive name that was assigned to the store during creation or last update. This name is used for identification and administrative purposes.


#### Returns
string
 The descriptive name of the store

### getStore


```php
public function getStore(): OpenFGA\Models\StoreInterface
```

Get the complete store object. Returns the full store object containing all store metadata and configuration. This provides access to the complete store data structure including any additional properties beyond the individual accessor methods.


#### Returns
OpenFGA\Models\StoreInterface
 The complete store object

### getUpdatedAt


```php
public function getUpdatedAt(): DateTimeImmutable
```

Get the timestamp when the store was last updated. Returns the timestamp of the most recent modification to the store&#039;s metadata or configuration. This is updated whenever store properties are changed.


#### Returns
DateTimeImmutable
 The last update timestamp of the store

### schema

*<small>Implements Responses\GetStoreResponseInterface</small>*  

```php
public function schema(): SchemaInterface
```

Get the schema definition for this response. Returns the schema that defines the structure and validation rules for store retrieval response data, ensuring consistent parsing and validation of API responses.


#### Returns
SchemaInterface
 The schema definition for response validation

