# CheckRequest

Request for performing authorization checks in OpenFGA. This request determines whether a user has a specific relationship with an object based on the configured authorization model and relationship tuples. It&#039;s the core operation for making authorization decisions in your application. The check operation supports contextual tuples, custom contexts, and tracing to provide comprehensive authorization decisions with detailed debugging information.

## Namespace

`OpenFGA\Requests`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Requests/CheckRequest.php)

## Implements

* [`CheckRequestInterface`](CheckRequestInterface.md)
* [`RequestInterface`](RequestInterface.md)

## Related Classes

* [CheckResponse](Responses/CheckResponse.md) (response)
* [CheckRequestInterface](Requests/CheckRequestInterface.md) (interface)

## Methods

#### getAuthorizationModel

```php
public function getAuthorizationModel(): string

```

Get the authorization model ID to use for the check. This specifies which version of the authorization model should be used when evaluating the permission check. Using a specific model ID ensures consistent results.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/CheckRequest.php#L73)

#### Returns

`string` — The authorization model ID for permission evaluation

#### getConsistency

```php
public function getConsistency(): ?OpenFGA\Models\Enums\Consistency

```

Get the consistency level for the check operation. This determines the read consistency requirement for the check operation, allowing you to balance between read performance and data consistency based on your application&#039;s needs.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/CheckRequest.php#L82)

#### Returns

[`Consistency`](Models/Enums/Consistency.md) &#124; `null` — The consistency level, or null to use the default consistency setting

#### getContext

```php
public function getContext(): ?object

```

Get additional context data for conditional evaluation. This provides contextual information that can be used in conditional expressions within the authorization model, enabling dynamic permission evaluation based on runtime data.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/CheckRequest.php#L91)

#### Returns

`object` &#124; `null` — The context object containing additional data for evaluation, or null if no context is provided

#### getContextualTuples

```php
public function getContextualTuples(): ?OpenFGA\Models\Collections\TupleKeysInterface

```

Get additional tuples to consider during the check. These contextual tuples are temporarily added to the authorization data during evaluation, allowing you to test permission scenarios with hypothetical or pending relationship changes.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/CheckRequest.php#L100)

#### Returns

[`TupleKeysInterface`](Models/Collections/TupleKeysInterface.md) &#124; `null` — Additional relationship tuples for evaluation, or null if none provided

#### getRequest

```php
public function getRequest(Psr\Http\Message\StreamFactoryInterface $streamFactory): OpenFGA\Network\RequestContext

```

Build a request context for HTTP execution. Transforms the request object into a standardized HTTP request context that can be executed by the OpenFGA HTTP client. This method handles all aspects of request preparation including parameter serialization, URL construction, header configuration, and body stream creation. The method validates that all required parameters are present and properly formatted, serializes complex objects to JSON, constructs the appropriate API endpoint URL, and creates the necessary HTTP message body streams.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/CheckRequest.php#L109)

#### Parameters

| Name             | Type                     | Description                                                                 |
| ---------------- | ------------------------ | --------------------------------------------------------------------------- |
| `$streamFactory` | `StreamFactoryInterface` | PSR-7 stream factory for creating request body streams from serialized data |

#### Returns

[`RequestContext`](Network/RequestContext.md) — The prepared request context containing HTTP method, URL, headers, and body ready for execution

#### getStore

```php
public function getStore(): string

```

Get the store ID containing the authorization data. This identifies which OpenFGA store contains the relationship tuples and configuration to use for the permission check.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/CheckRequest.php#L135)

#### Returns

`string` — The store ID containing the authorization data

#### getTrace

```php
public function getTrace(): ?bool

```

Get whether to include evaluation trace in the response. When enabled, the response will include detailed information about how the permission decision was reached, which is useful for debugging authorization logic.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/CheckRequest.php#L144)

#### Returns

`bool` &#124; `null` — Whether to include trace information, or null to use the default setting

#### getTupleKey

```php
public function getTupleKey(): OpenFGA\Models\TupleKeyInterface

```

Get the relationship tuple to check for permission. This defines the specific relationship (user, object, relation) to evaluate for authorization. For example, checking if &quot;user:alice&quot; has &quot;can_view&quot; permission on &quot;document:readme.&quot;

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/CheckRequest.php#L153)

#### Returns

[`TupleKeyInterface`](Models/TupleKeyInterface.md) — The relationship tuple specifying what permission to check
