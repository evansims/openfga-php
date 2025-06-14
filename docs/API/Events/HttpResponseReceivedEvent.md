# HttpResponseReceivedEvent

Event fired when an HTTP response is received from the OpenFGA API. This event contains both the request and response for complete telemetry tracking.

## Table of Contents

- [Namespace](#namespace)
- [Source](#source)
- [Implements](#implements)
- [Methods](#methods)

- [List Operations](#list-operations)
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
- [Utility](#utility)
  - [`isPropagationStopped()`](#ispropagationstopped)
  - [`isSuccessful()`](#issuccessful)
- [Other](#other)
  - [`stopPropagation()`](#stoppropagation)

## Namespace

`OpenFGA\Events`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Events/HttpResponseReceivedEvent.php)

## Implements

- [`EventInterface`](EventInterface.md)

## Methods

### List Operations

#### getEventId

```php
public function getEventId(): string

```

Get the unique identifier for this event.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Events/AbstractEvent.php#L37)

#### Returns

`string` — A unique identifier for the event instance

#### getEventType

```php
public function getEventType(): string

```

Get the name/type of this event.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Events/AbstractEvent.php#L43)

#### Returns

`string` — The event type identifier

#### getException

```php
public function getException(): ?Throwable

```

[View source](https://github.com/evansims/openfga-php/blob/main/src/Events/HttpResponseReceivedEvent.php#L38)

#### Returns

`Throwable` &#124; `null`

#### getModelId

```php
public function getModelId(): ?string

```

[View source](https://github.com/evansims/openfga-php/blob/main/src/Events/HttpResponseReceivedEvent.php#L43)

#### Returns

`string` &#124; `null`

#### getOccurredAt

```php
public function getOccurredAt(): DateTimeImmutable

```

Get when this event occurred.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Events/AbstractEvent.php#L49)

#### Returns

`DateTimeImmutable` — The timestamp when the event was created

#### getOperation

```php
public function getOperation(): string

```

[View source](https://github.com/evansims/openfga-php/blob/main/src/Events/HttpResponseReceivedEvent.php#L48)

#### Returns

`string`

#### getPayload

```php
public function getPayload(): array

```

Get the event payload data.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Events/AbstractEvent.php#L55)

#### Returns

`array` — The event data

#### getRequest

```php
public function getRequest(): Psr\Http\Message\RequestInterface

```

[View source](https://github.com/evansims/openfga-php/blob/main/src/Events/HttpResponseReceivedEvent.php#L53)

#### Returns

`Psr\Http\Message\RequestInterface`

#### getResponse

```php
public function getResponse(): ?Psr\Http\Message\ResponseInterface

```

[View source](https://github.com/evansims/openfga-php/blob/main/src/Events/HttpResponseReceivedEvent.php#L58)

#### Returns

`Psr\Http\Message\ResponseInterface` &#124; `null`

#### getStoreId

```php
public function getStoreId(): ?string

```

[View source](https://github.com/evansims/openfga-php/blob/main/src/Events/HttpResponseReceivedEvent.php#L63)

#### Returns

`string` &#124; `null`

### Utility

#### isPropagationStopped

```php
public function isPropagationStopped(): bool

```

Check if event propagation should be stopped.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Events/AbstractEvent.php#L61)

#### Returns

`bool` — True if propagation should be stopped

#### isSuccessful

```php
public function isSuccessful(): bool

```

[View source](https://github.com/evansims/openfga-php/blob/main/src/Events/HttpResponseReceivedEvent.php#L68)

#### Returns

`bool`

### Other

#### stopPropagation

```php
public function stopPropagation(): void

```

Stop event propagation to remaining listeners.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Events/AbstractEvent.php#L67)

#### Returns

`void`
