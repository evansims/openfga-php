# StreamedListObjectsRequestInterface

Request interface for streaming objects that a user has a specific relationship with. This request finds all objects of a given type where the specified user has the requested relationship, but returns them as a stream for processing large result sets efficiently. It&#039;s useful for building resource lists, dashboards, or any interface that shows what a user can access when dealing with large datasets.

<details>
<summary><strong>Table of Contents</strong></summary>

- [Namespace](#namespace)
- [Source](#source)
- [Implements](#implements)
- [Related Classes](#related-classes)
- [Methods](#methods)

- [`getConsistency()`](#getconsistency)
  - [`getContext()`](#getcontext)
  - [`getContextualTuples()`](#getcontextualtuples)
  - [`getModel()`](#getmodel)
  - [`getRelation()`](#getrelation)
  - [`getRequest()`](#getrequest)
  - [`getStore()`](#getstore)
  - [`getType()`](#gettype)
  - [`getUser()`](#getuser)

</details>

## Namespace

`OpenFGA\Requests`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Requests/StreamedListObjectsRequestInterface.php)

## Implements

- [`RequestInterface`](RequestInterface.md)

## Related Classes

- [StreamedListObjectsResponseInterface](Responses/StreamedListObjectsResponseInterface.md) (response)
- [StreamedListObjectsRequest](Requests/StreamedListObjectsRequest.md) (implementation)

## Methods

### getConsistency

```php
public function getConsistency(): Consistency|null

```

Get the consistency requirement for this request.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/StreamedListObjectsRequestInterface.php#L27)

#### Returns

[`Consistency`](Models/Enums/Consistency.md) &#124; `null` — The consistency requirement, or null if not specified

### getContext

```php
public function getContext(): object|null

```

Get the context object for this request.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/StreamedListObjectsRequestInterface.php#L34)

#### Returns

`object` &#124; `null` — The context object, or null if not specified

### getContextualTuples

```php
public function getContextualTuples(): TupleKeysInterface|null

```

Get the contextual tuples for this request.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/StreamedListObjectsRequestInterface.php#L41)

#### Returns

[`TupleKeysInterface`](Models/Collections/TupleKeysInterface.md) &#124; `null` — The contextual tuples collection, or null if not specified

### getModel

```php
public function getModel(): string|null

```

Get the authorization model ID for this request.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/StreamedListObjectsRequestInterface.php#L48)

#### Returns

`string` &#124; `null` — The authorization model ID, or null if not specified

### getRelation

```php
public function getRelation(): string

```

Get the relation name for this request.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/StreamedListObjectsRequestInterface.php#L55)

#### Returns

`string` — The relation name to check

### getRequest

```php
public function getRequest(StreamFactoryInterface $streamFactory): RequestContext

```

Build a request context for HTTP execution. Transforms the request object into a standardized HTTP request context that can be executed by the OpenFGA HTTP client. This method handles all aspects of request preparation including parameter serialization, URL construction, header configuration, and body stream creation. The method validates that all required parameters are present and properly formatted, serializes complex objects to JSON, constructs the appropriate API endpoint URL, and creates the necessary HTTP message body streams.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/RequestInterface.php#L57)

#### Parameters

| Name             | Type                     | Description                                                                 |
| ---------------- | ------------------------ | --------------------------------------------------------------------------- |
| `$streamFactory` | `StreamFactoryInterface` | PSR-7 stream factory for creating request body streams from serialized data |

#### Returns

`RequestContext` — The prepared request context containing HTTP method, URL, headers, and body ready for execution

### getStore

```php
public function getStore(): string

```

Get the store ID for this request.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/StreamedListObjectsRequestInterface.php#L62)

#### Returns

`string` — The store ID

### getType

```php
public function getType(): string

```

Get the object type for this request.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/StreamedListObjectsRequestInterface.php#L69)

#### Returns

`string` — The object type to list

### getUser

```php
public function getUser(): string

```

Get the user identifier for this request.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/StreamedListObjectsRequestInterface.php#L76)

#### Returns

`string` — The user identifier
