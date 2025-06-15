# EventDispatcher

Simple event dispatcher implementation. Manages event listeners and dispatches events to registered handlers. Supports event propagation control for stoppable events.

<details>
<summary><strong>Table of Contents</strong></summary>

- [Namespace](#namespace)
- [Source](#source)
- [Implements](#implements)
- [Related Classes](#related-classes)
- [Methods](#methods)

- [`addListener()`](#addlistener)
  - [`dispatch()`](#dispatch)
  - [`getListeners()`](#getlisteners)
  - [`hasListeners()`](#haslisteners)
  - [`removeListeners()`](#removelisteners)

</details>

## Namespace

`OpenFGA\Events`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Events/EventDispatcher.php)

## Implements

- [`EventDispatcherInterface`](EventDispatcherInterface.md)

## Related Classes

- [EventDispatcherInterface](Events/EventDispatcherInterface.md) (interface)

## Methods

### addListener

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

### dispatch

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

### getListeners

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

### hasListeners

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

### removeListeners

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
