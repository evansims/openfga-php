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

Get the exception if the operation failed.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Events/OperationCompletedEvent.php#L60)

#### Returns

`Throwable` &#124; `null` — The exception or null if the operation succeeded

### getModelId

```php
public function getModelId(): string|null

```

Get the model ID for the operation.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Events/OperationCompletedEvent.php#L70)

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

[View source](https://github.com/evansims/openfga-php/blob/main/src/Events/OperationCompletedEvent.php#L80)

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

### getResult

```php
public function getResult(): mixed

```

Get the result of the operation.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Events/OperationCompletedEvent.php#L90)

#### Returns

`mixed` — The operation result (typically a Response object) or null if failed

### getStoreId

```php
public function getStoreId(): string|null

```

Get the store ID for the operation.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Events/OperationCompletedEvent.php#L100)

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

Check if the operation completed successfully.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Events/OperationCompletedEvent.php#L110)

#### Returns

`bool` — True if the operation succeeded, false otherwise

### stopPropagation

```php
public function stopPropagation(): void

```

Stop event propagation to remaining listeners.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Events/AbstractEvent.php#L85)

#### Returns

`void`
