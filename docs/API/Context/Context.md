# Context

Ambient Context Manager. Provides Python-style context management for PHP, allowing functions to access shared context without explicit parameter passing. This enables a more ergonomic API where client, store, and model can be set once and used implicitly by helper functions. Contexts support inheritance - child contexts automatically inherit values from their parent context unless explicitly overridden. This allows for flexible nesting where you can override just the pieces you need.

<details>
<summary><strong>Table of Contents</strong></summary>

- [Namespace](#namespace)
- [Source](#source)
- [Implements](#implements)
- [Related Classes](#related-classes)
- [Methods](#methods)

- [`current()`](#current)
  - [`depth()`](#depth)
  - [`getClient()`](#getclient)
  - [`getModel()`](#getmodel)
  - [`getPrevious()`](#getprevious)
  - [`getStore()`](#getstore)
  - [`hasContext()`](#hascontext)
  - [`with()`](#with)

</details>

## Namespace

`OpenFGA\Context`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Context/Context.php)

## Implements

- [`ContextInterface`](ContextInterface.md)

## Related Classes

- [ContextInterface](Context/ContextInterface.md) (interface)

## Methods

### current

*<small>Implements Context\ContextInterface</small>*

```php
public function current(): self

```

Get the current ambient context.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Context/ContextInterface.php#L26)

#### Returns

`self`

### depth

*<small>Implements Context\ContextInterface</small>*

```php
public function depth(): int

```

Get the current nesting depth of contexts.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Context/ContextInterface.php#L33)

#### Returns

`int` — The number of active contexts in the stack

### getClient

*<small>Implements Context\ContextInterface</small>*

```php
public function getClient(): ClientInterface|null

```

Get the current client.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Context/ContextInterface.php#L40)

#### Returns

[`ClientInterface`](ClientInterface.md) &#124; `null` — The current client instance or null if not set

### getModel

*<small>Implements Context\ContextInterface</small>*

```php
public function getModel(): AuthorizationModelInterface|string|null

```

Get the current authorization model.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Context/ContextInterface.php#L47)

#### Returns

[`AuthorizationModelInterface`](Models/AuthorizationModelInterface.md) &#124; `string` &#124; `null` — The current model instance, model ID, or null if not set

### getPrevious

*<small>Implements Context\ContextInterface</small>*

```php
public function getPrevious(): ?self

```

Get the previous context in the stack.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Context/ContextInterface.php#L52)

#### Returns

`self` &#124; `null`

### getStore

*<small>Implements Context\ContextInterface</small>*

```php
public function getStore(): StoreInterface|string|null

```

Get the current store.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Context/ContextInterface.php#L59)

#### Returns

[`StoreInterface`](Models/StoreInterface.md) &#124; `string` &#124; `null` — The current store instance, store ID, or null if not set

### hasContext

*<small>Implements Context\ContextInterface</small>*

```php
public function hasContext(): bool

```

Check if an ambient context is currently active.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Context/ContextInterface.php#L66)

#### Returns

`bool` — True if at least one context is active, false otherwise

### with

*<small>Implements Context\ContextInterface</small>*

```php
public function with(
    callable $fn,
    ?OpenFGA\ClientInterface $client = NULL,
    StoreInterface|string|null $store = NULL,
    AuthorizationModelInterface|string|null $model = NULL,
): T

```

Execute a callable within a new ambient context.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Context/ContextInterface.php#L82)

#### Parameters

| Name      | Type                                                                                                 | Description                     |
| --------- | ---------------------------------------------------------------------------------------------------- | ------------------------------- |
| `$fn`     | `callable`                                                                                           |                                 |
| `$client` | [`ClientInterface`](ClientInterface.md) &#124; `null`                                                | Optional client for the context |
| `$store`  | [`StoreInterface`](Models/StoreInterface.md) &#124; `string` &#124; `null`                           | Optional store for the context  |
| `$model`  | [`AuthorizationModelInterface`](Models/AuthorizationModelInterface.md) &#124; `string` &#124; `null` | Optional model for the context  |

#### Returns

`T` — The result of the callable execution
