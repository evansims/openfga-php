# HttpRequestSentEvent

Event fired when an HTTP request is sent to the OpenFGA API. This event contains the outgoing request details for telemetry and debugging.

<details>
<summary><strong>Table of Contents</strong></summary>

- [Namespace](#namespace)
- [Source](#source)
- [Implements](#implements)
- [Methods](#methods)

- [`getEventId()`](#geteventid)
  - [`getEventType()`](#geteventtype)
  - [`getModelId()`](#getmodelid)
  - [`getOccurredAt()`](#getoccurredat)
  - [`getOperation()`](#getoperation)
  - [`getPayload()`](#getpayload)
  - [`getRequest()`](#getrequest)
  - [`getStoreId()`](#getstoreid)
  - [`isPropagationStopped()`](#ispropagationstopped)
  - [`stopPropagation()`](#stoppropagation)

</details>

## Namespace

`OpenFGA\Events`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Events/HttpRequestSentEvent.php)

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

### getModelId

```php
public function getModelId(): string|null

```

Get the model ID for the operation.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Events/HttpRequestSentEvent.php#L46)

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

[View source](https://github.com/evansims/openfga-php/blob/main/src/Events/HttpRequestSentEvent.php#L56)

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

Get the HTTP request being sent.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Events/HttpRequestSentEvent.php#L66)

#### Returns

[`RequestInterface`](Requests/RequestInterface.md) — The PSR-7 request object

### getStoreId

```php
public function getStoreId(): string|null

```

Get the store ID for the operation.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Events/HttpRequestSentEvent.php#L76)

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

### stopPropagation

```php
public function stopPropagation(): void

```

Stop event propagation to remaining listeners.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Events/AbstractEvent.php#L85)

#### Returns

`void`
