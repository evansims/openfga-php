# EventInterface

Base interface for all domain events. Events represent something significant that happened in the domain. They are immutable value objects that capture the facts about what occurred.

## Table of Contents

* [Namespace](#namespace)
* [Source](#source)
* [Methods](#methods)

* [List Operations](#list-operations)
    * [`getEventId()`](#geteventid)
    * [`getEventType()`](#geteventtype)
    * [`getOccurredAt()`](#getoccurredat)
    * [`getPayload()`](#getpayload)
* [Utility](#utility)
    * [`isPropagationStopped()`](#ispropagationstopped)
* [Other](#other)
    * [`stopPropagation()`](#stoppropagation)

## Namespace

`OpenFGA\Events`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Events/EventInterface.php)

## Methods

### List Operations

#### getEventId

```php
public function getEventId(): string

```

Get the unique identifier for this event.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Events/EventInterface.php#L22)

#### Returns

`string` — A unique identifier for the event instance

#### getEventType

```php
public function getEventType(): string

```

Get the name/type of this event.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Events/EventInterface.php#L29)

#### Returns

`string` — The event type identifier

#### getOccurredAt

```php
public function getOccurredAt(): DateTimeImmutable

```

Get when this event occurred.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Events/EventInterface.php#L36)

#### Returns

`DateTimeImmutable` — The timestamp when the event was created

#### getPayload

```php
public function getPayload(): array<string, mixed>

```

Get the event payload data.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Events/EventInterface.php#L43)

#### Returns

`array&lt;`string`, `mixed`&gt;` — The event data

### Utility

#### isPropagationStopped

```php
public function isPropagationStopped(): bool

```

Check if event propagation should be stopped.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Events/EventInterface.php#L50)

#### Returns

`bool` — True if propagation should be stopped

### Other

#### stopPropagation

```php
public function stopPropagation(): void

```

Stop event propagation to remaining listeners.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Events/EventInterface.php#L55)

#### Returns

`void`
