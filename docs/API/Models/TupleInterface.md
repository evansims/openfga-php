# TupleInterface

Represents a relationship tuple in the OpenFGA authorization system. Tuples are the fundamental building blocks of OpenFGA that define actual relationships between users, objects, and relations. They represent concrete facts about your system, such as &quot;user:alice has editor relation to document:doc1&quot; or &quot;group:engineering has member relation to user:bob.&quot; These relationships form the data foundation that OpenFGA uses during authorization checks to determine access permissions and evaluate complex authorization policies. Each tuple consists of: - A tuple key that defines the relationship (user, relation, object, optional condition) - A timestamp that tracks when the relationship was established or last modified Tuples can be unconditional (always valid) or conditional (valid only when specific runtime conditions are met). Conditional tuples enable dynamic authorization based on context such as time of day, resource attributes, or environmental factors. The tuple system supports OpenFGA&#039;s core authorization model by providing the actual relationship data that authorization queries evaluate against. Tuples are managed through write operations and queried during check operations.

## Namespace

`OpenFGA\Models`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Models/TupleInterface.php)

## Implements

* [`ModelInterface`](ModelInterface.md)
* `JsonSerializable`

## Related Classes

* [Tuple](Models/Tuple.md) (implementation)

## Methods

### List Operations

#### getKey

```php
public function getKey(): TupleKeyInterface

```

Get the tuple key that identifies the relationship. The tuple key contains the essential components that define a relationship within the OpenFGA authorization system. It includes the user (subject), relation (permission type), object (resource), and optional condition that together uniquely identify this specific authorization relationship. The tuple key serves as the primary identifier for relationship operations and is used in authorization queries to match against permission requests. All authorization decisions ultimately trace back to evaluating these relationship keys against the authorization model.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/TupleInterface.php#L54)

#### Returns

[`TupleKeyInterface`](TupleKeyInterface.md) — The tuple key defining this relationship with user, relation, object, and optional condition

#### getTimestamp

```php
public function getTimestamp(): DateTimeImmutable

```

Get the timestamp when this tuple was created or last modified. Timestamps provide essential audit information for relationship tracking, enabling debugging, compliance reporting, and temporal analysis of authorization changes. The timestamp is set by the OpenFGA service when the tuple is written and reflects the precise moment the relationship was established or updated. These timestamps are particularly valuable for: - Audit trails and compliance reporting - Debugging authorization issues - Understanding the evolution of permissions over time - Implementing time-based access controls

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/TupleInterface.php#L73)

#### Returns

`DateTimeImmutable` — The creation or last modification timestamp in UTC timezone

### Other

#### jsonSerialize

```php
public function jsonSerialize(): array<string, mixed>

```

Serialize the tuple for JSON encoding. This method prepares the tuple data for API communication with the OpenFGA service, converting the tuple key and timestamp into the format expected by the OpenFGA API. The tuple key is serialized to include all relationship components (user, relation, object, and optional condition), while the timestamp is formatted as an RFC3339 string in UTC timezone. The resulting structure matches the OpenFGA API specification for tuple objects, ensuring seamless integration with write operations, read queries, and other tuple-related API endpoints.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/TupleInterface.php#L91)

#### Returns

`array&lt;`string`, `mixed`&gt;` — Tuple data formatted for JSON encoding with API-compatible structure
