# SchemaRegistryInterface

Registry for managing schema definitions in the OpenFGA system. This interface provides a centralized storage and retrieval system for schema objects, enabling registration and lookup of schemas by class name. The registry serves as the single source of truth for all schema definitions used throughout the OpenFGA SDK. The registry supports dynamic schema registration during runtime and provides factory methods for creating new schema builders. This centralized approach ensures consistent validation behavior across all OpenFGA model objects and API responses.

## Table of Contents

- [Namespace](#namespace)
- [Source](#source)
- [Related Classes](#related-classes)

## Namespace

`OpenFGA\Schemas`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Schemas/SchemaRegistryInterface.php)

## Related Classes

- [SchemaRegistry](Schemas/SchemaRegistry.md) (implementation)
