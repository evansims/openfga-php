# OperationCompletedEvent

Event fired when a high-level operation completes. This event tracks the completion of business operations with success/failure information.

<details>
<summary><strong>Table of Contents</strong></summary>

- [Namespace](#namespace)
- [Source](#source)
- [Implements](#implements)
- [Methods](#methods)

- [`getContext()`](#getcontext)
  - [`getEventId()`](#geteventid)
  - [`getEventType()`](#geteventtype)
  - [`getException()`](#getexception)
  - [`getModelId()`](#getmodelid)
  - [`getOccurredAt()`](#getoccurredat)
  - [`getOperation()`](#getoperation)
  - [`getPayload()`](#getpayload)
  - [`getResult()`](#getresult)
  - [`getStoreId()`](#getstoreid)
  - [`isPropagationStopped()`](#ispropagationstopped)
  - [`isSuccessful()`](#issuccessful)
  - [`stopPropagation()`](#stoppropagation)

</details>

## Namespace

`OpenFGA\Events`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Events/OperationCompletedEvent.php)

## Implements

- [`EventInterface`](EventInterface.md)

## Methods

### getContext

```php
public function getContext(): array<string, mixed>

```

[View source](https://github.com/evansims/openfga-php/blob/main/src/Events/OperationCompletedEvent.php#L50)

#### Returns

`array&lt;`string`, `mixed`&gt;`

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

### getException

```php
public function getException(): ?Throwable

```

[View source](https://github.com/evansims/openfga-php/blob/main/src/Events/OperationCompletedEvent.php#L55)

#### Returns

`Throwable` &#124; `null`

### getModelId

```php
public function getModelId(): ?string

```

[View source](https://github.com/evansims/openfga-php/blob/main/src/Events/OperationCompletedEvent.php#L60)

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

[View source](https://github.com/evansims/openfga-php/blob/main/src/Events/OperationCompletedEvent.php#L65)

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

### getResult

```php
public function getResult(): mixed

```

[View source](https://github.com/evansims/openfga-php/blob/main/src/Events/OperationCompletedEvent.php#L70)

#### Returns

`mixed`

### getStoreId

```php
public function getStoreId(): ?string

```

[View source](https://github.com/evansims/openfga-php/blob/main/src/Events/OperationCompletedEvent.php#L75)

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

### isSuccessful

```php
public function isSuccessful(): bool

```

[View source](https://github.com/evansims/openfga-php/blob/main/src/Events/OperationCompletedEvent.php#L80)

#### Returns

`bool`

### stopPropagation

```php
public function stopPropagation(): void

```

Stop event propagation to remaining listeners.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Events/AbstractEvent.php#L67)

#### Returns

`void`
