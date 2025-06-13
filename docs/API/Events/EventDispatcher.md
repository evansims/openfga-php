# EventDispatcher

Simple event dispatcher implementation. Manages event listeners and dispatches events to registered handlers. Supports event propagation control for stoppable events.

## Table of Contents

* [Namespace](#namespace)
* [Source](#source)
* [Implements](#implements)
* [Related Classes](#related-classes)
* [Methods](#methods)

* [CRUD Operations](#crud-operations)
    * [`removeListeners()`](#removelisteners)
* [List Operations](#list-operations)
    * [`addListener()`](#addlistener)
    * [`getListeners()`](#getlisteners)
    * [`hasListeners()`](#haslisteners)
* [Utility](#utility)
    * [`dispatch()`](#dispatch)

## Namespace

`OpenFGA\Events`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Events/EventDispatcher.php)

## Implements

* [`EventDispatcherInterface`](EventDispatcherInterface.md)

## Related Classes

* [EventDispatcherInterface](Events/EventDispatcherInterface.md) (interface)

## Methods

### CRUD Operations

#### removeListeners

```php
public function removeListeners(string $eventType): void

```

Remove all listeners for a specific event type.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Events/EventDispatcher.php#L68)

#### Parameters

| Name         | Type     | Description                           |
| ------------ | -------- | ------------------------------------- |
| `$eventType` | `string` | The event type to clear listeners for |

#### Returns

`void`

### List Operations

#### addListener

```php
public function addListener(string $eventType, callable $listener): void

```

Register an event listener for a specific event type.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Events/EventDispatcher.php#L25)

#### Parameters

| Name         | Type       | Description                                             |
| ------------ | ---------- | ------------------------------------------------------- |
| `$eventType` | `string`   | The class name or identifier of the event to listen for |
| `$listener`  | `callable` |                                                         |

#### Returns

`void`

#### getListeners

```php
public function getListeners(string $eventType): array<callable(object): void>

```

Get all registered listeners for a specific event type.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Events/EventDispatcher.php#L56)

#### Parameters

| Name         | Type     | Description                         |
| ------------ | -------- | ----------------------------------- |
| `$eventType` | `string` | The event type to get listeners for |

#### Returns

`array&lt;`callable(object): void`&gt;` — Array of listeners for the event type

#### hasListeners

```php
public function hasListeners(string $eventType): bool

```

Check if there are any listeners for a specific event type.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Events/EventDispatcher.php#L62)

#### Parameters

| Name         | Type     | Description             |
| ------------ | -------- | ----------------------- |
| `$eventType` | `string` | The event type to check |

#### Returns

`bool` — True if there are listeners, false otherwise

### Utility

#### dispatch

```php
public function dispatch(OpenFGA\Events\EventInterface $event): void

```

Dispatch an event to all registered listeners. Calls all listeners registered for the given event&#039;s type. If an event is stoppable and a listener stops propagation, remaining listeners will not be called.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Events/EventDispatcher.php#L35)

#### Parameters

| Name     | Type                                  | Description           |
| -------- | ------------------------------------- | --------------------- |
| `$event` | [`EventInterface`](EventInterface.md) | The event to dispatch |

#### Returns

`void`
