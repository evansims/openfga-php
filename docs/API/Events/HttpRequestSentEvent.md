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

[View source](https://github.com/evansims/openfga-php/blob/main/src/Events/AbstractEvent.php#L37)

#### Returns

`string` — A unique identifier for the event instance

### getEventType

```php
public function getEventType(): string

```

Get the name/type of this event.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Events/AbstractEvent.php#L43)

#### Returns

`string` — The event type identifier

### getModelId

```php
public function getModelId(): ?string

```

[View source](https://github.com/evansims/openfga-php/blob/main/src/Events/HttpRequestSentEvent.php#L33)

#### Returns

`string` &#124; `null`

### getOccurredAt

```php
public function getOccurredAt(): DateTimeImmutable

```

Get when this event occurred.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Events/AbstractEvent.php#L49)

#### Returns

`DateTimeImmutable` — The timestamp when the event was created

### getOperation

```php
public function getOperation(): string

```

[View source](https://github.com/evansims/openfga-php/blob/main/src/Events/HttpRequestSentEvent.php#L38)

#### Returns

`string`

### getPayload

```php
public function getPayload(): array

```

Get the event payload data.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Events/AbstractEvent.php#L55)

#### Returns

`array` — The event data

### getRequest

```php
public function getRequest(): Psr\Http\Message\RequestInterface

```

[View source](https://github.com/evansims/openfga-php/blob/main/src/Events/HttpRequestSentEvent.php#L43)

#### Returns

`Psr\Http\Message\RequestInterface`

### getStoreId

```php
public function getStoreId(): ?string

```

[View source](https://github.com/evansims/openfga-php/blob/main/src/Events/HttpRequestSentEvent.php#L48)

#### Returns

`string` &#124; `null`

### isPropagationStopped

```php
public function isPropagationStopped(): bool

```

Check if event propagation should be stopped.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Events/AbstractEvent.php#L61)

#### Returns

`bool` — True if propagation should be stopped

### stopPropagation

```php
public function stopPropagation(): void

```

Stop event propagation to remaining listeners.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Events/AbstractEvent.php#L67)

#### Returns

`void`
