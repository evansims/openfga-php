# AssertionTupleKeyInterface


## Namespace
`OpenFGA\Models`

## Implements
* [ModelInterface](Models/ModelInterface.md)
* JsonSerializable



## Methods
### getObject


```php
public function getObject(): string
```

Get the object being tested in the assertion. This represents the resource or entity that the assertion is testing access to. In assertion testing, this is the object part of the tuple being validated against the authorization model.


#### Returns
string
 The object identifier being tested

### getRelation


```php
public function getRelation(): string
```

Get the relation being tested in the assertion. This represents the type of relationship or permission being tested in the assertion. It defines what kind of access is being validated between the user and object.


#### Returns
string
 The relation name being tested

### getUser


```php
public function getUser(): string
```

Get the user being tested in the assertion. This represents the subject (user, group, role, etc.) whose access is being tested in the assertion. It&#039;s the entity for which we&#039;re validating whether they have the specified relation to the object.


#### Returns
string
 The user identifier being tested

### jsonSerialize


```php
public function jsonSerialize(): array
```



#### Returns
array

