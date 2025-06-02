# GetStoreRequestInterface

Interface for retrieving information about an OpenFGA store. This interface defines the contract for requests that fetch metadata and configuration information for a specific OpenFGA store. Store information includes details such as the store name, creation time, and other administrative metadata. Getting store information is useful for: - Administrative interfaces and dashboards - Verifying store existence before performing operations - Displaying store metadata to users - Auditing and monitoring store usage - Implementing store management workflows

## Namespace

`OpenFGA\Requests`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Requests/GetStoreRequestInterface.php)

## Implements

* [`RequestInterface`](RequestInterface.md)

## Related Classes

* [GetStoreResponseInterface](Responses/GetStoreResponseInterface.md) (response)

* [GetStoreRequest](Requests/GetStoreRequest.md) (implementation)

## Methods

#### getRequest

```php
public function getRequest(StreamFactoryInterface $streamFactory): RequestContext

```

Build a request context for HTTP execution. Transforms the request object into a standardized HTTP request context that can be executed by the OpenFGA HTTP client. This method handles all aspects of request preparation including parameter serialization, URL construction, header configuration, and body stream creation. The method validates that all required parameters are present and properly formatted, serializes complex objects to JSON, constructs the appropriate API endpoint URL, and creates the necessary HTTP message body streams.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/RequestInterface.php#L57)

#### Parameters

| Name | Type | Description |

|------|------|-------------|

| `$streamFactory` | `StreamFactoryInterface` | PSR-7 stream factory for creating request body streams from serialized data |

#### Returns

`RequestContext` — The prepared request context containing HTTP method, URL, headers, and body ready for execution

#### getStore

```php
public function getStore(): string

```

Get the ID of the store to retrieve. Returns the unique identifier of the store whose information should be fetched. This will return metadata about the store including its name, creation timestamp, and other administrative details.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/GetStoreRequestInterface.php#L36)

#### Returns

`string` — The unique identifier of the store to retrieve information for
