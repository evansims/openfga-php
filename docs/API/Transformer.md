# Transformer

OpenFGA DSL Transformer implementation for authorization model conversions. This class provides complete implementation for converting between OpenFGA&#039;s Domain Specific Language (DSL) format and structured authorization model objects. It supports complex relationship definitions including unions, intersections, exclusions, and computed usersets with proper precedence handling. The transformer parses DSL syntax including: - Type definitions with relations - Direct user assignments [user, organization#member] - Computed usersets (owner, administrator) - Tuple-to-userset relations (owner from parent) - Boolean operations (and, or, but not) - Parenthetical grouping for precedence

## Namespace
`OpenFGA`

## Source
[View source code](https://github.com/evansims/openfga-php/blob/main/src/Transformer.php)

## Implements
* [`TransformerInterface`](TransformerInterface.md)

## Related Classes
* [TransformerInterface](TransformerInterface.md) (interface)

## Methods

#### fromDsl

*<small>Implements TransformerInterface</small>*

```php
public function fromDsl(string $dsl, SchemaValidator $validator): AuthorizationModelInterface
```

Parse a DSL string into an authorization model. This method converts a human-readable DSL (Domain Specific Language) string into a structured authorization model object that can be used with the OpenFGA API. The DSL provides an intuitive way to define authorization relationships and permissions using familiar syntax.

[View source](https://github.com/evansims/openfga-php/blob/main/src/TransformerInterface.php#L44)

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$dsl` | `string` | The DSL string containing the authorization model definition |
| `$validator` | `SchemaValidator` | Schema validator for validating the parsed model structure |

#### Returns
[`AuthorizationModelInterface`](Models/AuthorizationModelInterface.md) — The parsed authorization model ready for API operations
#### toDsl

*<small>Implements TransformerInterface</small>*

```php
public function toDsl(AuthorizationModelInterface $model): string
```

Convert an authorization model to its DSL string representation. This method transforms a structured authorization model object back into its human-readable DSL format, making it easy to review, edit, or share authorization model definitions. The output can be saved to files, version controlled, or used for documentation purposes.

[View source](https://github.com/evansims/openfga-php/blob/main/src/TransformerInterface.php#L59)

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$model` | [`AuthorizationModelInterface`](Models/AuthorizationModelInterface.md) | The authorization model to convert to DSL format |

#### Returns
`string` — The DSL string representation of the authorization model
