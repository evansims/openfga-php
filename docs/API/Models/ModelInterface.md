# ModelInterface

Base interface for all OpenFGA model objects. This interface establishes the foundation for all domain models in the OpenFGA SDK, ensuring consistent behavior for serialization and schema validation across the entire model hierarchy. All OpenFGA models implement this interface to provide uniform JSON serialization capabilities and schema-based validation. Models in the OpenFGA ecosystem represent various authorization concepts: - Authorization models that define permission structures - Relationship tuples that establish actual relationships - Stores that contain authorization data - Users, objects, and conditions used in authorization decisions The schema system enables robust type checking, validation, and transformation of data throughout the SDK, ensuring data integrity and API compatibility.

## Namespace

`OpenFGA\Models`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Models/ModelInterface.php)

## Implements

* `JsonSerializable`

## Methods

#### jsonSerialize

```php
public function jsonSerialize()

```

