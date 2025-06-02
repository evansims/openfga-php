# ListUsersRequestInterface

Interface for listing users who have a specific relation to an object. This interface defines the contract for requests that query which users have a specific relationship to a given object. This is similar to the expand operation but focuses specifically on returning the users rather than the complete relationship graph structure. List users operations are particularly useful for: - Building user interfaces that show who has access to a resource - Implementing sharing and collaboration features - Auditing and compliance reporting for access control - Sending notifications to users with specific permissions - Managing team membership and role assignments The operation supports: - Filtering by user types to control result scope - Contextual evaluation with additional runtime data - Temporary relationship tuples for scenario testing - Configurable read consistency levels for performance optimization - Authorization model versioning for consistent results This provides the inverse perspective to list objects - instead of asking &quot;what can this user access?&quot;, it asks &quot;who can access this object?&quot;.

## Namespace
`OpenFGA\Requests`

## Implements
* [RequestInterface](RequestInterface.md)



## Methods
### getConsistency


```php
public function getConsistency(): Consistency|null
```

Get the read consistency level for the list operation. Determines the consistency guarantees for reading authorization data during the user listing operation. This allows you to balance between read performance and data freshness based on your application&#039;s requirements.


#### Returns
Consistency&#124;null
 The consistency level for the operation, or null to use the default consistency setting

### getContext


```php
public function getContext(): object|null
```

Get additional context data for conditional evaluation. Provides contextual information that can be used in conditional expressions within the authorization model. This enables dynamic permission evaluation based on runtime data such as time-based access, location restrictions, or resource attributes when determining user access.


#### Returns
object&#124;null
 The context object containing additional data for evaluation, or null if no context is provided

### getContextualTuples


```php
public function getContextualTuples(): TupleKeysInterface<TupleKeyInterface>|null
```

Get additional tuples to consider during the list operation. Returns a collection of temporary relationship tuples that are added to the authorization data during evaluation. This allows you to test access scenarios with hypothetical or pending relationship changes without permanently modifying the store.


#### Returns
TupleKeysInterface&lt;TupleKeyInterface&gt;&#124;null
 Additional relationship tuples for evaluation, or null if none provided

### getModel


```php
public function getModel(): string
```

Get the authorization model ID to use for the list operation. Specifies which version of the authorization model should be used when evaluating user access. Using a specific model ID ensures consistent results even when the model is being updated.


#### Returns
string
 The authorization model ID for evaluating user relationships

### getObject


```php
public function getObject(): string
```

Get the object to list users for. Specifies the target object for which users will be listed. This identifies the specific resource, document, or entity for which you want to know which users have the specified relationship.


#### Returns
string
 The object identifier to list users for

### getRelation


```php
public function getRelation(): string
```

Get the relation to check for user access. Specifies the relationship type to evaluate when determining which users have access to the object. For example, &quot;owner&quot;, &quot;editor&quot;, &quot;viewer&quot;, or &quot;member&quot;. This defines what type of permission or relationship is being queried.


#### Returns
string
 The relation name to check for user access

### getRequest


```php
public function getRequest(StreamFactoryInterface $streamFactory): RequestContext
```

Build a request context for HTTP execution. Transforms the request object into a standardized HTTP request context that can be executed by the OpenFGA HTTP client. This method handles all aspects of request preparation including parameter serialization, URL construction, header configuration, and body stream creation. The method validates that all required parameters are present and properly formatted, serializes complex objects to JSON, constructs the appropriate API endpoint URL, and creates the necessary HTTP message body streams.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$streamFactory` | StreamFactoryInterface | PSR-7 stream factory for creating request body streams from serialized data |

#### Returns
RequestContext
 The prepared request context containing HTTP method, URL, headers, and body ready for execution

### getStore


```php
public function getStore(): string
```

Get the store ID containing the authorization data. Identifies which OpenFGA store contains the relationship tuples and configuration to use for the list operation. All evaluation will be performed within the context of this specific store.


#### Returns
string
 The store ID containing the authorization data

### getUserFilters


```php
public function getUserFilters(): UserTypeFiltersInterface<UserTypeFilterInterface>
```

Get the user type filters to apply to results. Returns a collection of filters that control which types of users are included in the results. This allows you to narrow the scope of the query to specific user types, such as individual users, groups, or service accounts, based on your application&#039;s needs. User filters help optimize performance and focus results by excluding user types that are not relevant to the current operation.


#### Returns
UserTypeFiltersInterface&lt;UserTypeFilterInterface&gt;
 Collection of user type filters to apply to the results

