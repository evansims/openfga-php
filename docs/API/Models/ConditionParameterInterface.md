# ConditionParameterInterface


## Namespace
`OpenFGA\Models`

## Implements
* [ModelInterface](Models/ModelInterface.md)
* JsonSerializable

## Methods
### getGenericTypes


```php
public function getGenericTypes(): ?[ConditionParametersInterface](Models/Collections/ConditionParametersInterface.md)
```



#### Returns
`?[ConditionParametersInterface](Models/Collections/ConditionParametersInterface.md)` 

### getTypeName


```php
public function getTypeName(): TypeName
```



#### Returns
`TypeName` 

### jsonSerialize


```php
public function jsonSerialize(): array
```



#### Returns
`array` &#039;TYPE_NAME_ANY&#039;|&#039;TYPE_NAME_BOOL&#039;|&#039;TYPE_NAME_DOUBLE&#039;|&#039;TYPE_NAME_DURATION&#039;|&#039;TYPE_NAME_INT&#039;|&#039;TYPE_NAME_IPADDRESS&#039;|&#039;TYPE_NAME_LIST&#039;|&#039;TYPE_NAME_MAP&#039;|&#039;TYPE_NAME_STRING&#039;|&#039;TYPE_NAME_TIMESTAMP&#039;|&#039;TYPE_NAME_UINT&#039;|&#039;TYPE_NAME_UNSPECIFIED&#039;|list&lt;array{generic_types?: array&lt;int, mixed&gt;, type_name: string}&gt;&gt;

