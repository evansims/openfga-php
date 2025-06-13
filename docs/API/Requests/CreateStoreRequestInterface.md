# CreateStoreRequestInterface

Interface for creating a new OpenFGA store. This interface defines the contract for requests that create new authorization stores in OpenFGA. A store is an isolated container for authorization data, including relationship tuples, authorization models, and configuration. Each store provides: - Complete isolation of authorization data from other stores - Independent versioning of authorization models - Separate configuration and access controls - Dedicated API endpoints for all operations Creating a store establishes a new authorization domain where you can define relationship models, write authorization tuples, and perform permission checks. The store name serves as a human-readable identifier for administrative purposes.

## Table of Contents

* [Namespace](#namespace)
* [Source](#source)
* [Implements](#implements)
* [Related Classes](#related-classes)
* [Methods](#methods)

* [List Operations](#list-operations)
    * [`getName()`](#getname)
    * [`getRequest()`](#getrequest)

## Namespace

`OpenFGA\Requests`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Requests/CreateStoreRequestInterface.php)

## Implements

* [`RequestInterface`](RequestInterface.md)

## Related Classes

* [CreateStoreResponseInterface](Responses/CreateStoreResponseInterface.md) (response)
* [CreateStoreRequest](Requests/CreateStoreRequest.md) (implementation)

## Methods

#### getName

```php
public function getName(): string

```

Get the name for the new store. Returns the human-readable name that will be assigned to the new store. This name is used for identification and administrative purposes and should be descriptive enough to distinguish the store from others in your organization. The store name: - Must be a non-empty string - Should be descriptive and meaningful for administrative purposes - Is used for display in management interfaces and logging - Does not need to be globally unique (the store ID serves that purpose)

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/CreateStoreRequestInterface.php#L45)

#### Returns

`string` — The descriptive name for the new authorization store

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
