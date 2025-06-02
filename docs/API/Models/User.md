# User

Represents a user or user specification in authorization contexts. A User can represent different types of entities in your authorization system: a specific user object, a userset (group of users), a wildcard (all users of a type), or a complex difference operation between user groups. This flexible model allows OpenFGA to handle various user identification patterns, from simple &quot;user:alice&quot; references to complex computed user groups based on relationships and rules.

## Namespace
`OpenFGA\Models`

## Implements
* [UserInterface](Models/UserInterface.md)
* JsonSerializable
* [ModelInterface](Models/ModelInterface.md)

## Constants
| Name | Value | Description |
|------|-------|-------------|
| `OPENAPI_MODEL` | `&#039;User&#039;` |  |


## Methods
### getDifference


```php
public function getDifference(): ?OpenFGA\Models\DifferenceV1Interface
```

Get the difference operation for this user. Difference operations enable sophisticated access control by subtracting one set of users from another, creating complex user definitions that include some users while explicitly excluding others. For example, &quot;all editors except contractors&quot; or &quot;all organization members except suspended users&quot;. This pattern is particularly useful for: - Implementing exception-based access policies - Temporary access restrictions without modifying base permissions - Complex organizational hierarchies with exclusion rules - Compliance scenarios requiring explicit user exclusions When a difference operation is present, the authorization system evaluates both the base user set and the excluded user set, granting access only to users who match the base set but not the exclusion set.


#### Returns
?[DifferenceV1Interface](Models/DifferenceV1Interface.md)
 The difference operation defining included and excluded user sets, or null if this is not a difference-based user

### getObject


```php
public function getObject(): ?OpenFGA\Models\UserObjectInterface|string|null
```

Get the user object representation. User objects represent direct, concrete user identifiers within the authorization system. These can be structured objects with explicit type and ID properties, or simple string identifiers following the &quot;type:id&quot; convention for backward compatibility and convenience. Examples of user object representations: - Structured: UserObject with type=&quot;user&quot; and id=&quot;alice&quot; - String format: &quot;user:alice&quot;, &quot;service:backup-agent&quot;, &quot;bot:notification-service&quot; Direct user objects are the most straightforward way to assign permissions to specific, known entities in your system. They provide clear, unambiguous identification and are efficient for authorization queries.


#### Returns
?[UserObjectInterface](Models/UserObjectInterface.md) | string | null
 The direct user identifier as a structured object or string, or null if this is not a direct user reference

### getUserset


```php
public function getUserset(): ?OpenFGA\Models\UsersetUserInterface
```

Get the userset reference for this user. Usersets define dynamic user groups through relationships to other objects, enabling permissions that automatically adapt as relationships change in your system. A userset specifies users indirectly by describing a relationship pattern, such as &quot;all editors of document:readme&quot; or &quot;all members of group:engineering&quot;. Usersets are powerful because they: - Automatically include/exclude users as relationships change - Reduce the need for explicit permission management - Enable permission inheritance and delegation patterns - Support complex organizational structures and role hierarchies When authorization checks encounter usersets, OpenFGA recursively evaluates the referenced relationships to determine the actual set of users that have access through this indirect relationship.


#### Returns
?[UsersetUserInterface](Models/UsersetUserInterface.md)
 The userset definition specifying users through relationships, or null if this is not a userset-based user

### getWildcard


```php
public function getWildcard(): ?OpenFGA\Models\TypedWildcardInterface
```

Get the wildcard definition for this user. Wildcards represent all users of a specific type, enabling broad, type-based permissions without enumerating individual users. This pattern is particularly useful for organization-wide permissions, public access scenarios, or when you want to grant access to all users matching certain criteria. Common wildcard use cases: - &quot;All employees can access the company directory&quot; - &quot;All authenticated users can read public documents&quot; - &quot;All service accounts can write to audit logs&quot; - &quot;All users in the organization can view the org chart&quot; Wildcards are efficient for authorization because they don&#039;t require maintaining explicit relationships for every user, while still providing type-safe access control based on user categorization.


#### Returns
?[TypedWildcardInterface](Models/TypedWildcardInterface.md)
 The wildcard definition specifying the user type, or null if this is not a wildcard user

### jsonSerialize


```php
public function jsonSerialize(): array<string, mixed>
```

Serialize the user for JSON encoding. This method prepares the user data for API communication with the OpenFGA service, converting the user representation into the format expected by the OpenFGA API. The serialization handles all user types (direct objects, usersets, wildcards, and difference operations) and ensures the resulting structure matches the OpenFGA API specification. Only the appropriate user type fields are included in the output: - Direct users include object field with type:id or structured object - Usersets include userset field with type, id, and relation - Wildcards include wildcard field with type specification - Difference operations include difference field with base and subtract sets


#### Returns
array&lt;string, mixed&gt;
 User data formatted for JSON encoding with the appropriate user type representation

### schema

*<small>Implements Models\UserInterface</small>*  

```php
public function schema(): SchemaInterface
```

Get the schema definition for this model. This method returns the schema that defines the structure, validation rules, and serialization behavior for this model class. The schema is used for data validation, transformation, and ensuring consistency across API operations with the OpenFGA service. Each model&#039;s schema defines: - Required and optional properties - Data types and format constraints - Nested object relationships - Validation rules and business logic constraints The schema system enables the SDK to automatically validate incoming data, transform between different representations, and ensure compliance with the OpenFGA API specification.


#### Returns
SchemaInterface
 The schema definition containing validation rules and property specifications for this model

