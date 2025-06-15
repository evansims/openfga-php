# HttpServiceInterface

Service for handling HTTP communication. This service abstracts HTTP operations from the Client class, providing a clean interface for sending requests and managing HTTP-related state like last request/response tracking.

<details>
<summary><strong>Table of Contents</strong></summary>

- [Namespace](#namespace)
- [Source](#source)
- [Related Classes](#related-classes)
- [Methods](#methods)

- [`getLastRequest()`](#getlastrequest)
  - [`getLastResponse()`](#getlastresponse)
  - [`send()`](#send)

</details>

## Namespace

`OpenFGA\Services`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Services/HttpServiceInterface.php)

## Related Classes

- [HttpService](Services/HttpService.md) (implementation)

## Methods

### getLastRequest

```php
public function getLastRequest(): HttpRequestInterface|null

```

Get the last HTTP request sent. Returns the most recent HTTP request sent by this service, useful for debugging and error reporting.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Services/HttpServiceInterface.php#L28)

#### Returns

`HttpRequestInterface` &#124; `null` — The last request, or null if no requests sent

### getLastResponse

```php
public function getLastResponse(): HttpResponseInterface|null

```

Get the last HTTP response received. Returns the most recent HTTP response received by this service, useful for debugging and error reporting.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Services/HttpServiceInterface.php#L38)

#### Returns

`HttpResponseInterface` &#124; `null` — The last response, or null if no responses received

### send

```php
public function send(RequestInterface $request): HttpResponseInterface

```

Send an HTTP request. Sends a request to the OpenFGA API and returns the response. This method handles all HTTP-level concerns including authentication, retries, and error handling.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Services/HttpServiceInterface.php#L54)

#### Parameters

| Name       | Type                                               | Description                 |
| ---------- | -------------------------------------------------- | --------------------------- |
| `$request` | [`RequestInterface`](Requests/RequestInterface.md) | The OpenFGA request to send |

#### Returns

`HttpResponseInterface` — The HTTP response
