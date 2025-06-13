# BatchRequestProcessor

Handles batch processing of write tuple requests. This class encapsulates the logic for processing write tuple requests in both transactional and non-transactional modes. It handles chunking, parallel execution, retries, and error aggregation.

## Table of Contents

* [Namespace](#namespace)
* [Source](#source)
* [Methods](#methods)

* [List Operations](#list-operations)
    * [`getLastRequest()`](#getlastrequest)
    * [`getLastResponse()`](#getlastresponse)
* [Other](#other)
    * [`process()`](#process)

## Namespace

`OpenFGA\Network`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Network/BatchRequestProcessor.php)

## Methods

### List Operations

#### getLastRequest

```php
public function getLastRequest(): ?Psr\Http\Message\RequestInterface

```

Get the last HTTP request made.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Network/BatchRequestProcessor.php#L42)

#### Returns

`Psr\Http\Message\RequestInterface` &#124; `null`

#### getLastResponse

```php
public function getLastResponse(): ?Psr\Http\Message\ResponseInterface

```

Get the last HTTP response received.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Network/BatchRequestProcessor.php#L50)

#### Returns

`Psr\Http\Message\ResponseInterface` &#124; `null`

### Other

#### process

```php
public function process(WriteTuplesRequest $request): SuccessInterface

```

Process a write tuples request.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Network/BatchRequestProcessor.php#L64)

#### Parameters

| Name       | Type                                                   | Description            |
| ---------- | ------------------------------------------------------ | ---------------------- |
| `$request` | [`WriteTuplesRequest`](Requests/WriteTuplesRequest.md) | The request to process |

#### Returns

[`SuccessInterface`](Results/SuccessInterface.md) — Always returns Success with WriteTuplesResponse
