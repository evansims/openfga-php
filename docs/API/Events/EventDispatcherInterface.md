# EventDispatcherInterface

Event dispatcher interface for handling domain events. The event dispatcher decouples event publishers from subscribers, allowing for flexible event handling and observability without tight coupling between business logic and infrastructure concerns.

## Namespace

`OpenFGA\Events`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Events/EventDispatcherInterface.php)

## Related Classes

* [EventDispatcher](Events/EventDispatcher.md) (implementation)

## Methods

### CRUD Operations

#### removeListeners

```php
public function removeListeners(string $eventType): void

```

Remove all listeners for a specific event type.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Events/EventDispatcherInterface.php#L56)

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

[View source](https://github.com/evansims/openfga-php/blob/main/src/Events/EventDispatcherInterface.php#L22)

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

[View source](https://github.com/evansims/openfga-php/blob/main/src/Events/EventDispatcherInterface.php#L41)

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

[View source](https://github.com/evansims/openfga-php/blob/main/src/Events/EventDispatcherInterface.php#L49)

#### Parameters

| Name         | Type     | Description             |
| ------------ | -------- | ----------------------- |
| `$eventType` | `string` | The event type to check |

#### Returns

`bool` — True if there are listeners, false otherwise

### Utility

#### dispatch

```php
public function dispatch(EventInterface $event): void

```

Dispatch an event to all registered listeners. Calls all listeners registered for the given event&#039;s type. If an event is stoppable and a listener stops propagation, remaining listeners will not be called.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Events/EventDispatcherInterface.php#L33)

#### Parameters

| Name     | Type                                  | Description           |
| -------- | ------------------------------------- | --------------------- |
| `$event` | [`EventInterface`](EventInterface.md) | The event to dispatch |

#### Returns

`void`
