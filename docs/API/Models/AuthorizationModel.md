# AuthorizationModel

Defines the authorization rules and relationships for your application. An AuthorizationModel is the core configuration that tells OpenFGA how permissions work in your system. It defines object types (like documents, folders), relationships (like owner, editor, viewer), and the rules for how those relationships grant access. Think of this as your application&#039;s &quot;permission blueprint&quot; - it describes all the ways users can be related to objects and what those relationships mean for access control decisions.

<details>
<summary><strong>Table of Contents</strong></summary>

- [Namespace](#namespace)
- [Source](#source)
- [Implements](#implements)
- [Related Classes](#related-classes)
- [Constants](#constants)
- [Methods](#methods)

- [`dsl()`](#dsl)
  - [`getConditions()`](#getconditions)
  - [`getId()`](#getid)
  - [`getSchemaVersion()`](#getschemaversion)
  - [`getTypeDefinitions()`](#gettypedefinitions)
  - [`jsonSerialize()`](#jsonserialize)
  - [`schema()`](#schema)

</details>

## Namespace

`OpenFGA\Models`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Models/AuthorizationModel.php)

## Implements

- [`AuthorizationModelInterface`](AuthorizationModelInterface.md)
- `JsonSerializable`
- [`ModelInterface`](ModelInterface.md)

## Related Classes

- [AuthorizationModelInterface](Models/AuthorizationModelInterface.md) (interface)
- [AuthorizationModels](Models/Collections/AuthorizationModels.md) (collection)

## Constants

| Name            | Value                | Description |
| --------------- | -------------------- | ----------- |
| `OPENAPI_MODEL` | `AuthorizationModel` |             |

## Methods

### dsl

```php
public function dsl(): string

```

Generate a DSL (Domain Specific Language) representation of this authorization model. The DSL provides a human-readable, text-based format for expressing authorization models that is easier to understand, review, and modify than raw JSON. The DSL format uses a syntax similar to configuration languages, making it accessible to both developers and non-technical stakeholders who need to understand or modify permission structures. The DSL representation includes: - Type definitions with their relations and inheritance rules - Condition expressions and parameters - Human-readable relation definitions and computed permissions - Comments and formatting that enhance comprehension This format is particularly valuable for: - Documentation and code reviews - Version control and change tracking - Model debugging and testing - Administrative interfaces and tooling

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/AuthorizationModel.php#L73)

#### Returns

`string` — The authorization model expressed in OpenFGA DSL format for human readability

### getConditions

```php
public function getConditions(): ?OpenFGA\Models\Collections\ConditionsInterface

```

Get the conditions defined in this authorization model. Conditions enable attribute-based access control (ABAC) by allowing relationships to be conditional based on runtime context and parameters. When conditions are present in the model, they can be referenced in relationship tuples to create dynamic authorization rules that consider factors such as: - Time-based restrictions (business hours, expiration dates) - Resource attributes (document classification, geographic location) - User context (department, role level, current project) - Environmental factors (IP address, device type, authentication method) Conditions are evaluated during authorization checks, and relationships with conditions are only considered valid when the condition evaluates to true given the current context parameters.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/AuthorizationModel.php#L82)

#### Returns

[`ConditionsInterface`](Models/Collections/ConditionsInterface.md) &#124; `null` — The collection of reusable conditions defined in this model, or null if no conditions are defined

### getId

```php
public function getId(): string

```

Get the unique identifier for this authorization model. The model ID serves as a unique identifier for this specific version of the authorization model within the OpenFGA system. This ID is generated by the OpenFGA service when the model is created and is used to: - Reference this model in API operations - Track model versions and deployment history - Ensure consistency across different services and environments - Enable model rollbacks and A/B testing scenarios Each model ID is unique within a store, allowing multiple model versions to coexist and enabling gradual migration between authorization schemas.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/AuthorizationModel.php#L91)

#### Returns

`string` — The globally unique identifier for this authorization model version

### getSchemaVersion

```php
public function getSchemaVersion(): OpenFGA\Models\Enums\SchemaVersion

```

Get the schema version of this authorization model. The schema version indicates which version of the OpenFGA authorization model specification this model conforms to. Different schema versions may support different features, syntax variations, or behavioral semantics. This version information ensures: - Proper interpretation of model structures and syntax - Compatibility checking between client and server versions - Feature availability and validation logic - Migration paths between different OpenFGA versions The schema version enables the OpenFGA service to correctly parse and execute authorization logic according to the appropriate specification.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/AuthorizationModel.php#L100)

#### Returns

[`SchemaVersion`](Models/Enums/SchemaVersion.md) — The schema version enumeration indicating the model format specification

### getTypeDefinitions

```php
public function getTypeDefinitions(): OpenFGA\Models\Collections\TypeDefinitionsInterface

```

Get the type definitions that define the structure of this authorization model. Type definitions form the core structure of an authorization model by specifying: - The types of objects that exist in your system (documents, users, folders, etc.) - The relationships that can exist between users and those object types - How permissions are computed and inherited through relationship chains - The rules that govern complex authorization scenarios Each type definition includes relations that describe the various ways users can be associated with objects of that type. Relations can be direct (simple assignments) or computed (derived from other relationships), enabling sophisticated permission hierarchies and inheritance patterns. Type definitions are the foundation that OpenFGA uses to understand your domain model and execute authorization queries efficiently.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/AuthorizationModel.php#L109)

#### Returns

[`TypeDefinitionsInterface`](Models/Collections/TypeDefinitionsInterface.md) — The collection of type definitions that structure this authorization model

### jsonSerialize

```php
public function jsonSerialize(): array

```

Serialize the authorization model for JSON encoding. This method prepares the complete authorization model data for API communication with the OpenFGA service, converting all components into the format specified by the OpenFGA API. The serialization includes: - Model identification and versioning information - Complete type definitions with relations and metadata - Optional conditions with expressions and parameters - All nested structures properly formatted for JSON transmission The resulting structure is suitable for creating new authorization models, updating existing models, or storing model definitions in external systems. All data is formatted according to the OpenFGA API specification to ensure compatibility and correct interpretation by the authorization service.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/AuthorizationModel.php#L118)

#### Returns

`array`

### schema

*<small>Implements Models\AuthorizationModelInterface</small>*

```php
public function schema(): SchemaInterface

```

Get the schema definition for this model. This method returns the schema that defines the structure, validation rules, and serialization behavior for this model class. The schema is used for data validation, transformation, and ensuring consistency across API operations with the OpenFGA service. Each model&#039;s schema defines: - Required and optional properties - Data types and format constraints - Nested object relationships - Validation rules and business logic constraints The schema system enables the SDK to automatically validate incoming data, transform between different representations, and ensure compliance with the OpenFGA API specification.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/ModelInterface.php#L52)

#### Returns

`SchemaInterface` — The schema definition containing validation rules and property specifications for this model
