# Client

OpenFGA Client implementation for relationship-based access control operations. This client provides a complete implementation of the OpenFGA API, supporting all core operations including store management, authorization model configuration, relationship tuple operations, and authorization checks. The client uses PSR-7/18 HTTP standards and implements the Result pattern for error handling. The client supports multiple authentication methods including OAuth 2.0 Client Credentials flow and pre-shared key authentication, with automatic token management and retry capabilities for reliable operation.

## Namespace
`OpenFGA`

## Source
[View source code](https://github.com/evansims/openfga-php/blob/main/src/Client.php)

## Implements
* [ClientInterface](ClientInterface.md)

## Related Classes
* [ClientInterface](ClientInterface.md) (interface)

## Constants
| Name | Value | Description |
|------|-------|-------------|
| `VERSION` | `&#039;1.2.0&#039;` | The version of the OpenFGA PHP SDK. |


## Methods

                                                                                                                                                                                                                                                                                                                                                
### Authorization
#### batchCheck


```php
public function batchCheck(OpenFGA\Models\StoreInterface|string $store, OpenFGA\Models\AuthorizationModelInterface|string $model, OpenFGA\Models\Collections\BatchCheckItemsInterface $checks): OpenFGA\Results\FailureInterface|OpenFGA\Results\SuccessInterface
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

[View source](https://github.com/evansims/openfga-php/blob/main/src/Client.php#L132)

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | OpenFGA\Models\StoreInterface&#124;string | The store to check against |
| `$model` | OpenFGA\Models\AuthorizationModelInterface&#124;string | The authorization model to use |
| `consistency` | ?OpenFGA\Models\Enums\Consistency |  |

#### Returns
OpenFGA\Results\FailureInterface&#124;OpenFGA\Results\SuccessInterface
 The batch check results

#### check


```php
public function check(OpenFGA\Models\StoreInterface|string $store, OpenFGA\Models\AuthorizationModelInterface|string $model, OpenFGA\Models\TupleKeyInterface $tupleKey, ?bool $trace = NULL, ?object $context = NULL, ?OpenFGA\Models\Collections\TupleKeysInterface $contextualTuples = NULL, ?OpenFGA\Models\Enums\Consistency $consistency = NULL): OpenFGA\Results\FailureInterface|OpenFGA\Results\SuccessInterface
```

Checks if a user has a specific relationship with an object. Performs an authorization check to determine if a user has a particular relationship with an object based on the configured authorization model. This is the core operation for making authorization decisions in OpenFGA.


**Basic permission check:**
```php
$result = $client->check(
    store: 'store-id',
    model: 'model-id',
    tupleKey: new TupleKey('user:anne', 'reader', 'document:budget')
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
    tupleKey: new TupleKey('user:anne', 'reader', 'document:budget'),
    contextualTuples: $contextualTuples
);
```

[View source](https://github.com/evansims/openfga-php/blob/main/src/Client.php#L163)

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | OpenFGA\Models\StoreInterface&#124;string | The store to check against |
| `$model` | OpenFGA\Models\AuthorizationModelInterface&#124;string | The authorization model to use |
| `$tupleKey` | OpenFGA\Models\TupleKeyInterface | The relationship to check |
| `$trace` | ?bool | Whether to include a trace in the response |
| `$context` | ?object | Additional context for the check |
| `$contextualTuples` | ?OpenFGA\Models\Collections\TupleKeysInterface | Additional tuples for contextual evaluation |
| `schemaVersion` | OpenFGA\Models\Enums\SchemaVersion |  |

#### Returns
OpenFGA\Results\FailureInterface&#124;OpenFGA\Results\SuccessInterface
 Success contains CheckResponseInterface, Failure contains Throwable

#### expand


```php
public function expand(OpenFGA\Models\StoreInterface|string $store, OpenFGA\Models\TupleKeyInterface $tupleKey, ?OpenFGA\Models\AuthorizationModelInterface|string|null $model = NULL, ?OpenFGA\Models\Collections\TupleKeysInterface $contextualTuples = NULL, ?OpenFGA\Models\Enums\Consistency $consistency = NULL): OpenFGA\Results\FailureInterface|OpenFGA\Results\SuccessInterface
```

Expands a relationship tuple to show all users that have the relationship.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Client.php#L313)

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | OpenFGA\Models\StoreInterface&#124;string | The store containing the tuple |
| `$tupleKey` | OpenFGA\Models\TupleKeyInterface | The tuple to expand |
| `$model` | ?OpenFGA\Models\AuthorizationModelInterface&#124;string&#124;null | The authorization model to use |
| `$contextualTuples` | ?OpenFGA\Models\Collections\TupleKeysInterface | Additional tuples for contextual evaluation |
| `model` | OpenFGA\Models\AuthorizationModelInterface|string |  |

#### Returns
OpenFGA\Results\FailureInterface&#124;OpenFGA\Results\SuccessInterface
 Success contains ExpandResponseInterface, Failure contains Throwable

### CRUD Operations
#### createAuthorizationModel


```php
public function createAuthorizationModel(OpenFGA\Models\StoreInterface|string $store, OpenFGA\Models\Collections\TypeDefinitionsInterface $typeDefinitions, ?OpenFGA\Models\Collections\ConditionsInterface $conditions = NULL, OpenFGA\Models\Enums\SchemaVersion $schemaVersion = OpenFGA\Models\Enums\SchemaVersion::V1_1): OpenFGA\Results\FailureInterface|OpenFGA\Results\SuccessInterface
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

[View source](https://github.com/evansims/openfga-php/blob/main/src/Client.php#L202)

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | OpenFGA\Models\StoreInterface&#124;string | The store to create the model in |
| `$typeDefinitions` | OpenFGA\Models\Collections\TypeDefinitionsInterface | The type definitions for the model |
| `$conditions` | ?OpenFGA\Models\Collections\ConditionsInterface | The conditions for the model |
| `name` | string |  |

#### Returns
OpenFGA\Results\FailureInterface&#124;OpenFGA\Results\SuccessInterface
 Success contains CreateAuthorizationModelResponseInterface, Failure contains Throwable

#### createStore


```php
public function createStore(string $name): OpenFGA\Results\FailureInterface|OpenFGA\Results\SuccessInterface
```

Creates a new store with the given name. Stores provide data isolation for different applications or environments. Each store maintains its own authorization models, relationship tuples, and provides complete separation from other stores.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Client.php#L230)

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `store` | OpenFGA\Models\StoreInterface|string |  |

#### Returns
OpenFGA\Results\FailureInterface&#124;OpenFGA\Results\SuccessInterface
 Success contains CreateStoreResponseInterface, Failure contains Throwable

#### deleteStore


```php
public function deleteStore(OpenFGA\Models\StoreInterface|string $store): OpenFGA\Results\FailureInterface|OpenFGA\Results\SuccessInterface
```

Deletes a store.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Client.php#L254)

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `dsl` | string |  |

#### Returns
OpenFGA\Results\FailureInterface&#124;OpenFGA\Results\SuccessInterface
 Success contains DeleteStoreResponseInterface, Failure contains Throwable

#### readAssertions


```php
public function readAssertions(OpenFGA\Models\StoreInterface|string $store, OpenFGA\Models\AuthorizationModelInterface|string $model): OpenFGA\Results\FailureInterface|OpenFGA\Results\SuccessInterface
```

Retrieves assertions for an authorization model.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Client.php#L577)

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | OpenFGA\Models\StoreInterface&#124;string | The store containing the model |
| `consistency` | ?OpenFGA\Models\Enums\Consistency |  |

#### Returns
OpenFGA\Results\FailureInterface&#124;OpenFGA\Results\SuccessInterface
 Success contains ReadAssertionsResponseInterface, Failure contains Throwable

#### readTuples


```php
public function readTuples(OpenFGA\Models\StoreInterface|string $store, OpenFGA\Models\TupleKeyInterface $tupleKey, ?string $continuationToken = NULL, ?int $pageSize = NULL, ?OpenFGA\Models\Enums\Consistency $consistency = NULL): OpenFGA\Results\FailureInterface|OpenFGA\Results\SuccessInterface
```

Reads relationship tuples from a store with optional filtering and pagination.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Client.php#L601)

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | OpenFGA\Models\StoreInterface&#124;string | The store to read from |
| `$tupleKey` | OpenFGA\Models\TupleKeyInterface | Filter tuples by this key (return all if null) |
| `$continuationToken` | ?string | Token for pagination |
| `$pageSize` | ?int | Maximum number of tuples to return |
| `consistency` | ?OpenFGA\Models\Enums\Consistency |  |

#### Returns
OpenFGA\Results\FailureInterface&#124;OpenFGA\Results\SuccessInterface
 Success contains ReadTuplesResponseInterface, Failure contains Throwable

#### writeAssertions


```php
public function writeAssertions(OpenFGA\Models\StoreInterface|string $store, OpenFGA\Models\AuthorizationModelInterface|string $model, OpenFGA\Models\Collections\AssertionsInterface $assertions): OpenFGA\Results\FailureInterface|OpenFGA\Results\SuccessInterface
```

Creates or updates assertions for an authorization model.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Client.php#L669)

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | OpenFGA\Models\StoreInterface&#124;string | The store containing the model |
| `$model` | OpenFGA\Models\AuthorizationModelInterface&#124;string | The model to update assertions for |
| `deletes` | ?OpenFGA\Models\Collections\TupleKeysInterface |  |

#### Returns
OpenFGA\Results\FailureInterface&#124;OpenFGA\Results\SuccessInterface
 Success contains WriteAssertionsResponseInterface, Failure contains Throwable

#### writeTuples


```php
public function writeTuples(OpenFGA\Models\StoreInterface|string $store, OpenFGA\Models\AuthorizationModelInterface|string $model, ?OpenFGA\Models\Collections\TupleKeysInterface $writes = NULL, ?OpenFGA\Models\Collections\TupleKeysInterface $deletes = NULL): OpenFGA\Results\FailureInterface|OpenFGA\Results\SuccessInterface
```

Writes or deletes relationship tuples in a store.


**Writing and deleting relationship tuples:**
```php
// Create relationships
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

[View source](https://github.com/evansims/openfga-php/blob/main/src/Client.php#L695)

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | OpenFGA\Models\StoreInterface&#124;string | The store to modify |
| `$model` | OpenFGA\Models\AuthorizationModelInterface&#124;string | The authorization model to use |
| `$writes` | ?OpenFGA\Models\Collections\TupleKeysInterface | Tuples to write (create or update) |
| `$deletes` | ?OpenFGA\Models\Collections\TupleKeysInterface | Tuples to delete |

#### Returns
OpenFGA\Results\FailureInterface&#124;OpenFGA\Results\SuccessInterface
 Success contains WriteTuplesResponseInterface, Failure contains Throwable

### List Operations
#### getAuthorizationModel


```php
public function getAuthorizationModel(OpenFGA\Models\StoreInterface|string $store, OpenFGA\Models\AuthorizationModelInterface|string $model): OpenFGA\Results\FailureInterface|OpenFGA\Results\SuccessInterface
```

Retrieves an authorization model by ID.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Client.php#L343)

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | OpenFGA\Models\StoreInterface&#124;string | The store containing the model |
| `store` | OpenFGA\Models\StoreInterface|string |  |

#### Returns
OpenFGA\Results\FailureInterface&#124;OpenFGA\Results\SuccessInterface
 Success contains GetAuthorizationModelResponseInterface, Failure contains Throwable

#### getLanguage


```php
public function getLanguage(): string
```

Get the configured language for i18n translations.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Client.php#L366)


#### Returns
string
 The configured language code

#### getLastRequest


```php
public function getLastRequest(): ?Psr\Http\Message\RequestInterface
```

Retrieves the last HTTP request made by the client.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Client.php#L375)


#### Returns
?Psr\Http\Message\RequestInterface

#### getLastResponse


```php
public function getLastResponse(): ?Psr\Http\Message\ResponseInterface
```

Retrieves the last HTTP response received by the client.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Client.php#L384)


#### Returns
?Psr\Http\Message\ResponseInterface

#### getStore


```php
public function getStore(OpenFGA\Models\StoreInterface|string $store): OpenFGA\Results\FailureInterface|OpenFGA\Results\SuccessInterface
```

Retrieves store details by ID.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Client.php#L395)

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `pageSize` | ?int |  |

#### Returns
OpenFGA\Results\FailureInterface&#124;OpenFGA\Results\SuccessInterface
 Success contains GetStoreResponseInterface, Failure contains Throwable

#### listAuthorizationModels


```php
public function listAuthorizationModels(OpenFGA\Models\StoreInterface|string $store, ?string $continuationToken = NULL, ?int $pageSize = NULL): OpenFGA\Results\FailureInterface|OpenFGA\Results\SuccessInterface
```

Lists authorization models in a store with pagination.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Client.php#L419)

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | OpenFGA\Models\StoreInterface&#124;string | The store to list models from |
| `$continuationToken` | ?string | Token for pagination |
| `consistency` | ?OpenFGA\Models\Enums\Consistency |  |

#### Returns
OpenFGA\Results\FailureInterface&#124;OpenFGA\Results\SuccessInterface
 Success contains ListAuthorizationModelsResponseInterface, Failure contains Throwable

#### listObjects


```php
public function listObjects(OpenFGA\Models\StoreInterface|string $store, OpenFGA\Models\AuthorizationModelInterface|string $model, string $type, string $relation, string $user, ?object $context = NULL, ?OpenFGA\Models\Collections\TupleKeysInterface $contextualTuples = NULL, ?OpenFGA\Models\Enums\Consistency $consistency = NULL): OpenFGA\Results\FailureInterface|OpenFGA\Results\SuccessInterface
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

[View source](https://github.com/evansims/openfga-php/blob/main/src/Client.php#L447)

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | OpenFGA\Models\StoreInterface&#124;string | The store to query |
| `$model` | OpenFGA\Models\AuthorizationModelInterface&#124;string | The authorization model to use |
| `$type` | string | The type of objects to list |
| `$relation` | string | The relationship to check |
| `$user` | string | The user to check relationships for |
| `$context` | ?object | Additional context for evaluation |
| `$contextualTuples` | ?OpenFGA\Models\Collections\TupleKeysInterface | Additional tuples for contextual evaluation |
| `pageSize` | ?int |  |

#### Returns
OpenFGA\Results\FailureInterface&#124;OpenFGA\Results\SuccessInterface
 Success contains ListObjectsResponseInterface, Failure contains Throwable

#### listStores


```php
public function listStores(?string $continuationToken = NULL, ?int $pageSize = NULL): OpenFGA\Results\FailureInterface|OpenFGA\Results\SuccessInterface
```

Lists all stores with pagination.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Client.php#L483)

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$continuationToken` | ?string | Token for pagination |
| `startTime` | ?DateTimeImmutable |  |

#### Returns
OpenFGA\Results\FailureInterface&#124;OpenFGA\Results\SuccessInterface
 Success contains ListStoresResponseInterface, Failure contains Throwable

#### listTupleChanges


```php
public function listTupleChanges(OpenFGA\Models\StoreInterface|string $store, ?string $continuationToken = NULL, ?int $pageSize = NULL, ?string $type = NULL, ?DateTimeImmutable $startTime = NULL): OpenFGA\Results\FailureInterface|OpenFGA\Results\SuccessInterface
```

Lists changes to relationship tuples in a store.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Client.php#L509)

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | OpenFGA\Models\StoreInterface&#124;string | The store to list changes for |
| `$continuationToken` | ?string | Token for pagination |
| `$pageSize` | ?int | Maximum number of changes to return |
| `$type` | ?string | Filter changes by type |
| `consistency` | ?OpenFGA\Models\Enums\Consistency |  |

#### Returns
OpenFGA\Results\FailureInterface&#124;OpenFGA\Results\SuccessInterface
 Success contains ListTupleChangesResponseInterface, Failure contains Throwable

#### listUsers


```php
public function listUsers(OpenFGA\Models\StoreInterface|string $store, OpenFGA\Models\AuthorizationModelInterface|string $model, string $object, string $relation, OpenFGA\Models\Collections\UserTypeFiltersInterface $userFilters, ?object $context = NULL, ?OpenFGA\Models\Collections\TupleKeysInterface $contextualTuples = NULL, ?OpenFGA\Models\Enums\Consistency $consistency = NULL): OpenFGA\Results\FailureInterface|OpenFGA\Results\SuccessInterface
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

[View source](https://github.com/evansims/openfga-php/blob/main/src/Client.php#L541)

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | OpenFGA\Models\StoreInterface&#124;string | The store to query |
| `$model` | OpenFGA\Models\AuthorizationModelInterface&#124;string | The authorization model to use |
| `$object` | string | The object to check relationships for |
| `$relation` | string | The relationship to check |
| `$userFilters` | OpenFGA\Models\Collections\UserTypeFiltersInterface | Filters for user types to include |
| `$context` | ?object | Additional context for evaluation |
| `$contextualTuples` | ?OpenFGA\Models\Collections\TupleKeysInterface | Additional tuples for contextual evaluation |
| `model` | OpenFGA\Models\AuthorizationModelInterface|string |  |

#### Returns
OpenFGA\Results\FailureInterface&#124;OpenFGA\Results\SuccessInterface
 Success contains ListUsersResponseInterface, Failure contains Throwable

#### streamedListObjects


```php
public function streamedListObjects(OpenFGA\Models\StoreInterface|string $store, OpenFGA\Models\AuthorizationModelInterface|string $model, string $type, string $relation, string $user, ?object $context = NULL, ?OpenFGA\Models\Collections\TupleKeysInterface $contextualTuples = NULL, ?OpenFGA\Models\Enums\Consistency $consistency = NULL): OpenFGA\Results\FailureInterface|OpenFGA\Results\SuccessInterface
```

Streams objects that a user has a specific relationship with. Returns all objects of a given type that the specified user has a relationship with, using a streaming response for memory-efficient processing of large result sets. This is ideal for handling thousands of objects without loading them all into memory.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Client.php#L633)

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | OpenFGA\Models\StoreInterface&#124;string | The store to query |
| `$model` | OpenFGA\Models\AuthorizationModelInterface&#124;string | The authorization model to use |
| `$type` | string | The object type to find |
| `$relation` | string | The relationship to check |
| `$user` | string | The user to check relationships for |
| `$context` | ?object | Additional context for evaluation |
| `$contextualTuples` | ?OpenFGA\Models\Collections\TupleKeysInterface | Additional tuples for contextual evaluation |
| `assertions` | OpenFGA\Models\Collections\AssertionsInterface |  |

#### Returns
OpenFGA\Results\FailureInterface&#124;OpenFGA\Results\SuccessInterface
 Success contains Generator&lt;StreamedListObjectsResponseInterface&gt;, Failure contains Throwable

### Utility
#### assertLastRequest


```php
public function assertLastRequest(): Psr\Http\Message\RequestInterface
```

Retrieves the last HTTP request made by the client.


**Accessing the last request for debugging:**
```php
$result = $client->check(
    store: 'store-id',
    model: 'model-id',
    tupleKey: new TupleKey('user:anne', 'viewer', 'document:budget')
);

$lastRequest = $client->assertLastRequest();

echo "Method: " . $lastRequest->getMethod();
echo "URL: " . $lastRequest->getUri();
echo "Headers: " . json_encode($lastRequest->getHeaders());
/
```

[View source](https://github.com/evansims/openfga-php/blob/main/src/Client.php#L117)


#### Returns
Psr\Http\Message\RequestInterface
 The last request

### Other
#### dsl


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

[View source](https://github.com/evansims/openfga-php/blob/main/src/Client.php#L276)

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `consistency` | ?OpenFGA\Models\Enums\Consistency |  |

#### Returns
OpenFGA\Results\FailureInterface&#124;OpenFGA\Results\SuccessInterface
 Success contains AuthorizationModelInterface, Failure contains Throwable

