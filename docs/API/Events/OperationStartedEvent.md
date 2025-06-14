# OperationStartedEvent

Event fired when a high-level operation starts. This event tracks business operations like check, expand, writeTuples, etc.

## Table of Contents

- [Namespace](#namespace)
- [Source](#source)
- [Implements](#implements)
- [Methods](#methods)

- [List Operations](#list-operations)
  - [`getContext()`](#getcontext)
  - [`getEventId()`](#geteventid)
  - [`getEventType()`](#geteventtype)
  - [`getModelId()`](#getmodelid)
  - [`getOccurredAt()`](#getoccurredat)
  - [`getOperation()`](#getoperation)
  - [`getPayload()`](#getpayload)
  - [`getStoreId()`](#getstoreid)
- [Utility](#utility)
  - [`isPropagationStopped()`](#ispropagationstopped)
- [Other](#other)
  - [`stopPropagation()`](#stoppropagation)

## Namespace

`OpenFGA\Events`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Events/OperationStartedEvent.php)

## Implements

- [`EventInterface`](EventInterface.md)

## Methods

### List Operations

#### getContext

```php
public function getContext(): array<string, mixed>

```

[View source](https://github.com/evansims/openfga-php/blob/main/src/Events/OperationStartedEvent.php#L37)

#### Returns

`array&lt;`string`, `mixed`&gt;`

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

#### getModelId

```php
public function getModelId(): ?string

```

[View source](https://github.com/evansims/openfga-php/blob/main/src/Events/OperationStartedEvent.php#L42)

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

[View source](https://github.com/evansims/openfga-php/blob/main/src/Events/OperationStartedEvent.php#L47)

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

#### getStoreId

```php
public function getStoreId(): ?string

```

[View source](https://github.com/evansims/openfga-php/blob/main/src/Events/OperationStartedEvent.php#L52)

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

### Other

#### stopPropagation

```php
public function stopPropagation(): void

```

Stop event propagation to remaining listeners.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Events/AbstractEvent.php#L67)

#### Returns

`void`
