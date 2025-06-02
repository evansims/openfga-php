# CheckRequestInterface

Interface for authorization check request specifications. This interface defines the contract for creating authorization check requests that determine whether a user has a specific relationship with an object. It&#039;s the core interface for implementing permission verification in applications.

## Namespace
`OpenFGA\Requests`

## Source
[View source code](https://github.com/evansims/openfga-php/blob/main/src/Requests/CheckRequestInterface.php)

## Implements
* [RequestInterface](RequestInterface.md)



## Methods
### getAuthorizationModel


```php
public function getAuthorizationModel(): string
```

Get the authorization model ID to use for the check. This specifies which version of the authorization model should be used when evaluating the permission check. Using a specific model ID ensures consistent results.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/CheckRequestInterface.php#L30)


#### Returns
string
 The authorization model ID for permission evaluation

### getConsistency


```php
public function getConsistency(): Consistency|null
```

Get the consistency level for the check operation. This determines the read consistency requirement for the check operation, allowing you to balance between read performance and data consistency based on your application&#039;s needs.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/CheckRequestInterface.php#L40)


#### Returns
Consistency&#124;null
 The consistency level, or null to use the default consistency setting

### getContext


```php
public function getContext(): object|null
```

Get additional context data for conditional evaluation. This provides contextual information that can be used in conditional expressions within the authorization model, enabling dynamic permission evaluation based on runtime data.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/CheckRequestInterface.php#L50)


#### Returns
object&#124;null
 The context object containing additional data for evaluation, or null if no context is provided

### getContextualTuples


```php
public function getContextualTuples(): TupleKeysInterface<TupleKeyInterface>|null
```

Get additional tuples to consider during the check. These contextual tuples are temporarily added to the authorization data during evaluation, allowing you to test permission scenarios with hypothetical or pending relationship changes.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/CheckRequestInterface.php#L60)


#### Returns
TupleKeysInterface&lt;TupleKeyInterface&gt;&#124;null
 Additional relationship tuples for evaluation, or null if none provided

### getRequest


```php
public function getRequest(StreamFactoryInterface $streamFactory): RequestContext
```

Build a request context for HTTP execution. Transforms the request object into a standardized HTTP request context that can be executed by the OpenFGA HTTP client. This method handles all aspects of request preparation including parameter serialization, URL construction, header configuration, and body stream creation. The method validates that all required parameters are present and properly formatted, serializes complex objects to JSON, constructs the appropriate API endpoint URL, and creates the necessary HTTP message body streams.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/RequestInterface.php#L57)

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

Get the store ID containing the authorization data. This identifies which OpenFGA store contains the relationship tuples and configuration to use for the permission check.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/CheckRequestInterface.php#L70)


#### Returns
string
 The store ID containing the authorization data

### getTrace


```php
public function getTrace(): bool|null
```

Get whether to include evaluation trace in the response. When enabled, the response will include detailed information about how the permission decision was reached, which is useful for debugging authorization logic.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/CheckRequestInterface.php#L80)


#### Returns
bool&#124;null
 Whether to include trace information, or null to use the default setting

### getTupleKey


```php
public function getTupleKey(): TupleKeyInterface
```

Get the relationship tuple to check for permission. This defines the specific relationship (user, object, relation) to evaluate for authorization. For example, checking if &quot;user:alice&quot; has &quot;can_view&quot; permission on &quot;document:readme&quot;.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/CheckRequestInterface.php#L90)


#### Returns
TupleKeyInterface
 The relationship tuple specifying what permission to check

