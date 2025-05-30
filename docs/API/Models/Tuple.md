# Tuple


## Namespace
`OpenFGA\Models`

## Implements
* [TupleInterface](Models/TupleInterface.md)
* JsonSerializable
* [ModelInterface](Models/ModelInterface.md)

## Constants
| Name | Value | Description |
|------|-------|-------------|
| `OPENAPI_MODEL` | `&#039;Tuple&#039;` |  |


## Methods
### getKey


```php
public function getKey(): OpenFGA\Models\TupleKeyInterface
```

Get the tuple key that identifies the relationship. The tuple key contains the essential components that define a relationship within the OpenFGA authorization system. It includes the user (subject), relation (permission type), object (resource), and optional condition that together uniquely identify this specific authorization relationship. The tuple key serves as the primary identifier for relationship operations and is used in authorization queries to match against permission requests. All authorization decisions ultimately trace back to evaluating these relationship keys against the authorization model.


#### Returns
[TupleKeyInterface](Models/TupleKeyInterface.md)
 The tuple key defining this relationship with user, relation, object, and optional condition

### getTimestamp


```php
public function getTimestamp(): DateTimeImmutable
```

Get the timestamp when this tuple was created or last modified. Timestamps provide essential audit information for relationship tracking, enabling debugging, compliance reporting, and temporal analysis of authorization changes. The timestamp is set by the OpenFGA service when the tuple is written and reflects the precise moment the relationship was established or updated. These timestamps are particularly valuable for: - Audit trails and compliance reporting - Debugging authorization issues - Understanding the evolution of permissions over time - Implementing time-based access controls


#### Returns
DateTimeImmutable
 The creation or last modification timestamp in UTC timezone

### jsonSerialize


```php
public function jsonSerialize(): array
```

Serialize the tuple for JSON encoding. This method prepares the tuple data for API communication with the OpenFGA service, converting the tuple key and timestamp into the format expected by the OpenFGA API. The tuple key is serialized to include all relationship components (user, relation, object, and optional condition), while the timestamp is formatted as an RFC3339 string in UTC timezone. The resulting structure matches the OpenFGA API specification for tuple objects, ensuring seamless integration with write operations, read queries, and other tuple-related API endpoints.


#### Returns
array

### schema

*<small>Implements Models\TupleInterface</small>*  

```php
public function schema(): SchemaInterface
```

Get the schema definition for this model. This method returns the schema that defines the structure, validation rules, and serialization behavior for this model class. The schema is used for data validation, transformation, and ensuring consistency across API operations with the OpenFGA service. Each model&#039;s schema defines: - Required and optional properties - Data types and format constraints - Nested object relationships - Validation rules and business logic constraints The schema system enables the SDK to automatically validate incoming data, transform between different representations, and ensure compliance with the OpenFGA API specification.


#### Returns
SchemaInterface
 The schema definition containing validation rules and property specifications for this model

