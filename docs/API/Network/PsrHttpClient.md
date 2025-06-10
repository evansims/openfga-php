# PsrHttpClient

PSR-18 compliant HTTP client implementation. This implementation wraps any PSR-18 compatible HTTP client, providing automatic discovery if no client is provided. It ensures compatibility with various HTTP client libraries while maintaining a consistent interface for the OpenFGA SDK.

## Namespace

`OpenFGA\Network`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Network/PsrHttpClient.php)

## Implements

* [`HttpClientInterface`](HttpClientInterface.md)

## Methods

#### send

```php
public function send(Psr\Http\Message\RequestInterface $request): Psr\Http\Message\ResponseInterface

```

Send an HTTP request and return the response.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Network/PsrHttpClient.php#L44)

#### Parameters

| Name       | Type                                | Description              |
| ---------- | ----------------------------------- | ------------------------ |
| `$request` | `Psr\Http\Message\RequestInterface` | The HTTP request to send |

#### Returns

`Psr\Http\Message\ResponseInterface` — The HTTP response
