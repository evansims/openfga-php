# HttpClientInterface

HTTP client interface for sending HTTP requests. This interface abstracts the HTTP client implementation, allowing different HTTP clients to be used interchangeably. It follows the PSR-18 HTTP Client standard for compatibility.

## Table of Contents

* [Namespace](#namespace)
* [Source](#source)
* [Methods](#methods)

* [Other](#other)
    * [`send()`](#send)

## Namespace

`OpenFGA\Network`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Network/HttpClientInterface.php)

## Methods

#### send

```php
public function send(RequestInterface $request): ResponseInterface

```

Send an HTTP request and return the response.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Network/HttpClientInterface.php#L28)

#### Parameters

| Name       | Type                                               | Description              |
| ---------- | -------------------------------------------------- | ------------------------ |
| `$request` | [`RequestInterface`](Requests/RequestInterface.md) | The HTTP request to send |

#### Returns

[`ResponseInterface`](Responses/ResponseInterface.md) — The HTTP response
