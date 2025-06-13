# CreateAuthorizationModelRequest

Request for creating a new authorization model in OpenFGA. Authorization models define the permission structure for your application, including object types, relationships, and how permissions are computed. Models are immutable once created and identified by a unique ID.

## Table of Contents

* [Namespace](#namespace)
* [Source](#source)
* [Implements](#implements)
* [Related Classes](#related-classes)
* [Methods](#methods)

* [List Operations](#list-operations)
    * [`getConditions()`](#getconditions)
    * [`getRequest()`](#getrequest)
    * [`getSchemaVersion()`](#getschemaversion)
    * [`getStore()`](#getstore)
    * [`getTypeDefinitions()`](#gettypedefinitions)

## Namespace

`OpenFGA\Requests`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Requests/CreateAuthorizationModelRequest.php)

## Implements

* [`CreateAuthorizationModelRequestInterface`](CreateAuthorizationModelRequestInterface.md)
* [`RequestInterface`](RequestInterface.md)

## Related Classes

* [CreateAuthorizationModelResponse](Responses/CreateAuthorizationModelResponse.md) (response)
* [CreateAuthorizationModelRequestInterface](Requests/CreateAuthorizationModelRequestInterface.md) (interface)

## Methods

#### getConditions

```php
public function getConditions(): ?OpenFGA\Models\Collections\ConditionsInterface

```

Get the conditional rules for the authorization model. Returns a collection of conditions that define dynamic authorization logic based on runtime context. Conditions allow for sophisticated access control scenarios such as time-based access, location restrictions, resource attributes, or custom business logic. Conditions are referenced by name within type definitions and evaluated at permission check time using contextual data provided in authorization requests. They enable attribute-based access control (ABAC) patterns within the relationship-based authorization framework.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/CreateAuthorizationModelRequest.php#L58)

#### Returns

[`ConditionsInterface`](Models/Collections/ConditionsInterface.md) &#124; `null` — Collection of conditional rules for dynamic authorization, or null if no conditions are defined

#### getRequest

```php
public function getRequest(Psr\Http\Message\StreamFactoryInterface $streamFactory): OpenFGA\Network\RequestContext

```

Build a request context for HTTP execution. Transforms the request object into a standardized HTTP request context that can be executed by the OpenFGA HTTP client. This method handles all aspects of request preparation including parameter serialization, URL construction, header configuration, and body stream creation. The method validates that all required parameters are present and properly formatted, serializes complex objects to JSON, constructs the appropriate API endpoint URL, and creates the necessary HTTP message body streams.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/CreateAuthorizationModelRequest.php#L69)

#### Parameters

| Name             | Type                     | Description                                                                 |
| ---------------- | ------------------------ | --------------------------------------------------------------------------- |
| `$streamFactory` | `StreamFactoryInterface` | PSR-7 stream factory for creating request body streams from serialized data |

#### Returns

[`RequestContext`](Network/RequestContext.md) — The prepared request context containing HTTP method, URL, headers, and body ready for execution

#### getSchemaVersion

```php
public function getSchemaVersion(): OpenFGA\Models\Enums\SchemaVersion

```

Get the schema version for the authorization model. Specifies which version of the OpenFGA modeling language should be used to interpret the authorization model definition. Different schema versions support different features and syntax, allowing OpenFGA to evolve while maintaining backward compatibility. The schema version determines: - Available relationship operators and syntax - Supported conditional expression features - Type definition validation rules - API compatibility and behavior

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/CreateAuthorizationModelRequest.php#L97)

#### Returns

[`SchemaVersion`](Models/Enums/SchemaVersion.md) — The modeling language schema version for this authorization model

#### getStore

```php
public function getStore(): string

```

Get the store ID where the authorization model will be created. Identifies the OpenFGA store that will contain the new authorization model. Each store can have multiple model versions, allowing you to evolve your authorization schema over time while maintaining access to previous versions for consistency and rollback scenarios.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/CreateAuthorizationModelRequest.php#L106)

#### Returns

`string` — The store ID where the authorization model will be created

#### getTypeDefinitions

```php
public function getTypeDefinitions(): OpenFGA\Models\Collections\TypeDefinitionsInterface

```

Get the type definitions for the authorization model. Returns a collection of type definitions that specify the object types and their allowed relationships within the authorization model. Type definitions form the core schema that defines what objects exist in your system and how they can be related to users and other objects. Each type definition includes: - Object type name (for example &quot;document,&quot; &quot;folder,&quot; &quot;organization&quot;) - Allowed relationships (for example &quot;owner,&quot; &quot;editor,&quot; &quot;viewer&quot;) - Relationship inheritance and computation rules - References to conditional logic for dynamic authorization

[View source](https://github.com/evansims/openfga-php/blob/main/src/Requests/CreateAuthorizationModelRequest.php#L115)

#### Returns

[`TypeDefinitionsInterface`](Models/Collections/TypeDefinitionsInterface.md) — Collection of object type definitions that define the authorization schema
