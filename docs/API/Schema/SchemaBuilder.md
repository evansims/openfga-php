# SchemaBuilder


## Namespace
`OpenFGA\Schema`


## Methods
### array

```php
public function array(string $name, array $items, bool $required = false, mixed $default = null): self
```

Add an array property.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$name` | `string` |  |
| `$items` | `array` |  |
| `$required` | `bool` |  |
| `$default` | `mixed` |  |

#### Returns
`self` 

### boolean

```php
public function boolean(string $name, bool $required = false, mixed $default = null): self
```

Add a boolean property.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$name` | `string` |  |
| `$required` | `bool` |  |
| `$default` | `mixed` |  |

#### Returns
`self` 

### date

```php
public function date(string $name, bool $required = false, mixed $default = null): self
```

Add a date property.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$name` | `string` |  |
| `$required` | `bool` |  |
| `$default` | `mixed` |  |

#### Returns
`self` 

### datetime

```php
public function datetime(string $name, bool $required = false, mixed $default = null): self
```

Add a datetime property.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$name` | `string` |  |
| `$required` | `bool` |  |
| `$default` | `mixed` |  |

#### Returns
`self` 

### integer

```php
public function integer(string $name, bool $required = false, mixed $default = null): self
```

Add an integer property.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$name` | `string` |  |
| `$required` | `bool` |  |
| `$default` | `mixed` |  |

#### Returns
`self` 

### number

```php
public function number(string $name, bool $required = false, mixed $default = null): self
```

Add a number (float) property.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$name` | `string` |  |
| `$required` | `bool` |  |
| `$default` | `mixed` |  |

#### Returns
`self` 

### object

```php
public function object(string $name, string $className, bool $required = false): self
```

Add an object property.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$name` | `string` |  |
| `$className` | `string` |  |
| `$required` | `bool` |  |

#### Returns
`self` 

### register

```php
public function register(): [Schema](Schema/Schema.md)
```

Build and register the schema.


#### Returns
`[Schema](Schema/Schema.md)` 

### string

```php
public function string(string $name, bool $required = false, ?string $format = null, ?array $enum = null, mixed $default = null): self
```

Add a string property.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$name` | `string` |  |
| `$required` | `bool` |  |
| `$format` | `?string` |  |
| `$enum` | `?array` |  |
| `$default` | `mixed` |  |

#### Returns
`self` 

