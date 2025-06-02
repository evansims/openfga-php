# SchemaRegistryInterface

Registry for managing schema definitions in the OpenFGA system. This interface provides a centralized storage and retrieval system for schema objects, enabling registration and lookup of schemas by class name. The registry serves as the single source of truth for all schema definitions used throughout the OpenFGA SDK. The registry supports dynamic schema registration during runtime and provides factory methods for creating new schema builders. This centralized approach ensures consistent validation behavior across all OpenFGA model objects and API responses.

## Namespace

`OpenFGA\Schema`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Schema/SchemaRegistryInterface.php)

## Related Classes

* [SchemaRegistry](Schema/SchemaRegistry.md) (implementation)
