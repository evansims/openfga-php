# ListObjectsRequest

Request for listing objects that a user has a specific relationship with. This request finds all objects of a given type where the specified user has the requested relationship. It&#039;s useful for building resource lists, dashboards, or any interface that shows what a user can access.

## Namespace
`OpenFGA\Requests`

## Source
[View source code](https://github.com/evansims/openfga-php/blob/main/src/Requests/ListObjectsRequest.php)

## Implements
* [ListObjectsRequestInterface](ListObjectsRequestInterface.md)
* [RequestInterface](RequestInterface.md)



## Methods
### getConsistency


```php
public function getConsistency(): ?OpenFGA\Models\Enums\Consistency
```

Get the read consistency level for the list operation. Determines the consistency guarantees for reading authorization data during the list operation. This allows you to balance between read performance and data freshness based on your application&#039;s requirements.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/ListObjectsRequest.php#L82)


#### Returns
?OpenFGA\Models\Enums\Consistency
 The consistency level for the operation, or null to use the default consistency setting

### getContext


```php
public function getContext(): ?object
```

Get additional context data for conditional evaluation. Provides contextual information that can be used in conditional expressions within the authorization model. This enables dynamic permission evaluation based on runtime data such as time-based access, location restrictions, or resource attributes.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/ListObjectsRequest.php#L91)


#### Returns
?object
 The context object containing additional data for evaluation, or null if no context is provided

### getContextualTuples


```php
public function getContextualTuples(): ?OpenFGA\Models\Collections\TupleKeysInterface
```

Get additional tuples to consider during the list operation. Returns a collection of temporary relationship tuples that are added to the authorization data during evaluation. This allows you to test access scenarios with hypothetical or pending relationship changes without permanently modifying the store.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/ListObjectsRequest.php#L100)


#### Returns
?OpenFGA\Models\Collections\TupleKeysInterface
 Additional relationship tuples for evaluation, or null if none provided

### getModel


```php
public function getModel(): ?string
```

Get the authorization model ID to use for the list operation. Specifies which version of the authorization model should be used when evaluating object access. Using a specific model ID ensures consistent results even when the model is being updated. If not specified, the latest model version will be used.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/ListObjectsRequest.php#L109)


#### Returns
?string
 The authorization model ID for evaluation, or null to use the latest model version

### getRelation


```php
public function getRelation(): string
```

Get the relation to check for object access. Specifies the relationship type to evaluate when determining object access. For example, &quot;can_view&quot;, &quot;can_edit&quot;, or &quot;owner&quot;. This defines what type of permission or relationship is being queried.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/ListObjectsRequest.php#L118)


#### Returns
string
 The relation name to check for object access

### getRequest


```php
public function getRequest(Psr\Http\Message\StreamFactoryInterface $streamFactory): OpenFGA\Network\RequestContext
```

Build a request context for HTTP execution. Transforms the request object into a standardized HTTP request context that can be executed by the OpenFGA HTTP client. This method handles all aspects of request preparation including parameter serialization, URL construction, header configuration, and body stream creation. The method validates that all required parameters are present and properly formatted, serializes complex objects to JSON, constructs the appropriate API endpoint URL, and creates the necessary HTTP message body streams.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/ListObjectsRequest.php#L129)

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$streamFactory` | Psr\Http\Message\StreamFactoryInterface | PSR-7 stream factory for creating request body streams from serialized data |

#### Returns
OpenFGA\Network\RequestContext
 The prepared request context containing HTTP method, URL, headers, and body ready for execution

### getStore


```php
public function getStore(): string
```

Get the store ID containing the authorization data. Identifies which OpenFGA store contains the relationship tuples and configuration to use for the list operation. All evaluation will be performed within the context of this specific store.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/ListObjectsRequest.php#L156)


#### Returns
string
 The store ID containing the authorization data

### getType


```php
public function getType(): string
```

Get the object type to filter results by. Specifies the type of objects to include in the results. Only objects of this type will be considered when determining what the user can access. For example, &quot;document&quot;, &quot;folder&quot;, or &quot;repository&quot;.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/ListObjectsRequest.php#L165)


#### Returns
string
 The object type to filter results by

### getUser


```php
public function getUser(): string
```

Get the user to check object access for. Identifies the user for whom object access is being evaluated. This can be a direct user identifier or a userset expression. The operation will return all objects of the specified type that this user can access through the specified relation.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/ListObjectsRequest.php#L174)


#### Returns
string
 The user identifier or userset to check object access for

