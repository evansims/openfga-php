# ConditionInterface


## Namespace
`OpenFGA\Models`

## Implements
* [ModelInterface](Models/ModelInterface.md)
* JsonSerializable



## Methods
### getExpression


```php
public function getExpression(): string
```



#### Returns
string

### getMetadata


```php
public function getMetadata(): ?OpenFGA\Models\ConditionMetadataInterface
```



#### Returns
?[ConditionMetadataInterface](Models/ConditionMetadataInterface.md)

### getName


```php
public function getName(): string
```



#### Returns
string

### getParameters


```php
public function getParameters(): null|ConditionParametersInterface<ConditionParameterInterface>
```



#### Returns
null | ConditionParametersInterface&lt;[ConditionParameterInterface](Models/ConditionParameterInterface.md)&gt;

### jsonSerialize


```php
public function jsonSerialize(): array
```



#### Returns
array

