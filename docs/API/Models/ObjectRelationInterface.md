# ObjectRelationInterface

Represents an object-relation pair in OpenFGA authorization models. Object-relation pairs are fundamental components that specify a relationship between a specific object and a relation type. They are commonly used in: - Tuple definitions to specify what relationship exists - Userset references to point to related objects - Permission lookups to identify target resources The pair consists of: - Object: The target resource (for example, &quot;document:readme&quot;, &quot;folder:private&quot;) - Relation: The type of relationship (for example, &quot;viewer&quot;, &quot;editor&quot;, &quot;owner&quot;) Examples: - {object: &quot;document:readme&quot;, relation: &quot;viewer&quot;} - {object: &quot;folder:private&quot;, relation: &quot;owner&quot;} - {relation: &quot;member&quot;} (object can be omitted in some contexts)

## Namespace
`OpenFGA\Models`

## Source
[View source code](https://github.com/evansims/openfga-php/blob/main/src/Models/ObjectRelationInterface.php)

## Implements
* [`ModelInterface`](ModelInterface.md)
* `JsonSerializable`

## Related Classes
* [ObjectRelation](Models/ObjectRelation.md) (implementation)

## Methods

### List Operations
#### getObject

```php
public function getObject(): ?string
```

Get the object identifier in an object-relation pair. The object represents the resource or entity being referenced, typically formatted as &quot;type:id&quot; where type describes the kind of resource.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/ObjectRelationInterface.php#L40)

#### Returns
`string` &#124; `null`
#### getRelation

```php
public function getRelation(): ?string
```

Get the relation name that defines the type of relationship to the object. The relation describes what kind of permission or relationship exists. Common examples include &quot;owner&quot;, &quot;viewer&quot;, &quot;editor&quot;, &quot;member&quot;.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/ObjectRelationInterface.php#L50)

#### Returns
`string` &#124; `null`
### Other
#### jsonSerialize

```php
public function jsonSerialize(): array
```

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/ObjectRelationInterface.php#L56)

#### Returns
`array`
