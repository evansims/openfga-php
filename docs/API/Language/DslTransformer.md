# DslTransformer


## Namespace
`OpenFGA\Language`

## Implements
* [DslTransformerInterface](Language/DslTransformerInterface.md)



## Methods
### fromDsl

*<small>Implements Language\DslTransformerInterface</small>*  

```php
public function fromDsl(string $dsl, OpenFGA\Schema\SchemaValidator $validator): OpenFGA\Models\AuthorizationModelInterface
```


#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$dsl` | string |  |
| `$validator` | [SchemaValidator](Schema/SchemaValidator.md) |  |

#### Returns
[AuthorizationModelInterface](Models/AuthorizationModelInterface.md)

### toDsl

*<small>Implements Language\DslTransformerInterface</small>*  

```php
public function toDsl(OpenFGA\Models\AuthorizationModelInterface $model): string
```


#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$model` | [AuthorizationModelInterface](Models/AuthorizationModelInterface.md) |  |

#### Returns
string

