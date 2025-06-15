# ExpandRequest

Request for expanding a relationship to show all users who have that relationship. This request returns the complete set of users and usersets that have the specified relationship with an object. It&#039;s useful for debugging authorization models, auditing permissions, and understanding the complete authorization tree.

<details>
<summary><strong>Table of Contents</strong></summary>

- [Namespace](#namespace)
- [Source](#source)
- [Implements](#implements)
- [Related Classes](#related-classes)
- [Methods](#methods)

- [`getConsistency()`](#getconsistency)
  - [`getContextualTuples()`](#getcontextualtuples)
  - [`getModel()`](#getmodel)
  - [`getRequest()`](#getrequest)
  - [`getStore()`](#getstore)
  - [`getTupleKey()`](#gettuplekey)

</details>

## Namespace

`OpenFGA\Requests`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Requests/ExpandRequest.php)

## Implements

- [`ExpandRequestInterface`](ExpandRequestInterface.md)
- [`RequestInterface`](RequestInterface.md)

## Related Classes

- [ExpandResponse](Responses/ExpandResponse.md) (response)
- [ExpandRequestInterface](Requests/ExpandRequestInterface.md) (interface)

## Methods

### getConsistency

```php
public function getConsistency(): ?OpenFGA\Models\Enums\Consistency

```

Get the read consistency level for the expand operation. Determines the consistency guarantees for reading authorization data during the expansion. This allows you to balance between read performance and data freshness based on your application&#039;s requirements.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/ExpandRequest.php#L63)

#### Returns

[`Consistency`](Models/Enums/Consistency.md) &#124; `null` — The consistency level for the operation, or null to use the default consistency setting

### getContextualTuples

```php
public function getContextualTuples(): ?OpenFGA\Models\Collections\TupleKeysInterface

```

Get additional tuples to consider during the expansion. Returns a collection of temporary relationship tuples that are added to the authorization data during evaluation. This allows you to test how hypothetical or pending relationship changes would affect the authorization graph without permanently modifying the store.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/ExpandRequest.php#L72)

#### Returns

[`TupleKeysInterface`](Models/Collections/TupleKeysInterface.md) &#124; `null` — Additional relationship tuples for evaluation, or null if none provided

### getModel

```php
public function getModel(): ?string

```

Get the authorization model ID to use for the expansion. Specifies which version of the authorization model should be used when expanding the relationship graph. Using a specific model ID ensures consistent results even when the model is being updated. If not specified, the latest model version will be used.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/ExpandRequest.php#L81)

#### Returns

`string` &#124; `null` — The authorization model ID for evaluation, or null to use the latest model version

### getRequest

```php
public function getRequest(Psr\Http\Message\StreamFactoryInterface $streamFactory): OpenFGA\Network\RequestContext

```

Build a request context for HTTP execution. Transforms the request object into a standardized HTTP request context that can be executed by the OpenFGA HTTP client. This method handles all aspects of request preparation including parameter serialization, URL construction, header configuration, and body stream creation. The method validates that all required parameters are present and properly formatted, serializes complex objects to JSON, constructs the appropriate API endpoint URL, and creates the necessary HTTP message body streams.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/ExpandRequest.php#L92)

#### Parameters

| Name             | Type                     | Description                                                                 |
| ---------------- | ------------------------ | --------------------------------------------------------------------------- |
| `$streamFactory` | `StreamFactoryInterface` | PSR-7 stream factory for creating request body streams from serialized data |

#### Returns

[`RequestContext`](Network/RequestContext.md) — The prepared request context containing HTTP method, URL, headers, and body ready for execution

### getStore

```php
public function getStore(): string

```

Get the store ID containing the authorization data. Identifies which OpenFGA store contains the relationship tuples and configuration to use for the expansion. All evaluation will be performed within the context of this specific store.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/ExpandRequest.php#L116)

#### Returns

`string` — The store ID containing the authorization data

### getTupleKey

```php
public function getTupleKey(): OpenFGA\Models\TupleKeyInterface

```

Get the relationship tuple to expand. Specifies the starting point for the relationship expansion. This defines the object and relation for which the authorization graph should be expanded. The expansion will show all users and user sets that have the specified relation to the specified object.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/ExpandRequest.php#L125)

#### Returns

[`TupleKeyInterface`](Models/TupleKeyInterface.md) — The relationship tuple specifying what to expand (object and relation)
