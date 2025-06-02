# TupleKeyInterface

Represents a tuple key that defines the components of a relationship in OpenFGA. Tuple keys are the core data structure that defines relationships in the OpenFGA authorization system. They specify the essential components that together describe an authorization relationship: who (user), what (relation), and where (object), with optional conditional logic (condition). The tuple key structure follows the pattern: - **User**: The subject of the relationship (who has the permission) - **Relation**: The type of permission or relationship being defined - **Object**: The resource or entity the permission applies to - **Condition**: Optional runtime constraints that must be satisfied Examples of tuple keys: - `user:alice` has `editor` relation to `document:readme` - `group:engineering` has `member` relation to `user:bob` - `user:contractor` has `read` relation to `file:confidential` when `time_constraint` is met Tuple keys are used throughout OpenFGA operations: - Writing relationships (creating authorization facts) - Reading relationships (querying existing permissions) - Authorization checks (evaluating access requests) - Relationship expansion (understanding permission inheritance) The flexible tuple key design enables OpenFGA to represent complex authorization patterns while maintaining efficient query performance and clear relationship semantics.

## Namespace
`OpenFGA\Models`

## Source
[View source code](https://github.com/evansims/openfga-php/blob/main/src/Models/TupleKeyInterface.php)

## Implements
* [ModelInterface](ModelInterface.md)
* JsonSerializable

## Related Classes
* [TupleKey](Models/TupleKey.md) (implementation)



## Methods

                                                                                    
### List Operations
#### getCondition


```php
public function getCondition(): ConditionInterface|null
```

Get the condition that constrains this relationship. Conditions enable dynamic authorization by allowing relationships to be conditional based on runtime context, such as time of day, resource attributes, or other factors. When a condition is present, the relationship is only valid when the condition evaluates to true.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/TupleKeyInterface.php#L52)


#### Returns
ConditionInterface&#124;null
 The condition that must be satisfied for this relationship to be valid, or null for an unconditional relationship

#### getObject


```php
public function getObject(): string|null
```

Get the object in this relationship tuple. The object represents the resource or entity that the permission or relationship applies to. For example, in &quot;user:alice can view document:readme&quot;, the object would be &quot;document:readme&quot;. Objects are typically formatted as &quot;type:id&quot; where type describes the kind of resource.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/TupleKeyInterface.php#L63)


#### Returns
string&#124;null
 The object identifier, or null if not specified

#### getRelation


```php
public function getRelation(): string|null
```

Get the relation that defines the type of relationship. The relation describes what kind of permission or relationship exists between the user and object. For example, common relations include &quot;owner&quot;, &quot;viewer&quot;, &quot;editor&quot;, &quot;can_read&quot;, &quot;can_write&quot;. Relations are defined in your authorization model and determine what actions are permitted.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/TupleKeyInterface.php#L74)


#### Returns
string&#124;null
 The relation name defining the type of relationship, or null if not specified

#### getUser


```php
public function getUser(): string|null
```

Get the user (subject) in this relationship tuple. The user represents the entity that has the relationship to the object. This can be an individual user, a group, a role, or any other subject defined in your authorization model. For example, in &quot;user:alice can view document:readme&quot;, the user would be &quot;user:alice&quot;.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/TupleKeyInterface.php#L85)


#### Returns
string&#124;null
 The user identifier, or null if not specified

### Other
#### jsonSerialize


```php
public function jsonSerialize(): array<string, mixed>
```

Serialize the tuple key for JSON encoding. This method prepares the tuple key data for API requests or storage, ensuring all components (user, relation, object, and optional condition) are properly formatted according to the OpenFGA API specification.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/TupleKeyInterface.php#L97)


#### Returns
array&lt;string, mixed&gt;
 The serialized tuple key data ready for JSON encoding

