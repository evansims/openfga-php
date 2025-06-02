# UserInterface

Represents a user in an OpenFGA authorization model. In OpenFGA, users are flexible entities that can represent various types of subjects in authorization relationships. The user concept extends beyond individual people to include groups, roles, services, or any entity that can be granted permissions. OpenFGA supports multiple user representation patterns to accommodate complex authorization scenarios: Direct User Objects**: Simple user identifiers in &quot;type:id&quot; format, such as &quot;user:alice&quot; or &quot;service:backup-agent&quot;. These represent concrete entities with specific identities that can be directly assigned permissions. Usersets**: Dynamic user groups defined through relationships, such as &quot;all editors of document:readme&quot; or &quot;all members of group:engineering&quot;. Usersets enable permissions that automatically adapt as relationships change. Wildcards**: Type-based user groups that match all users of a specific type, such as &quot;all users of type employee&quot;. Wildcards enable broad, organization-wide permissions without enumerating individual users. Difference Operations**: Complex user definitions that include some users while excluding others, such as &quot;all editors except contractors&quot;. This enables fine-grained access control with exception handling. This flexible user model enables OpenFGA to handle sophisticated authorization patterns while maintaining performance and simplicity in common use cases.

## Namespace
`OpenFGA\Models`

## Implements
* [ModelInterface](ModelInterface.md)
* JsonSerializable



## Methods
### getDifference


```php
public function getDifference(): DifferenceV1Interface|null
```

Get the difference operation for this user. Difference operations enable sophisticated access control by subtracting one set of users from another, creating complex user definitions that include some users while explicitly excluding others. For example, &quot;all editors except contractors&quot; or &quot;all organization members except suspended users&quot;. This pattern is particularly useful for: - Implementing exception-based access policies - Temporary access restrictions without modifying base permissions - Complex organizational hierarchies with exclusion rules - Compliance scenarios requiring explicit user exclusions When a difference operation is present, the authorization system evaluates both the base user set and the excluded user set, granting access only to users who match the base set but not the exclusion set.


#### Returns
DifferenceV1Interface&#124;null
 The difference operation defining included and excluded user sets, or null if this is not a difference-based user

### getObject


```php
public function getObject(): string|UserObjectInterface|null
```

Get the user object representation. User objects represent direct, concrete user identifiers within the authorization system. These can be structured objects with explicit type and ID properties, or simple string identifiers following the &quot;type:id&quot; convention for backward compatibility and convenience. Examples of user object representations: - Structured: UserObject with type=&quot;user&quot; and id=&quot;alice&quot; - String format: &quot;user:alice&quot;, &quot;service:backup-agent&quot;, &quot;bot:notification-service&quot; Direct user objects are the most straightforward way to assign permissions to specific, known entities in your system. They provide clear, unambiguous identification and are efficient for authorization queries.


#### Returns
string&#124;UserObjectInterface&#124;null
 The direct user identifier as a structured object or string, or null if this is not a direct user reference

### getUserset


```php
public function getUserset(): UsersetUserInterface|null
```

Get the userset reference for this user. Usersets define dynamic user groups through relationships to other objects, enabling permissions that automatically adapt as relationships change in your system. A userset specifies users indirectly by describing a relationship pattern, such as &quot;all editors of document:readme&quot; or &quot;all members of group:engineering&quot;. Usersets are powerful because they: - Automatically include/exclude users as relationships change - Reduce the need for explicit permission management - Enable permission inheritance and delegation patterns - Support complex organizational structures and role hierarchies When authorization checks encounter usersets, OpenFGA recursively evaluates the referenced relationships to determine the actual set of users that have access through this indirect relationship.


#### Returns
UsersetUserInterface&#124;null
 The userset definition specifying users through relationships, or null if this is not a userset-based user

### getWildcard


```php
public function getWildcard(): TypedWildcardInterface|null
```

Get the wildcard definition for this user. Wildcards represent all users of a specific type, enabling broad, type-based permissions without enumerating individual users. This pattern is particularly useful for organization-wide permissions, public access scenarios, or when you want to grant access to all users matching certain criteria. Common wildcard use cases: - &quot;All employees can access the company directory&quot; - &quot;All authenticated users can read public documents&quot; - &quot;All service accounts can write to audit logs&quot; - &quot;All users in the organization can view the org chart&quot; Wildcards are efficient for authorization because they don&#039;t require maintaining explicit relationships for every user, while still providing type-safe access control based on user categorization.


#### Returns
TypedWildcardInterface&#124;null
 The wildcard definition specifying the user type, or null if this is not a wildcard user

### jsonSerialize


```php
public function jsonSerialize(): array<string, mixed>
```

Serialize the user for JSON encoding. This method prepares the user data for API communication with the OpenFGA service, converting the user representation into the format expected by the OpenFGA API. The serialization handles all user types (direct objects, usersets, wildcards, and difference operations) and ensures the resulting structure matches the OpenFGA API specification. Only the appropriate user type fields are included in the output: - Direct users include object field with type:id or structured object - Usersets include userset field with type, id, and relation - Wildcards include wildcard field with type specification - Difference operations include difference field with base and subtract sets


#### Returns
array&lt;string, mixed&gt;
 User data formatted for JSON encoding with the appropriate user type representation

