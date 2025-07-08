# HttpResponseReceivedEvent

Event fired when an HTTP response is received from the OpenFGA API. This event contains both the request and response for complete telemetry tracking.

<details>
<summary><strong>Table of Contents</strong></summary>

- [Namespace](#namespace)
- [Source](#source)
- [Implements](#implements)
- [Methods](#methods)

- [`getEventId()`](#geteventid)
  - [`getEventType()`](#geteventtype)
  - [`getException()`](#getexception)
  - [`getModelId()`](#getmodelid)
  - [`getOccurredAt()`](#getoccurredat)
  - [`getOperation()`](#getoperation)
  - [`getPayload()`](#getpayload)
  - [`getRequest()`](#getrequest)
  - [`getResponse()`](#getresponse)
  - [`getStoreId()`](#getstoreid)
  - [`isPropagationStopped()`](#ispropagationstopped)
  - [`isSuccessful()`](#issuccessful)
  - [`stopPropagation()`](#stoppropagation)

</details>

## Namespace

`OpenFGA\Events`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Events/HttpResponseReceivedEvent.php)

## Implements

- [`EventInterface`](EventInterface.md)

## Methods

### getEventId

```php
public function getEventId(): string

```

Get the unique identifier for this event.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Events/AbstractEvent.php#L40)

#### Returns

`string` — A unique identifier for the event instance

### getEventType

```php
public function getEventType(): string

```

Get the name/type of this event.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Events/AbstractEvent.php#L49)

#### Returns

`string` — The event type identifier

### getException

```php
public function getException(): Throwable|null

```

Get the exception if the request failed.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Events/HttpResponseReceivedEvent.php#L53)

#### Returns

`Throwable` &#124; `null` — The exception or null if the request succeeded

### getModelId

```php
public function getModelId(): string|null

```

Get the model ID for the operation.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Events/HttpResponseReceivedEvent.php#L63)

#### Returns

`string` &#124; `null` — The model ID or null if not applicable

### getOccurredAt

```php
public function getOccurredAt(): DateTimeImmutable

```

Get when this event occurred.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Events/AbstractEvent.php#L58)

#### Returns

`DateTimeImmutable` — The timestamp when the event was created

### getOperation

```php
public function getOperation(): string

```

Get the OpenFGA operation name.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Events/HttpResponseReceivedEvent.php#L73)

#### Returns

`string` — The operation name (for example, &#039;check&#039;, &#039;write&#039;, &#039;read&#039;)

### getPayload

```php
public function getPayload(): array

```

Get the event payload data.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Events/AbstractEvent.php#L67)

#### Returns

`array` — The event data

### getRequest

```php
public function getRequest(): RequestInterface

```

Get the HTTP request that was sent.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Events/HttpResponseReceivedEvent.php#L83)

#### Returns

[`RequestInterface`](Requests/RequestInterface.md) — The PSR-7 request object

### getResponse

```php
public function getResponse(): ResponseInterface|null

```

Get the HTTP response received.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Events/HttpResponseReceivedEvent.php#L93)

#### Returns

[`ResponseInterface`](Responses/ResponseInterface.md) &#124; `null` — The PSR-7 response object or null if an exception occurred

### getStoreId

```php
public function getStoreId(): string|null

```

Get the store ID for the operation.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Events/HttpResponseReceivedEvent.php#L103)

#### Returns

`string` &#124; `null` — The store ID or null if not applicable

### isPropagationStopped

```php
public function isPropagationStopped(): bool

```

Check if event propagation should be stopped.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Events/AbstractEvent.php#L76)

#### Returns

`bool` — True if propagation should be stopped

### isSuccessful

```php
public function isSuccessful(): bool

```

Check if the HTTP request was successful.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Events/HttpResponseReceivedEvent.php#L113)

#### Returns

`bool` — True if no exception occurred, false otherwise

### stopPropagation

```php
public function stopPropagation(): void

```

Stop event propagation to remaining listeners.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Events/AbstractEvent.php#L85)

#### Returns

`void`
