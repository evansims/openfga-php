# ListAuthorizationModelsRequestInterface

Interface for listing authorization models in a store. This interface defines the contract for requests that retrieve a paginated list of all authorization model versions within a specific OpenFGA store. Authorization models are versioned, and this operation allows you to browse through the evolution of your authorization schema over time. Listing authorization models is useful for: - Administrative interfaces showing model version history - Implementing model rollback and comparison functionality - Auditing changes to authorization schemas over time - Building deployment and migration tools for authorization models - Understanding the evolution of permission structures - Debugging authorization issues by examining model versions Each model in the list includes metadata such as creation time and model ID, allowing you to understand when changes were made and select specific versions for detailed inspection or operational use.

<details>
<summary><strong>Table of Contents</strong></summary>

- [Namespace](#namespace)
- [Source](#source)
- [Implements](#implements)
- [Related Classes](#related-classes)
- [Methods](#methods)

- [`getContinuationToken()`](#getcontinuationtoken)
  - [`getPageSize()`](#getpagesize)
  - [`getRequest()`](#getrequest)
  - [`getStore()`](#getstore)

</details>

## Namespace

`OpenFGA\Requests`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Requests/ListAuthorizationModelsRequestInterface.php)

## Implements

- [`RequestInterface`](RequestInterface.md)

## Related Classes

- [ListAuthorizationModelsResponseInterface](Responses/ListAuthorizationModelsResponseInterface.md) (response)
- [ListAuthorizationModelsRequest](Requests/ListAuthorizationModelsRequest.md) (implementation)

## Methods

### getContinuationToken

```php
public function getContinuationToken(): string|null

```

Get the continuation token for paginated results. Returns the pagination token from a previous list models operation to continue retrieving results from where the last request left off. This enables efficient pagination through stores with many model versions without missing or duplicating entries.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/ListAuthorizationModelsRequestInterface.php#L42)

#### Returns

`string` &#124; `null` — The continuation token from a previous operation, or null for the first page

### getPageSize

```php
public function getPageSize(): int|null

```

Get the maximum number of models to return per page. Specifies the page size for paginated results. This controls how many authorization models are returned in a single response. Smaller page sizes reduce memory usage and latency, while larger page sizes reduce the number of API calls needed to retrieve all model versions.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/ListAuthorizationModelsRequestInterface.php#L54)

#### Returns

`int` &#124; `null` — The maximum number of models to return per page, or null to use the default page size

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

Get the store ID containing the authorization models to list. Identifies which OpenFGA store contains the authorization models to enumerate. Each store maintains its own independent collection of model versions, representing the evolution of that store&#039;s authorization schema over time.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/ListAuthorizationModelsRequestInterface.php#L66)

#### Returns

`string` — The store ID containing the authorization models to list
