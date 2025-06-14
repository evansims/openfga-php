# GetAuthorizationModelRequestInterface

Interface for retrieving a specific authorization model. This interface defines the contract for requests that fetch a complete authorization model from an OpenFGA store. Authorization models define the relationship types, object types, and access control rules that govern permission evaluation in your application. Retrieving authorization models is essential for: - Inspecting current authorization schema and rules - Building administrative interfaces for model management - Implementing model comparison and diff functionality - Backing up and versioning authorization configurations - Understanding inheritance and relationship patterns - Debugging authorization behavior and rule conflicts The retrieved model includes all type definitions, conditions, and schema information needed to understand how permissions are evaluated.

## Table of Contents

- [Namespace](#namespace)
- [Source](#source)
- [Implements](#implements)
- [Related Classes](#related-classes)
- [Methods](#methods)

- [List Operations](#list-operations)
  - [`getModel()`](#getmodel)
  - [`getRequest()`](#getrequest)
  - [`getStore()`](#getstore)

## Namespace

`OpenFGA\Requests`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Requests/GetAuthorizationModelRequestInterface.php)

## Implements

- [`RequestInterface`](RequestInterface.md)

## Related Classes

- [GetAuthorizationModelResponseInterface](Responses/GetAuthorizationModelResponseInterface.md) (response)
- [GetAuthorizationModelRequest](Requests/GetAuthorizationModelRequest.md) (implementation)

## Methods

#### getModel

```php
public function getModel(): string

```

Get the authorization model ID to retrieve. Specifies which version of the authorization model should be fetched from the store. Each model has a unique identifier that allows you to retrieve specific versions even as new models are created.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/GetAuthorizationModelRequestInterface.php#L40)

#### Returns

`string` — The unique identifier of the authorization model to retrieve

#### getRequest

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

#### getStore

```php
public function getStore(): string

```

Get the store ID containing the authorization model. Identifies which OpenFGA store contains the authorization model to retrieve. Each store can contain multiple model versions, and this specifies which store context to search within.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/GetAuthorizationModelRequestInterface.php#L51)

#### Returns

`string` — The store ID containing the authorization model to retrieve
