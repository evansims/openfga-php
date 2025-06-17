# Client

OpenFGA Client implementation for relationship-based access control operations. This client provides a complete implementation of the OpenFGA API, supporting all core operations including store management, authorization model configuration, relationship tuple operations, and authorization checks. The client uses PSR-7, PSR-17 and PSR-18 HTTP standards and implements the Result pattern for error handling. The client supports multiple authentication methods including OAuth 2.0 Client Credentials flow and pre-shared key authentication, with automatic token management and retry capabilities for reliable operation.

<details>
<summary><strong>Table of Contents</strong></summary>

- [Namespace](#namespace)
- [Source](#source)
- [Implements](#implements)
- [Related Classes](#related-classes)
- [Constants](#constants)
- [Methods](#methods)

- [`batchCheck()`](#batchcheck)
  - [`check()`](#check)
  - [`createAuthorizationModel()`](#createauthorizationmodel)
  - [`createStore()`](#createstore)
  - [`deleteStore()`](#deletestore)
  - [`dsl()`](#dsl)
  - [`expand()`](#expand)
  - [`getAuthorizationModel()`](#getauthorizationmodel)
  - [`getLanguage()`](#getlanguage)
  - [`getLanguageEnum()`](#getlanguageenum)
  - [`getLastRequest()`](#getlastrequest)
  - [`getLastResponse()`](#getlastresponse)
  - [`getStore()`](#getstore)
  - [`listAuthorizationModels()`](#listauthorizationmodels)
  - [`listObjects()`](#listobjects)
  - [`listStores()`](#liststores)
  - [`listTupleChanges()`](#listtuplechanges)
  - [`listUsers()`](#listusers)
  - [`readAssertions()`](#readassertions)
  - [`readTuples()`](#readtuples)
  - [`streamedListObjects()`](#streamedlistobjects)
  - [`writeAssertions()`](#writeassertions)
  - [`writeTuples()`](#writetuples)

</details>

## Namespace

`OpenFGA`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Client.php)

## Implements

- [`ClientInterface`](ClientInterface.md)

## Related Classes

- [ClientInterface](ClientInterface.md) (interface)

## Constants

| Name      | Value   | Description                         |
| --------- | ------- | ----------------------------------- |
| `VERSION` | `1.4.0` | The version of the OpenFGA PHP SDK. |

## Methods

### batchCheck

```php
public function batchCheck(
    OpenFGA\Models\StoreInterface|string $store,
    OpenFGA\Models\AuthorizationModelInterface|string $model,
    OpenFGA\Models\Collections\BatchCheckItemsInterface $checks,
): OpenFGA\Results\FailureInterface|OpenFGA\Results\SuccessInterface

```

Performs multiple authorization checks in a single batch request. This method allows checking multiple user-object relationships simultaneously for better performance when multiple authorization decisions are needed. Each check in the batch has a correlation ID to map results back to the original requests. The batch check operation supports the same features as individual checks: contextual tuples, custom contexts, and detailed error information for each check.

**Batch checking multiple permissions efficiently:**

```php
$checks = new BatchCheckItems([
    new BatchCheckItem(
        tupleKey: new TupleKey('user:anne', 'viewer', 'document:budget'),
        correlationId: 'check-anne-viewer'
    ),
    new BatchCheckItem(
        tupleKey: new TupleKey('user:bob', 'editor', 'document:budget'),
        correlationId: 'check-bob-editor'
    ),
    new BatchCheckItem(
        tupleKey: new TupleKey('user:charlie', 'owner', 'document:roadmap'),
        correlationId: 'check-charlie-owner'
    ),
]);

$result = $client->batchCheck(
    store: 'store-id',
    model: 'model-id',
    checks: $checks
);

if ($result->success()) {
    $responses = $result->value()->getResults();
    foreach ($responses as $response) {
        echo $response->getCorrelationId() . ': ' .
             ($response->getAllowed() ? 'ALLOWED' : 'DENIED') . "\n";
    }
}

```

[View source](https://github.com/evansims/openfga-php/blob/main/src/Client.php#L123)

#### Parameters

| Name      | Type                                                                                   | Description                    |
| --------- | -------------------------------------------------------------------------------------- | ------------------------------ |
| `$store`  | [`StoreInterface`](Models/StoreInterface.md) &#124; `string`                           | The store to check against     |
| `$model`  | [`AuthorizationModelInterface`](Models/AuthorizationModelInterface.md) &#124; `string` | The authorization model to use |
| `$checks` | [`BatchCheckItemsInterface`](Models/Collections/BatchCheckItemsInterface.md)           | The batch check items          |

#### Returns

[`FailureInterface`](Results/FailureInterface.md) &#124; [`SuccessInterface`](Results/SuccessInterface.md) — The batch check results

### check

```php
public function check(
    OpenFGA\Models\StoreInterface|string $store,
    OpenFGA\Models\AuthorizationModelInterface|string $model,
    OpenFGA\Models\TupleKeyInterface $tuple,
    ?bool $trace = NULL,
    ?object $context = NULL,
    ?OpenFGA\Models\Collections\TupleKeysInterface $contextualTuples = NULL,
    ?OpenFGA\Models\Enums\Consistency $consistency = NULL,
): OpenFGA\Results\FailureInterface|OpenFGA\Results\SuccessInterface

```

Checks if a user has a specific relationship with an object. Performs an authorization check to determine if a user has a particular relationship with an object based on the configured authorization model. This is the core operation for making authorization decisions in OpenFGA.

**Basic permission check:**

```php
$result = $client->check(
    store: 'store-id',
    model: 'model-id',
    tuple: new TupleKey('user:anne', 'reader', 'document:budget')
);

if ($result->success()) {
    $allowed = $result->value()->getAllowed();
    if ($allowed) {
        // User has permission
    }
}

```

**Check with contextual tuples:**

```php
$contextualTuples = new TupleKeys([
    new TupleKey('user:anne', 'member', 'team:finance')
]);

$result = $client->check(
    store: 'store-id',
    model: 'model-id',
    tuple: new TupleKey('user:anne', 'reader', 'document:budget'),
    contextualTuples: $contextualTuples
);

```

[View source](https://github.com/evansims/openfga-php/blob/main/src/Client.php#L145)

#### Parameters

| Name                | Type                                                                                   | Description                                 |
| ------------------- | -------------------------------------------------------------------------------------- | ------------------------------------------- |
| `$store`            | [`StoreInterface`](Models/StoreInterface.md) &#124; `string`                           | The store to check against                  |
| `$model`            | [`AuthorizationModelInterface`](Models/AuthorizationModelInterface.md) &#124; `string` | The authorization model to use              |
| `$tuple`            | [`TupleKeyInterface`](Models/TupleKeyInterface.md)                                     | The relationship to check                   |
| `$trace`            | `bool` &#124; `null`                                                                   | Whether to include a trace in the response  |
| `$context`          | `object` &#124; `null`                                                                 | Additional context for the check            |
| `$contextualTuples` | [`TupleKeysInterface`](Models/Collections/TupleKeysInterface.md) &#124; `null`         | Additional tuples for contextual evaluation |
| `$consistency`      | [`Consistency`](Models/Enums/Consistency.md) &#124; `null`                             | Override the default consistency level      |

#### Returns

[`FailureInterface`](Results/FailureInterface.md) &#124; [`SuccessInterface`](Results/SuccessInterface.md) — Success contains CheckResponseInterface, Failure contains Throwable

### createAuthorizationModel

```php
public function createAuthorizationModel(
    OpenFGA\Models\StoreInterface|string $store,
    OpenFGA\Models\Collections\TypeDefinitionsInterface $typeDefinitions,
    ?OpenFGA\Models\Collections\ConditionsInterface $conditions = NULL,
    OpenFGA\Models\Enums\SchemaVersion $schemaVersion = OpenFGA\Models\Enums\SchemaVersion::V1_1,
): OpenFGA\Results\FailureInterface|OpenFGA\Results\SuccessInterface

```

Creates a new authorization model with the given type definitions and conditions. Authorization models define the permission structure for your application, including object types, relationships, and how permissions are computed. Models are immutable once created and identified by a unique ID.

**Creating a document authorization model with DSL (recommended):**

```php
// Using DSL is usually easier than manually building type definitions
$dsl = '
    model
      schema 1.1

    type user

    type document
      relations
        define owner: [user]
        define editor: [user] or owner
        define viewer: [user] or editor
';

$authModel = $client->dsl($dsl)->unwrap();
$result = $client->createAuthorizationModel(
    store: 'store-id',
    typeDefinitions: $authModel->getTypeDefinitions()
);

if ($result->success()) {
    $modelId = $result->value()->getAuthorizationModelId();
    echo "Created model: {$modelId}";
}

```

[View source](https://github.com/evansims/openfga-php/blob/main/src/Client.php#L175)

#### Parameters

| Name               | Type                                                                             | Description                              |
| ------------------ | -------------------------------------------------------------------------------- | ---------------------------------------- |
| `$store`           | [`StoreInterface`](Models/StoreInterface.md) &#124; `string`                     | The store to create the model in         |
| `$typeDefinitions` | [`TypeDefinitionsInterface`](Models/Collections/TypeDefinitionsInterface.md)     | The type definitions for the model       |
| `$conditions`      | [`ConditionsInterface`](Models/Collections/ConditionsInterface.md) &#124; `null` | The conditions for the model             |
| `$schemaVersion`   | [`SchemaVersion`](Models/Enums/SchemaVersion.md)                                 | The schema version to use (default: 1.1) |

#### Returns

[`FailureInterface`](Results/FailureInterface.md) &#124; [`SuccessInterface`](Results/SuccessInterface.md) — Success contains CreateAuthorizationModelResponseInterface, Failure contains Throwable

### createStore

```php
public function createStore(string $name): OpenFGA\Results\FailureInterface|OpenFGA\Results\SuccessInterface

```

Creates a new store with the given name. Stores provide data isolation for different applications or environments. Each store maintains its own authorization models, relationship tuples, and provides complete separation from other stores.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Client.php#L198)

#### Parameters

| Name    | Type     | Description                |
| ------- | -------- | -------------------------- |
| `$name` | `string` | The name for the new store |

#### Returns

[`FailureInterface`](Results/FailureInterface.md) &#124; [`SuccessInterface`](Results/SuccessInterface.md) — Success contains CreateStoreResponseInterface, Failure contains Throwable

### deleteStore

```php
public function deleteStore(
    OpenFGA\Models\StoreInterface|string $store,
): OpenFGA\Results\FailureInterface|OpenFGA\Results\SuccessInterface

```

Deletes a store.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Client.php#L216)

#### Parameters

| Name     | Type                                                         | Description         |
| -------- | ------------------------------------------------------------ | ------------------- |
| `$store` | [`StoreInterface`](Models/StoreInterface.md) &#124; `string` | The store to delete |

#### Returns

[`FailureInterface`](Results/FailureInterface.md) &#124; [`SuccessInterface`](Results/SuccessInterface.md) — Success contains DeleteStoreResponseInterface, Failure contains Throwable

### dsl

```php
public function dsl(string $dsl): OpenFGA\Results\FailureInterface|OpenFGA\Results\SuccessInterface

```

Parses a DSL string and returns an AuthorizationModel. The Domain Specific Language (DSL) provides a human-readable way to define authorization models using intuitive syntax for relationships and permissions. This method converts DSL text into a structured authorization model object.

**Parse a complete authorization model from DSL:**

```php
$dsl = '
    model
      schema 1.1

    type user

    type organization
      relations
        define member: [user]

    type document
      relations
        define owner: [user]
        define editor: [user, organization#member] or owner
        define viewer: [user, organization#member] or editor
';

$result = $client->dsl($dsl);

if ($result->success()) {
    $authModel = $result->value();
    echo "Parsed model with " . count($authModel->getTypeDefinitions()) . " types";
}

```

[View source](https://github.com/evansims/openfga-php/blob/main/src/Client.php#L235)

#### Parameters

| Name   | Type     | Description             |
| ------ | -------- | ----------------------- |
| `$dsl` | `string` | The DSL string to parse |

#### Returns

[`FailureInterface`](Results/FailureInterface.md) &#124; [`SuccessInterface`](Results/SuccessInterface.md) — Success contains AuthorizationModelInterface, Failure contains Throwable

### expand

```php
public function expand(
    OpenFGA\Models\StoreInterface|string $store,
    OpenFGA\Models\TupleKeyInterface $tuple,
    ?OpenFGA\Models\AuthorizationModelInterface|string|null $model = NULL,
    ?OpenFGA\Models\Collections\TupleKeysInterface $contextualTuples = NULL,
    ?OpenFGA\Models\Enums\Consistency $consistency = NULL,
): OpenFGA\Results\FailureInterface|OpenFGA\Results\SuccessInterface

```

Expands a relationship tuple to show all users that have the relationship.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Client.php#L257)

#### Parameters

| Name                | Type                                                                                                               | Description                                 |
| ------------------- | ------------------------------------------------------------------------------------------------------------------ | ------------------------------------------- |
| `$store`            | [`StoreInterface`](Models/StoreInterface.md) &#124; `string`                                                       | The store containing the tuple              |
| `$tuple`            | [`TupleKeyInterface`](Models/TupleKeyInterface.md)                                                                 | The tuple to expand                         |
| `$model`            | [`AuthorizationModelInterface`](Models/AuthorizationModelInterface.md) &#124; `null` &#124; `string` &#124; `null` | The authorization model to use              |
| `$contextualTuples` | [`TupleKeysInterface`](Models/Collections/TupleKeysInterface.md) &#124; `null`                                     | Additional tuples for contextual evaluation |
| `$consistency`      | [`Consistency`](Models/Enums/Consistency.md) &#124; `null`                                                         | Override the default consistency level      |

#### Returns

[`FailureInterface`](Results/FailureInterface.md) &#124; [`SuccessInterface`](Results/SuccessInterface.md) — Success contains ExpandResponseInterface, Failure contains Throwable

### getAuthorizationModel

```php
public function getAuthorizationModel(
    OpenFGA\Models\StoreInterface|string $store,
    OpenFGA\Models\AuthorizationModelInterface|string $model,
): OpenFGA\Results\FailureInterface|OpenFGA\Results\SuccessInterface

```

Retrieves an authorization model by ID.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Client.php#L283)

#### Parameters

| Name     | Type                                                                                   | Description                    |
| -------- | -------------------------------------------------------------------------------------- | ------------------------------ |
| `$store` | [`StoreInterface`](Models/StoreInterface.md) &#124; `string`                           | The store containing the model |
| `$model` | [`AuthorizationModelInterface`](Models/AuthorizationModelInterface.md) &#124; `string` | The model to retrieve          |

#### Returns

[`FailureInterface`](Results/FailureInterface.md) &#124; [`SuccessInterface`](Results/SuccessInterface.md) — Success contains GetAuthorizationModelResponseInterface, Failure contains Throwable

### getLanguage

```php
public function getLanguage(): string

```

Get the configured language for i18n translations.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Client.php#L302)

#### Returns

`string` — The configured language code

### getLanguageEnum

```php
public function getLanguageEnum(): Language

```

Get the configured language enum for type-safe access. Returns the Language enum representing the currently configured language, providing access to language metadata and type-safe operations.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Client.php#L315)

#### Returns

[`Language`](Language.md) — The configured language enum

### getLastRequest

```php
public function getLastRequest(): ?Psr\Http\Message\RequestInterface

```

Retrieves the last HTTP request made by the client.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Client.php#L326)

#### Returns

`Psr\Http\Message\RequestInterface` &#124; `null`

### getLastResponse

```php
public function getLastResponse(): ?Psr\Http\Message\ResponseInterface

```

Retrieves the last HTTP response received by the client.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Client.php#L339)

#### Returns

`Psr\Http\Message\ResponseInterface` &#124; `null`

### getStore

```php
public function getStore(
    OpenFGA\Models\StoreInterface|string $store,
): OpenFGA\Results\FailureInterface|OpenFGA\Results\SuccessInterface

```

Retrieves store details by ID.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Client.php#L352)

#### Parameters

| Name     | Type                                                         | Description           |
| -------- | ------------------------------------------------------------ | --------------------- |
| `$store` | [`StoreInterface`](Models/StoreInterface.md) &#124; `string` | The store to retrieve |

#### Returns

[`FailureInterface`](Results/FailureInterface.md) &#124; [`SuccessInterface`](Results/SuccessInterface.md) — Success contains GetStoreResponseInterface, Failure contains Throwable

### listAuthorizationModels

```php
public function listAuthorizationModels(
    OpenFGA\Models\StoreInterface|string $store,
    ?string $continuationToken = NULL,
    ?int $pageSize = NULL,
): OpenFGA\Results\FailureInterface|OpenFGA\Results\SuccessInterface

```

Lists authorization models in a store with pagination.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Client.php#L371)

#### Parameters

| Name                 | Type                                                         | Description                                           |
| -------------------- | ------------------------------------------------------------ | ----------------------------------------------------- |
| `$store`             | [`StoreInterface`](Models/StoreInterface.md) &#124; `string` | The store to list models from                         |
| `$continuationToken` | `string` &#124; `null`                                       | Token for pagination                                  |
| `$pageSize`          | `int` &#124; `null`                                          | Maximum number of models to return (must be positive) |

#### Returns

[`FailureInterface`](Results/FailureInterface.md) &#124; [`SuccessInterface`](Results/SuccessInterface.md) — Success contains ListAuthorizationModelsResponseInterface, Failure contains Throwable

### listObjects

```php
public function listObjects(
    OpenFGA\Models\StoreInterface|string $store,
    OpenFGA\Models\AuthorizationModelInterface|string $model,
    string $type,
    string $relation,
    string $user,
    ?object $context = NULL,
    ?OpenFGA\Models\Collections\TupleKeysInterface $contextualTuples = NULL,
    ?OpenFGA\Models\Enums\Consistency $consistency = NULL,
): OpenFGA\Results\FailureInterface|OpenFGA\Results\SuccessInterface

```

Lists objects that have a specific relationship with a user.

**List all documents a user can view:**

```php
$result = $client->listObjects(
    store: 'store-id',
    model: 'model-id',
    type: 'document',
    relation: 'viewer',
    user: 'user:anne'
);

if ($result->success()) {
    $objects = $result->value()->getObjects();
    echo "Anne can view " . count($objects) . " documents:\n";
    foreach ($objects as $object) {
        echo "- {$object}\n";
    }
}

```

**List objects with contextual evaluation:**

```php
// Check what documents anne can edit, considering her team membership
$contextualTuples = new TupleKeys([
    new TupleKey('user:anne', 'member', 'team:engineering')
]);

$result = $client->listObjects(
    store: 'store-id',
    model: 'model-id',
    type: 'document',
    relation: 'editor',
    user: 'user:anne',
    contextualTuples: $contextualTuples
);
/

```

[View source](https://github.com/evansims/openfga-php/blob/main/src/Client.php#L396)

#### Parameters

| Name                | Type                                                                                   | Description                                 |
| ------------------- | -------------------------------------------------------------------------------------- | ------------------------------------------- |
| `$store`            | [`StoreInterface`](Models/StoreInterface.md) &#124; `string`                           | The store to query                          |
| `$model`            | [`AuthorizationModelInterface`](Models/AuthorizationModelInterface.md) &#124; `string` | The authorization model to use              |
| `$type`             | `string`                                                                               | The type of objects to list                 |
| `$relation`         | `string`                                                                               | The relationship to check                   |
| `$user`             | `string`                                                                               | The user to check relationships for         |
| `$context`          | `object` &#124; `null`                                                                 | Additional context for evaluation           |
| `$contextualTuples` | [`TupleKeysInterface`](Models/Collections/TupleKeysInterface.md) &#124; `null`         | Additional tuples for contextual evaluation |
| `$consistency`      | [`Consistency`](Models/Enums/Consistency.md) &#124; `null`                             | Override the default consistency level      |

#### Returns

[`FailureInterface`](Results/FailureInterface.md) &#124; [`SuccessInterface`](Results/SuccessInterface.md) — Success contains ListObjectsResponseInterface, Failure contains Throwable

### listStores

```php
public function listStores(
    ?string $continuationToken = NULL,
    ?int $pageSize = NULL,
): OpenFGA\Results\FailureInterface|OpenFGA\Results\SuccessInterface

```

Lists all stores with pagination.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Client.php#L428)

#### Parameters

| Name                 | Type                   | Description                        |
| -------------------- | ---------------------- | ---------------------------------- |
| `$continuationToken` | `string` &#124; `null` | Token for pagination               |
| `$pageSize`          | `int` &#124; `null`    | Maximum number of stores to return |

#### Returns

[`FailureInterface`](Results/FailureInterface.md) &#124; [`SuccessInterface`](Results/SuccessInterface.md) — Success contains ListStoresResponseInterface, Failure contains Throwable

### listTupleChanges

```php
public function listTupleChanges(
    OpenFGA\Models\StoreInterface|string $store,
    ?string $continuationToken = NULL,
    ?int $pageSize = NULL,
    ?string $type = NULL,
    ?DateTimeImmutable $startTime = NULL,
): OpenFGA\Results\FailureInterface|OpenFGA\Results\SuccessInterface

```

Lists changes to relationship tuples in a store.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Client.php#L449)

#### Parameters

| Name                 | Type                                                         | Description                                            |
| -------------------- | ------------------------------------------------------------ | ------------------------------------------------------ |
| `$store`             | [`StoreInterface`](Models/StoreInterface.md) &#124; `string` | The store to list changes for                          |
| `$continuationToken` | `string` &#124; `null`                                       | Token for pagination                                   |
| `$pageSize`          | `int` &#124; `null`                                          | Maximum number of changes to return                    |
| `$type`              | `string` &#124; `null`                                       | Filter changes by type                                 |
| `$startTime`         | `DateTimeImmutable` &#124; `null`                            | Only include changes at or after this time (inclusive) |

#### Returns

[`FailureInterface`](Results/FailureInterface.md) &#124; [`SuccessInterface`](Results/SuccessInterface.md) — Success contains ListTupleChangesResponseInterface, Failure contains Throwable

### listUsers

```php
public function listUsers(
    OpenFGA\Models\StoreInterface|string $store,
    OpenFGA\Models\AuthorizationModelInterface|string $model,
    string $object,
    string $relation,
    OpenFGA\Models\Collections\UserTypeFiltersInterface $userFilters,
    ?object $context = NULL,
    ?OpenFGA\Models\Collections\TupleKeysInterface $contextualTuples = NULL,
    ?OpenFGA\Models\Enums\Consistency $consistency = NULL,
): OpenFGA\Results\FailureInterface|OpenFGA\Results\SuccessInterface

```

Lists users that have a specific relationship with an object.

**List all users who can view a document:**

```php
$userFilters = new UserTypeFilters([
    new UserTypeFilter('user') // Only include direct users, not groups
]);

$result = $client->listUsers(
    store: 'store-id',
    model: 'model-id',
    object: 'document:budget',
    relation: 'viewer',
    userFilters: $userFilters
);

if ($result->success()) {
    $users = $result->value()->getUsers();
    echo "Users who can view the budget document:\n";
    foreach ($users as $user) {
        echo "- {$user}\n";
    }
}

```

**Find both users and groups with access:**

```php
$userFilters = new UserTypeFilters([
    new UserTypeFilter('user'),
    new UserTypeFilter('group')
]);

$result = $client->listUsers(
    store: 'store-id',
    model: 'model-id',
    object: 'document:sensitive',
    relation: 'editor',
    userFilters: $userFilters
);
/

```

[View source](https://github.com/evansims/openfga-php/blob/main/src/Client.php#L475)

#### Parameters

| Name                | Type                                                                                   | Description                                 |
| ------------------- | -------------------------------------------------------------------------------------- | ------------------------------------------- |
| `$store`            | [`StoreInterface`](Models/StoreInterface.md) &#124; `string`                           | The store to query                          |
| `$model`            | [`AuthorizationModelInterface`](Models/AuthorizationModelInterface.md) &#124; `string` | The authorization model to use              |
| `$object`           | `string`                                                                               | The object to check relationships for       |
| `$relation`         | `string`                                                                               | The relationship to check                   |
| `$userFilters`      | [`UserTypeFiltersInterface`](Models/Collections/UserTypeFiltersInterface.md)           | Filters for user types to include           |
| `$context`          | `object` &#124; `null`                                                                 | Additional context for evaluation           |
| `$contextualTuples` | [`TupleKeysInterface`](Models/Collections/TupleKeysInterface.md) &#124; `null`         | Additional tuples for contextual evaluation |
| `$consistency`      | [`Consistency`](Models/Enums/Consistency.md) &#124; `null`                             | Override the default consistency level      |

#### Returns

[`FailureInterface`](Results/FailureInterface.md) &#124; [`SuccessInterface`](Results/SuccessInterface.md) — Success contains ListUsersResponseInterface, Failure contains Throwable

### readAssertions

```php
public function readAssertions(
    OpenFGA\Models\StoreInterface|string $store,
    OpenFGA\Models\AuthorizationModelInterface|string $model,
): OpenFGA\Results\FailureInterface|OpenFGA\Results\SuccessInterface

```

Retrieves assertions for an authorization model.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Client.php#L507)

#### Parameters

| Name     | Type                                                                                   | Description                     |
| -------- | -------------------------------------------------------------------------------------- | ------------------------------- |
| `$store` | [`StoreInterface`](Models/StoreInterface.md) &#124; `string`                           | The store containing the model  |
| `$model` | [`AuthorizationModelInterface`](Models/AuthorizationModelInterface.md) &#124; `string` | The model to get assertions for |

#### Returns

[`FailureInterface`](Results/FailureInterface.md) &#124; [`SuccessInterface`](Results/SuccessInterface.md) — Success contains ReadAssertionsResponseInterface, Failure contains Throwable

### readTuples

```php
public function readTuples(
    OpenFGA\Models\StoreInterface|string $store,
    ?OpenFGA\Models\TupleKeyInterface $tuple = NULL,
    ?string $continuationToken = NULL,
    ?int $pageSize = NULL,
    ?OpenFGA\Models\Enums\Consistency $consistency = NULL,
): OpenFGA\Results\FailureInterface|OpenFGA\Results\SuccessInterface

```

Reads relationship tuples from a store with optional filtering and pagination.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Client.php#L527)

#### Parameters

| Name                 | Type                                                             | Description                                    |
| -------------------- | ---------------------------------------------------------------- | ---------------------------------------------- |
| `$store`             | [`StoreInterface`](Models/StoreInterface.md) &#124; `string`     | The store to read from                         |
| `$tuple`             | [`TupleKeyInterface`](Models/TupleKeyInterface.md) &#124; `null` | Filter tuples by this key (return all if null) |
| `$continuationToken` | `string` &#124; `null`                                           | Token for pagination                           |
| `$pageSize`          | `int` &#124; `null`                                              | Maximum number of tuples to return             |
| `$consistency`       | [`Consistency`](Models/Enums/Consistency.md) &#124; `null`       | Override the default consistency level         |

#### Returns

[`FailureInterface`](Results/FailureInterface.md) &#124; [`SuccessInterface`](Results/SuccessInterface.md) — Success contains ReadTuplesResponseInterface, Failure contains Throwable

### streamedListObjects

```php
public function streamedListObjects(
    OpenFGA\Models\StoreInterface|string $store,
    OpenFGA\Models\AuthorizationModelInterface|string $model,
    string $type,
    string $relation,
    string $user,
    ?object $context = NULL,
    ?OpenFGA\Models\Collections\TupleKeysInterface $contextualTuples = NULL,
    ?OpenFGA\Models\Enums\Consistency $consistency = NULL,
): OpenFGA\Results\FailureInterface|OpenFGA\Results\SuccessInterface

```

Streams objects that a user has a specific relationship with. Returns all objects of a given type that the specified user has a relationship with, using a streaming response for memory-efficient processing of large result sets. This is ideal for handling thousands of objects without loading them all into memory.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Client.php#L553)

#### Parameters

| Name                | Type                                                                                   | Description                                 |
| ------------------- | -------------------------------------------------------------------------------------- | ------------------------------------------- |
| `$store`            | [`StoreInterface`](Models/StoreInterface.md) &#124; `string`                           | The store to query                          |
| `$model`            | [`AuthorizationModelInterface`](Models/AuthorizationModelInterface.md) &#124; `string` | The authorization model to use              |
| `$type`             | `string`                                                                               | The object type to find                     |
| `$relation`         | `string`                                                                               | The relationship to check                   |
| `$user`             | `string`                                                                               | The user to check relationships for         |
| `$context`          | `object` &#124; `null`                                                                 | Additional context for evaluation           |
| `$contextualTuples` | [`TupleKeysInterface`](Models/Collections/TupleKeysInterface.md) &#124; `null`         | Additional tuples for contextual evaluation |
| `$consistency`      | [`Consistency`](Models/Enums/Consistency.md) &#124; `null`                             | Override the default consistency level      |

#### Returns

[`FailureInterface`](Results/FailureInterface.md) &#124; [`SuccessInterface`](Results/SuccessInterface.md) — Success contains Generator&lt;StreamedListObjectsResponseInterface&gt;, Failure contains Throwable

### writeAssertions

```php
public function writeAssertions(
    OpenFGA\Models\StoreInterface|string $store,
    OpenFGA\Models\AuthorizationModelInterface|string $model,
    OpenFGA\Models\Collections\AssertionsInterface $assertions,
): OpenFGA\Results\FailureInterface|OpenFGA\Results\SuccessInterface

```

Creates or updates assertions for an authorization model.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Client.php#L585)

#### Parameters

| Name          | Type                                                                                   | Description                        |
| ------------- | -------------------------------------------------------------------------------------- | ---------------------------------- |
| `$store`      | [`StoreInterface`](Models/StoreInterface.md) &#124; `string`                           | The store containing the model     |
| `$model`      | [`AuthorizationModelInterface`](Models/AuthorizationModelInterface.md) &#124; `string` | The model to update assertions for |
| `$assertions` | [`AssertionsInterface`](Models/Collections/AssertionsInterface.md)                     | The assertions to upsert           |

#### Returns

[`FailureInterface`](Results/FailureInterface.md) &#124; [`SuccessInterface`](Results/SuccessInterface.md) — Success contains WriteAssertionsResponseInterface, Failure contains Throwable

### writeTuples

```php
public function writeTuples(
    OpenFGA\Models\StoreInterface|string $store,
    OpenFGA\Models\AuthorizationModelInterface|string $model,
    ?OpenFGA\Models\Collections\TupleKeysInterface $writes = NULL,
    ?OpenFGA\Models\Collections\TupleKeysInterface $deletes = NULL,
    bool $transactional = true,
    int $maxParallelRequests = 1,
    int $maxTuplesPerChunk = 100,
    int $maxRetries = 0,
    float $retryDelaySeconds = 1.0,
    bool $stopOnFirstError = false,
): OpenFGA\Results\FailureInterface|OpenFGA\Results\SuccessInterface

```

Writes or deletes relationship tuples in a store. This method supports both transactional (all-or-nothing) and non-transactional (independent operations) modes. In transactional mode, all operations must succeed or the entire request fails. In non-transactional mode, operations are processed independently with detailed success/failure tracking.

**Transactional write (all-or-nothing):**

```php
// Create relationships - all succeed or all fail together
$writes = new TupleKeys([
    new TupleKey('user:anne', 'owner', 'document:budget'),
    new TupleKey('user:bob', 'viewer', 'document:budget'),
    new TupleKey('user:charlie', 'editor', 'document:roadmap'),
]);

$result = $client->writeTuples(
    store: 'store-id',
    model: 'model-id',
    writes: $writes
);

if ($result->success()) {
    echo "Successfully wrote " . count($writes) . " relationships";
}

```

**Non-transactional batch processing:**

```php
// Process large datasets with parallel execution and partial success handling
$writes = new TupleKeys([
    // ... hundreds or thousands of tuples
]);

$result = $client->writeTuples(
    store: 'store-id',
    model: 'model-id',
    writes: $writes,
    transactional: false,
    maxParallelRequests: 5,
    maxTuplesPerChunk: 50,
    maxRetries: 2
);

$result->success(function($response) {
    if ($response->isCompleteSuccess()) {
        echo "All operations succeeded\n";
    } elseif ($response->isPartialSuccess()) {
        echo "Partial success: {$response->getSuccessfulChunks()}/{$response->getTotalChunks()} chunks\n";
        foreach ($response->getErrors() as $error) {
            echo "Error: " . $error->getMessage() . "\n";
        }
    }
});

```

**Updating permissions by adding and removing tuples:**

```php
$writes = new TupleKeys([
    new TupleKey('user:anne', 'editor', 'document:budget'), // Promote anne to editor
]);

$deletes = new TupleKeys([
    new TupleKey('user:bob', 'viewer', 'document:budget'), // Remove bob's access
]);

$client->writeTuples(
    store: 'store-id',
    model: 'model-id',
    writes: $writes,
    deletes: $deletes
);
/

```

[View source](https://github.com/evansims/openfga-php/blob/main/src/Client.php#L607)

#### Parameters

| Name                   | Type                                                                                   | Description                                                      |
| ---------------------- | -------------------------------------------------------------------------------------- | ---------------------------------------------------------------- |
| `$store`               | [`StoreInterface`](Models/StoreInterface.md) &#124; `string`                           | The store to modify                                              |
| `$model`               | [`AuthorizationModelInterface`](Models/AuthorizationModelInterface.md) &#124; `string` | The authorization model to use                                   |
| `$writes`              | [`TupleKeysInterface`](Models/Collections/TupleKeysInterface.md) &#124; `null`         | Tuples to write (create or update)                               |
| `$deletes`             | [`TupleKeysInterface`](Models/Collections/TupleKeysInterface.md) &#124; `null`         | Tuples to delete                                                 |
| `$transactional`       | `bool`                                                                                 | Whether to use transactional mode (default: true)                |
| `$maxParallelRequests` | `int`                                                                                  | Maximum concurrent requests (non-transactional only, default: 1) |
| `$maxTuplesPerChunk`   | `int`                                                                                  | Maximum tuples per chunk (non-transactional only, default: 100)  |
| `$maxRetries`          | `int`                                                                                  | Maximum retry attempts (non-transactional only, default: 0)      |
| `$retryDelaySeconds`   | `float`                                                                                | Retry delay in seconds (non-transactional only, default: 1.0)    |
| `$stopOnFirstError`    | `bool`                                                                                 | Stop on first error (non-transactional only, default: false)     |

#### Returns

[`FailureInterface`](Results/FailureInterface.md) &#124; [`SuccessInterface`](Results/SuccessInterface.md) — Success contains WriteTuplesResponseInterface, Failure contains Throwable
