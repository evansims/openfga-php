# RequestInterface

Base interface for all OpenFGA API request objects. This interface defines the core contract that all OpenFGA API requests must implement. Request objects encapsulate the parameters and configuration needed for specific API operations, providing a structured way to prepare HTTP requests for the OpenFGA service. Each request implementation handles: - Parameter validation and constraints - Request body serialization and formatting - HTTP method and endpoint determination - Header configuration and content negotiation - URL path construction with proper parameter encoding The interface follows the Command pattern, where each request object represents a specific operation to be performed against the OpenFGA API. This design enables consistent request handling, validation, and testing across all API operations.

<details>
<summary><strong>Table of Contents</strong></summary>

- [Namespace](#namespace)
- [Source](#source)
- [Related Classes](#related-classes)
- [Methods](#methods)

- [`getRequest()`](#getrequest)

</details>

## Namespace

`OpenFGA\Requests`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Requests/RequestInterface.php)

## Related Classes

- [ResponseInterface](Responses/ResponseInterface.md) (response)

## Methods

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
