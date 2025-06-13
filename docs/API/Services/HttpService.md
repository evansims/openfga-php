# HttpService

Default implementation of HttpServiceInterface. This implementation delegates to RequestManager for actual HTTP operations, providing a clean abstraction layer between the Client and network concerns. It emits events for telemetry and observability without direct coupling.

## Table of Contents

* [Namespace](#namespace)
* [Source](#source)
* [Implements](#implements)
* [Related Classes](#related-classes)
* [Methods](#methods)

* [List Operations](#list-operations)
    * [`getLastRequest()`](#getlastrequest)
    * [`getLastResponse()`](#getlastresponse)
* [Other](#other)
    * [`send()`](#send)

## Namespace

`OpenFGA\Services`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Services/HttpService.php)

## Implements

* [`HttpServiceInterface`](HttpServiceInterface.md)

## Related Classes

* [HttpServiceInterface](Services/HttpServiceInterface.md) (interface)

## Methods

### List Operations

#### getLastRequest

```php
public function getLastRequest(): ?Psr\Http\Message\RequestInterface

```

Get the last HTTP request sent. Returns the most recent HTTP request sent by this service, useful for debugging and error reporting.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Services/HttpService.php#L52)

#### Returns

`Psr\Http\Message\RequestInterface` &#124; `null` — The last request, or null if no requests sent

#### getLastResponse

```php
public function getLastResponse(): ?Psr\Http\Message\ResponseInterface

```

Get the last HTTP response received. Returns the most recent HTTP response received by this service, useful for debugging and error reporting.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Services/HttpService.php#L61)

#### Returns

`Psr\Http\Message\ResponseInterface` &#124; `null` — The last response, or null if no responses received

### Other

#### send

```php
public function send(OpenFGA\Requests\RequestInterface $request): Psr\Http\Message\ResponseInterface

```

Send an HTTP request. Sends a request to the OpenFGA API and returns the response. This method handles all HTTP-level concerns including authentication, retries, and error handling.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Services/HttpService.php#L75)

#### Parameters

| Name       | Type                                               | Description                 |
| ---------- | -------------------------------------------------- | --------------------------- |
| `$request` | [`RequestInterface`](Requests/RequestInterface.md) | The OpenFGA request to send |

#### Returns

`Psr\Http\Message\ResponseInterface` — The HTTP response
