# ListUsersRequest

Request for listing users who have a specific relationship with an object. This request finds all users (or usersets) that have the specified relationship with a given object, filtered by user type. It&#039;s useful for building access management interfaces, member lists, and permission auditing tools.

## Namespace

`OpenFGA\Requests`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Requests/ListUsersRequest.php)

## Implements

* [`ListUsersRequestInterface`](ListUsersRequestInterface.md)
* [`RequestInterface`](RequestInterface.md)

## Related Classes

* [ListUsersResponse](Responses/ListUsersResponse.md) (response)
* [ListUsersRequestInterface](Requests/ListUsersRequestInterface.md) (interface)

## Methods

#### getConsistency

```php
public function getConsistency(): ?OpenFGA\Models\Enums\Consistency

```

Get the read consistency level for the list operation. Determines the consistency guarantees for reading authorization data during the user listing operation. This allows you to balance between read performance and data freshness based on your application&#039;s requirements.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/ListUsersRequest.php#L81)

#### Returns

[`Consistency`](Models/Enums/Consistency.md) &#124; `null` — The consistency level for the operation, or null to use the default consistency setting

#### getContext

```php
public function getContext(): ?object

```

Get additional context data for conditional evaluation. Provides contextual information that can be used in conditional expressions within the authorization model. This enables dynamic permission evaluation based on runtime data such as time-based access, location restrictions, or resource attributes when determining user access.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/ListUsersRequest.php#L90)

#### Returns

`object` &#124; `null` — The context object containing additional data for evaluation, or null if no context is provided

#### getContextualTuples

```php
public function getContextualTuples(): ?OpenFGA\Models\Collections\TupleKeysInterface

```

Get additional tuples to consider during the list operation. Returns a collection of temporary relationship tuples that are added to the authorization data during evaluation. This allows you to test access scenarios with hypothetical or pending relationship changes without permanently modifying the store.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/ListUsersRequest.php#L99)

#### Returns

[`TupleKeysInterface`](Models/Collections/TupleKeysInterface.md) &#124; `null` — Additional relationship tuples for evaluation, or null if none provided

#### getModel

```php
public function getModel(): string

```

Get the authorization model ID to use for the list operation. Specifies which version of the authorization model should be used when evaluating user access. Using a specific model ID ensures consistent results even when the model is being updated.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/ListUsersRequest.php#L108)

#### Returns

`string` — The authorization model ID for evaluating user relationships

#### getObject

```php
public function getObject(): string

```

Get the object to list users for. Specifies the target object for which users will be listed. This identifies the specific resource, document, or entity for which you want to know which users have the specified relationship.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/ListUsersRequest.php#L117)

#### Returns

`string` — The object identifier to list users for

#### getRelation

```php
public function getRelation(): string

```

Get the relation to check for user access. Specifies the relationship type to evaluate when determining which users have access to the object. For example, &quot;owner&quot;, &quot;editor&quot;, &quot;viewer&quot;, or &quot;member&quot;. This defines what type of permission or relationship is being queried.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/ListUsersRequest.php#L126)

#### Returns

`string` — The relation name to check for user access

#### getRequest

```php
public function getRequest(Psr\Http\Message\StreamFactoryInterface $streamFactory): OpenFGA\Network\RequestContext

```

Build a request context for HTTP execution. Transforms the request object into a standardized HTTP request context that can be executed by the OpenFGA HTTP client. This method handles all aspects of request preparation including parameter serialization, URL construction, header configuration, and body stream creation. The method validates that all required parameters are present and properly formatted, serializes complex objects to JSON, constructs the appropriate API endpoint URL, and creates the necessary HTTP message body streams.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/ListUsersRequest.php#L137)

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

Get the store ID containing the authorization data. Identifies which OpenFGA store contains the relationship tuples and configuration to use for the list operation. All evaluation will be performed within the context of this specific store.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/ListUsersRequest.php#L171)

#### Returns

`string` — The store ID containing the authorization data

#### getUserFilters

```php
public function getUserFilters(): OpenFGA\Models\Collections\UserTypeFiltersInterface

```

Get the user type filters to apply to results. Returns a collection of filters that control which types of users are included in the results. This allows you to narrow the scope of the query to specific user types, such as individual users, groups, or service accounts, based on your application&#039;s needs. User filters help optimize performance and focus results by excluding user types that are not relevant to the current operation.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/ListUsersRequest.php#L180)

#### Returns

[`UserTypeFiltersInterface`](Models/Collections/UserTypeFiltersInterface.md) — Collection of user type filters to apply to the results
