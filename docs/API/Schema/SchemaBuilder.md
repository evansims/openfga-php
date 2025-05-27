# SchemaBuilder


## Namespace
`OpenFGA\Schema`




## Methods
### array


```php
public function array(string $name, array $items, bool $required = false, mixed $default = NULL): self
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
public function boolean(string $name, bool $required = false, null | mixed $default = NULL): self
```

Add a boolean property.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$name` | `string` |  |
| `$required` | `bool` |  |
| `$default` | `null | mixed` |  |

#### Returns
`self`

### date


```php
public function date(string $name, bool $required = false, null | mixed $default = NULL): self
```

Add a date property.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$name` | `string` |  |
| `$required` | `bool` |  |
| `$default` | `null | mixed` |  |

#### Returns
`self`

### datetime


```php
public function datetime(string $name, bool $required = false, null | mixed $default = NULL): self
```

Add a datetime property.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$name` | `string` |  |
| `$required` | `bool` |  |
| `$default` | `null | mixed` |  |

#### Returns
`self`

### integer


```php
public function integer(string $name, bool $required = false, null | mixed $default = NULL): self
```

Add an integer property.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$name` | `string` |  |
| `$required` | `bool` |  |
| `$default` | `null | mixed` |  |

#### Returns
`self`

### number


```php
public function number(string $name, bool $required = false, null | mixed $default = NULL): self
```

Add a number (float) property.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$name` | `string` |  |
| `$required` | `bool` |  |
| `$default` | `null | mixed` |  |

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
public function string(string $name, bool $required = false, null | string $format = NULL, null | array<string> $enum = NULL, mixed $default = NULL): self
```

Add a string property.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$name` | `string` |  |
| `$required` | `bool` |  |
| `$format` | `null | string` |  |
| `$enum` | `null | array<string>` |  |
| `$default` | `mixed` |  |

#### Returns
`self`

