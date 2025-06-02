# CreateAuthorizationModelRequestInterface

Interface for creating new authorization models in OpenFGA. This interface defines the contract for requests that create new authorization models within an OpenFGA store. Authorization models define the relationship types, object types, and access control rules that govern how permissions are evaluated in your application. An authorization model consists of: - **Type definitions**: Define object types and their allowed relationships - **Conditions**: Define conditional logic for dynamic authorization - **Schema version**: Specifies the model definition language version Authorization models are versioned, allowing you to evolve your permission system over time while maintaining compatibility. Each new model receives a unique ID that can be used to ensure consistent permission evaluation even as the model evolves. Key capabilities include: - Defining object types (documents, folders, organizations, etc.) - Specifying relationship types (owner, editor, viewer, member, etc.) - Creating inheritance and permission hierarchies - Implementing conditional authorization with runtime context - Supporting complex authorization patterns like RBAC, ABAC, and ReBAC

## Namespace

`OpenFGA\Requests`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Requests/CreateAuthorizationModelRequestInterface.php)

## Implements

* [`RequestInterface`](RequestInterface.md)

## Related Classes

* [CreateAuthorizationModelResponseInterface](Responses/CreateAuthorizationModelResponseInterface.md) (response)

* [CreateAuthorizationModelRequest](Requests/CreateAuthorizationModelRequest.md) (implementation)

## Methods

#### getConditions

```php
public function getConditions(): ConditionsInterface<ConditionInterface>|null

```

Get the conditional rules for the authorization model. Returns a collection of conditions that define dynamic authorization logic based on runtime context. Conditions allow for sophisticated access control scenarios such as time-based access, location restrictions, resource attributes, or custom business logic. Conditions are referenced by name within type definitions and evaluated at permission check time using contextual data provided in authorization requests. They enable attribute-based access control (ABAC) patterns within the relationship-based authorization framework.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/CreateAuthorizationModelRequestInterface.php#L61)

#### Returns

[`ConditionsInterface`](Models/Collections/ConditionsInterface.md)&lt;[`ConditionInterface`](Models/ConditionInterface.md)&gt; &#124; `null` — Collection of conditional rules for dynamic authorization, or null if no conditions are defined

#### getRequest

```php
public function getRequest(StreamFactoryInterface $streamFactory): RequestContext

```

Build a request context for HTTP execution. Transforms the request object into a standardized HTTP request context that can be executed by the OpenFGA HTTP client. This method handles all aspects of request preparation including parameter serialization, URL construction, header configuration, and body stream creation. The method validates that all required parameters are present and properly formatted, serializes complex objects to JSON, constructs the appropriate API endpoint URL, and creates the necessary HTTP message body streams.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/RequestInterface.php#L57)

#### Parameters

| Name | Type | Description |

|------|------|-------------|

| `$streamFactory` | `StreamFactoryInterface` | PSR-7 stream factory for creating request body streams from serialized data |

#### Returns

`RequestContext` — The prepared request context containing HTTP method, URL, headers, and body ready for execution

#### getSchemaVersion

```php
public function getSchemaVersion(): SchemaVersion

```

Get the schema version for the authorization model. Specifies which version of the OpenFGA modeling language should be used to interpret the authorization model definition. Different schema versions support different features and syntax, allowing OpenFGA to evolve while maintaining backward compatibility. The schema version determines: - Available relationship operators and syntax - Supported conditional expression features - Type definition validation rules - API compatibility and behavior

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/CreateAuthorizationModelRequestInterface.php#L79)

#### Returns

[`SchemaVersion`](Models/Enums/SchemaVersion.md) — The modeling language schema version for this authorization model

#### getStore

```php
public function getStore(): string

```

Get the store ID where the authorization model will be created. Identifies the OpenFGA store that will contain the new authorization model. Each store can have multiple model versions, allowing you to evolve your authorization schema over time while maintaining access to previous versions for consistency and rollback scenarios.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/CreateAuthorizationModelRequestInterface.php#L91)

#### Returns

`string` — The store ID where the authorization model will be created

#### getTypeDefinitions

```php
public function getTypeDefinitions(): TypeDefinitionsInterface<TypeDefinitionInterface>

```

Get the type definitions for the authorization model. Returns a collection of type definitions that specify the object types and their allowed relationships within the authorization model. Type definitions form the core schema that defines what objects exist in your system and how they can be related to users and other objects. Each type definition includes: - Object type name (e.g., &quot;document&quot;, &quot;folder&quot;, &quot;organization&quot;) - Allowed relationships (e.g., &quot;owner&quot;, &quot;editor&quot;, &quot;viewer&quot;) - Relationship inheritance and computation rules - References to conditional logic for dynamic authorization

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/CreateAuthorizationModelRequestInterface.php#L109)

#### Returns

[`TypeDefinitionsInterface`](Models/Collections/TypeDefinitionsInterface.md)&lt;[`TypeDefinitionInterface`](Models/TypeDefinitionInterface.md)&gt; — Collection of object type definitions that define the authorization schema
