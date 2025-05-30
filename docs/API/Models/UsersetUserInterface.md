# UsersetUserInterface


## Namespace
`OpenFGA\Models`

## Implements
* [ModelInterface](Models/ModelInterface.md)
* JsonSerializable



## Methods
### getId


```php
public function getId(): string
```

Get the object identifier in the userset reference. This represents the specific object instance that the userset refers to. For example, in &quot;group:eng#member&quot;, this would return &quot;eng&quot;.


#### Returns
string
 The object identifier

### getRelation


```php
public function getRelation(): string
```

Get the relation name in the userset reference. This represents the specific relation on the referenced object that defines the userset. For example, in &quot;group:eng#member&quot;, this would return &quot;member&quot;.


#### Returns
string
 The relation name

### getType


```php
public function getType(): string
```

Get the object type in the userset reference. This represents the type of object that the userset refers to. For example, in &quot;group:eng#member&quot;, this would return &quot;group&quot;.


#### Returns
string
 The object type

### jsonSerialize


```php
public function jsonSerialize(): array
```



#### Returns
array

