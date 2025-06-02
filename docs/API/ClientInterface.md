# ClientInterface

OpenFGA Client Interface for relationship-based access control operations. This interface defines the complete API for interacting with OpenFGA services, providing methods for managing stores, authorization models, relationship tuples, and performing authorization checks. The client implements the Result pattern, returning Success or Failure objects instead of throwing exceptions. All operations support OpenFGA&#039;s core concepts including stores for data isolation, authorization models for defining permission structures, and relationship tuples for expressing user-object relationships.

## Namespace
`OpenFGA`

## Source
[View source code](https://github.com/evansims/openfga-php/blob/main/src/ClientInterface.php)


## Related Classes
* [Client](Client.md) (implementation)



## Methods

                                                                                                                                                                                                                                                                                                                                    
### Authorization
#### batchCheck


```php
public function batchCheck(StoreInterface|string $store, AuthorizationModelInterface|string $model, BatchCheckItemsInterface $checks): FailureInterface|SuccessInterface
```

Performs multiple authorization checks in a single batch request. This method allows checking multiple user-object relationships simultaneously for better performance when multiple authorization decisions are needed. Each check in the batch has a correlation ID to map results back to the original requests. The batch check operation supports the same features as individual checks: contextual tuples, custom contexts, and detailed error information for each check.


**Batch checking multiple permissions efficiently:**
```php
$checks = new BatchCheckItems([
new BatchCheckItem(
tupleKey: new TupleKey(&#039;user:anne&#039;, &#039;viewer&#039;, &#039;document:budget&#039;),
correlationId: &#039;check-anne-viewer&#039;
),
new BatchCheckItem(
tupleKey: new TupleKey(&#039;user:bob&#039;, &#039;editor&#039;, &#039;document:budget&#039;),
correlationId: &#039;check-bob-editor&#039;
),
new BatchCheckItem(
tupleKey: new TupleKey(&#039;user:charlie&#039;, &#039;owner&#039;, &#039;document:roadmap&#039;),
correlationId: &#039;check-charlie-owner&#039;
),
]);
$result = $client-&gt;batchCheck(
store: &#039;store-id&#039;,
model: &#039;model-id&#039;,
checks: $checks
);
if ($result-&gt;success()) {
$responses = $result-&gt;value()-&gt;getResults();
foreach ($responses as $response) {
echo $response-&gt;getCorrelationId() . &#039;: &#039; .
($response-&gt;getAllowed() ? &#039;ALLOWED&#039; : &#039;DENIED&#039;) . &quot;\n&quot;;
}
}
```

[View source](https://github.com/evansims/openfga-php/blob/main/src/ClientInterface.php#L109)

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | StoreInterface&#124;string | The store to check against |
| `$model` | AuthorizationModelInterface&#124;string | The authorization model to use |
| `$checks` | BatchCheckItemsInterface | The batch check items |

#### Returns
FailureInterface&#124;SuccessInterface
 The batch check results

#### check


```php
public function check(StoreInterface|string $store, AuthorizationModelInterface|string $model, TupleKeyInterface $tupleKey, bool|null $trace = NULL, object|null $context = NULL, TupleKeysInterface<TupleKeyInterface>|null $contextualTuples = NULL, Consistency|null $consistency = NULL): FailureInterface|SuccessInterface
```

Checks if a user has a specific relationship with an object. Performs an authorization check to determine if a user has a particular relationship with an object based on the configured authorization model. This is the core operation for making authorization decisions in OpenFGA.


**Basic permission check:**
```php
$result = $client-&gt;check(
store: &#039;store-id&#039;,
model: &#039;model-id&#039;,
tupleKey: new TupleKey(&#039;user:anne&#039;, &#039;reader&#039;, &#039;document:budget&#039;)
);
if ($result-&gt;success()) {
$allowed = $result-&gt;value()-&gt;getAllowed();
if ($allowed) {
User has permission
}
}
```

**Check with contextual tuples:**
```php
$contextualTuples = new TupleKeys([
new TupleKey(&#039;user:anne&#039;, &#039;member&#039;, &#039;team:finance&#039;)
]);
$result = $client-&gt;check(
store: &#039;store-id&#039;,
model: &#039;model-id&#039;,
tupleKey: new TupleKey(&#039;user:anne&#039;, &#039;reader&#039;, &#039;document:budget&#039;),
contextualTuples: $contextualTuples
);
```

[View source](https://github.com/evansims/openfga-php/blob/main/src/ClientInterface.php#L158)

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | StoreInterface&#124;string | The store to check against |
| `$model` | AuthorizationModelInterface&#124;string | The authorization model to use |
| `$tupleKey` | TupleKeyInterface | The relationship to check |
| `$trace` | bool&#124;null | Whether to include a trace in the response |
| `$context` | object&#124;null | Additional context for the check |
| `$contextualTuples` | TupleKeysInterface&lt;TupleKeyInterface&gt;&#124;null | Additional tuples for contextual evaluation |
| `$consistency` | Consistency&#124;null | Override the default consistency level |

#### Returns
FailureInterface&#124;SuccessInterface
 Success contains CheckResponseInterface, Failure contains Throwable

#### expand


```php
public function expand(StoreInterface|string $store, TupleKeyInterface $tupleKey, AuthorizationModelInterface|string|null $model = NULL, TupleKeysInterface<TupleKeyInterface>|null $contextualTuples = NULL, Consistency|null $consistency = NULL): FailureInterface|SuccessInterface
```

Expands a relationship tuple to show all users that have the relationship.

[View source](https://github.com/evansims/openfga-php/blob/main/src/ClientInterface.php#L295)

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | StoreInterface&#124;string | The store containing the tuple |
| `$tupleKey` | TupleKeyInterface | The tuple to expand |
| `$model` | AuthorizationModelInterface&#124;string&#124;null | The authorization model to use |
| `$contextualTuples` | TupleKeysInterface&lt;TupleKeyInterface&gt;&#124;null | Additional tuples for contextual evaluation |
| `$consistency` | Consistency&#124;null | Override the default consistency level |

#### Returns
FailureInterface&#124;SuccessInterface
 Success contains ExpandResponseInterface, Failure contains Throwable

### CRUD Operations
#### createAuthorizationModel


```php
public function createAuthorizationModel(StoreInterface|string $store, TypeDefinitionsInterface<TypeDefinitionInterface> $typeDefinitions, ConditionsInterface<ConditionInterface>|null $conditions = NULL, SchemaVersion $schemaVersion = OpenFGA\Models\Enums\SchemaVersion::V1_1): FailureInterface|SuccessInterface
```

Creates a new authorization model with the given type definitions and conditions. Authorization models define the permission structure for your application, including object types, relationships, and how permissions are computed. Models are immutable once created and identified by a unique ID.


**Creating a document authorization model with DSL (recommended):**
```php
Using DSL is usually easier than manually building type definitions
$dsl = &#039;
model
schema 1.1
type user
type document
relations
define owner: [user]
define editor: [user] or owner
define viewer: [user] or editor
&#039;;
$authModel = $client-&gt;dsl($dsl)-&gt;unwrap();
$result = $client-&gt;createAuthorizationModel(
store: &#039;store-id&#039;,
typeDefinitions: $authModel-&gt;getTypeDefinitions()
);
if ($result-&gt;success()) {
$modelId = $result-&gt;value()-&gt;getAuthorizationModelId();
echo &quot;Created model: {$modelId}&quot;;
}
```

[View source](https://github.com/evansims/openfga-php/blob/main/src/ClientInterface.php#L210)

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | StoreInterface&#124;string | The store to create the model in |
| `$typeDefinitions` | TypeDefinitionsInterface&lt;TypeDefinitionInterface&gt; | The type definitions for the model |
| `$conditions` | ConditionsInterface&lt;ConditionInterface&gt;&#124;null | The conditions for the model |
| `$schemaVersion` | SchemaVersion | The schema version to use (default: 1.1) |

#### Returns
FailureInterface&#124;SuccessInterface
 Success contains CreateAuthorizationModelResponseInterface, Failure contains Throwable

#### createStore


```php
public function createStore(string $name): FailureInterface|SuccessInterface
```

Creates a new store with the given name. Stores provide data isolation for different applications or environments. Each store maintains its own authorization models, relationship tuples, and provides complete separation from other stores.

[View source](https://github.com/evansims/openfga-php/blob/main/src/ClientInterface.php#L229)

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$name` | string | The name for the new store |

#### Returns
FailureInterface&#124;SuccessInterface
 Success contains CreateStoreResponseInterface, Failure contains Throwable

#### deleteStore


```php
public function deleteStore(StoreInterface|string $store): FailureInterface|SuccessInterface
```

Deletes a store.

[View source](https://github.com/evansims/openfga-php/blob/main/src/ClientInterface.php#L239)

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | StoreInterface&#124;string | The store to delete |

#### Returns
FailureInterface&#124;SuccessInterface
 Success contains DeleteStoreResponseInterface, Failure contains Throwable

#### readAssertions


```php
public function readAssertions(StoreInterface|string $store, AuthorizationModelInterface|string $model): FailureInterface|SuccessInterface
```

Retrieves assertions for an authorization model.

[View source](https://github.com/evansims/openfga-php/blob/main/src/ClientInterface.php#L514)

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | StoreInterface&#124;string | The store containing the model |
| `$model` | AuthorizationModelInterface&#124;string | The model to get assertions for |

#### Returns
FailureInterface&#124;SuccessInterface
 Success contains ReadAssertionsResponseInterface, Failure contains Throwable

#### readTuples


```php
public function readTuples(StoreInterface|string $store, TupleKeyInterface $tupleKey, string|null $continuationToken = NULL, ?int $pageSize = NULL, Consistency|null $consistency = NULL): FailureInterface|SuccessInterface
```

Reads relationship tuples from a store with optional filtering and pagination.

[View source](https://github.com/evansims/openfga-php/blob/main/src/ClientInterface.php#L532)

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | StoreInterface&#124;string | The store to read from |
| `$tupleKey` | TupleKeyInterface | Filter tuples by this key (return all if null) |
| `$continuationToken` | string&#124;null | Token for pagination |
| `$pageSize` | ?int | Maximum number of tuples to return |
| `$consistency` | Consistency&#124;null | Override the default consistency level |

#### Returns
FailureInterface&#124;SuccessInterface
 Success contains ReadTuplesResponseInterface, Failure contains Throwable

#### writeAssertions


```php
public function writeAssertions(StoreInterface|string $store, AuthorizationModelInterface|string $model, AssertionsInterface<AssertionInterface> $assertions): FailureInterface|SuccessInterface
```

Creates or updates assertions for an authorization model.

[View source](https://github.com/evansims/openfga-php/blob/main/src/ClientInterface.php#L576)

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | StoreInterface&#124;string | The store containing the model |
| `$model` | AuthorizationModelInterface&#124;string | The model to update assertions for |
| `$assertions` | AssertionsInterface&lt;AssertionInterface&gt; | The assertions to upsert |

#### Returns
FailureInterface&#124;SuccessInterface
 Success contains WriteAssertionsResponseInterface, Failure contains Throwable

#### writeTuples


```php
public function writeTuples(StoreInterface|string $store, AuthorizationModelInterface|string $model, TupleKeysInterface<TupleKeyInterface>|null $writes = NULL, TupleKeysInterface<TupleKeyInterface>|null $deletes = NULL): FailureInterface|SuccessInterface
```

Writes or deletes relationship tuples in a store.


**Writing and deleting relationship tuples:**
```php
Create relationships
$writes = new TupleKeys([
new TupleKey(&#039;user:anne&#039;, &#039;owner&#039;, &#039;document:budget&#039;),
new TupleKey(&#039;user:bob&#039;, &#039;viewer&#039;, &#039;document:budget&#039;),
new TupleKey(&#039;user:charlie&#039;, &#039;editor&#039;, &#039;document:roadmap&#039;),
]);
$result = $client-&gt;writeTuples(
store: &#039;store-id&#039;,
model: &#039;model-id&#039;,
writes: $writes
);
if ($result-&gt;success()) {
echo &quot;Successfully wrote &quot; . count($writes) . &quot; relationships&quot;;
}
```

**Updating permissions by adding and removing tuples:**
```php
$writes = new TupleKeys([
new TupleKey(&#039;user:anne&#039;, &#039;editor&#039;, &#039;document:budget&#039;), // Promote anne to editor
]);
$deletes = new TupleKeys([
new TupleKey(&#039;user:bob&#039;, &#039;viewer&#039;, &#039;document:budget&#039;), // Remove bob&#039;s access
]);
$client-&gt;writeTuples(
store: &#039;store-id&#039;,
model: &#039;model-id&#039;,
writes: $writes,
deletes: $deletes
);
```

[View source](https://github.com/evansims/openfga-php/blob/main/src/ClientInterface.php#L625)

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | StoreInterface&#124;string | The store to modify |
| `$model` | AuthorizationModelInterface&#124;string | The authorization model to use |
| `$writes` | TupleKeysInterface&lt;TupleKeyInterface&gt;&#124;null | Tuples to write (create or update) |
| `$deletes` | TupleKeysInterface&lt;TupleKeyInterface&gt;&#124;null | Tuples to delete |

#### Returns
FailureInterface&#124;SuccessInterface
 Success contains WriteTuplesResponseInterface, Failure contains Throwable

### List Operations
#### getAuthorizationModel


```php
public function getAuthorizationModel(StoreInterface|string $store, AuthorizationModelInterface|string $model): FailureInterface|SuccessInterface
```

Retrieves an authorization model by ID.

[View source](https://github.com/evansims/openfga-php/blob/main/src/ClientInterface.php#L310)

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | StoreInterface&#124;string | The store containing the model |
| `$model` | AuthorizationModelInterface&#124;string | The model to retrieve |

#### Returns
FailureInterface&#124;SuccessInterface
 Success contains GetAuthorizationModelResponseInterface, Failure contains Throwable

#### getLastRequest


```php
public function getLastRequest(): ?Psr\Http\Message\RequestInterface
```

Retrieves the last HTTP request made by the client.

[View source](https://github.com/evansims/openfga-php/blob/main/src/ClientInterface.php#L320)


#### Returns
?Psr\Http\Message\RequestInterface

#### getLastResponse


```php
public function getLastResponse(): ?Psr\Http\Message\ResponseInterface
```

Retrieves the last HTTP response received by the client.

[View source](https://github.com/evansims/openfga-php/blob/main/src/ClientInterface.php#L327)


#### Returns
?Psr\Http\Message\ResponseInterface

#### getStore


```php
public function getStore(StoreInterface|string $store): FailureInterface|SuccessInterface
```

Retrieves store details by ID.

[View source](https://github.com/evansims/openfga-php/blob/main/src/ClientInterface.php#L335)

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | StoreInterface&#124;string | The store to retrieve |

#### Returns
FailureInterface&#124;SuccessInterface
 Success contains GetStoreResponseInterface, Failure contains Throwable

#### listAuthorizationModels


```php
public function listAuthorizationModels(StoreInterface|string $store, string|null $continuationToken = NULL, ?int $pageSize = NULL): FailureInterface|SuccessInterface
```

Lists authorization models in a store with pagination.

[View source](https://github.com/evansims/openfga-php/blob/main/src/ClientInterface.php#L350)

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | StoreInterface&#124;string | The store to list models from |
| `$continuationToken` | string&#124;null | Token for pagination |
| `$pageSize` | ?int | Maximum number of models to return |

#### Returns
FailureInterface&#124;SuccessInterface
 Success contains ListAuthorizationModelsResponseInterface, Failure contains Throwable

#### listObjects


```php
public function listObjects(StoreInterface|string $store, AuthorizationModelInterface|string $model, string $type, string $relation, string $user, object|null $context = NULL, TupleKeysInterface<TupleKeyInterface>|null $contextualTuples = NULL, Consistency|null $consistency = NULL): FailureInterface|SuccessInterface
```

Lists objects that have a specific relationship with a user.


**List all documents a user can view:**
```php
$result = $client-&gt;listObjects(
store: &#039;store-id&#039;,
model: &#039;model-id&#039;,
type: &#039;document&#039;,
relation: &#039;viewer&#039;,
user: &#039;user:anne&#039;
);
if ($result-&gt;success()) {
$objects = $result-&gt;value()-&gt;getObjects();
echo &quot;Anne can view &quot; . count($objects) . &quot; documents:\n&quot;;
foreach ($objects as $object) {
echo &quot;- {$object}\n&quot;;
}
}
```

**List objects with contextual evaluation:**
```php
Check what documents anne can edit, considering her team membership
$contextualTuples = new TupleKeys([
new TupleKey(&#039;user:anne&#039;, &#039;member&#039;, &#039;team:engineering&#039;)
]);
$result = $client-&gt;listObjects(
store: &#039;store-id&#039;,
model: &#039;model-id&#039;,
type: &#039;document&#039;,
relation: &#039;editor&#039;,
user: &#039;user:anne&#039;,
contextualTuples: $contextualTuples
);
```

[View source](https://github.com/evansims/openfga-php/blob/main/src/ClientInterface.php#L401)

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | StoreInterface&#124;string | The store to query |
| `$model` | AuthorizationModelInterface&#124;string | The authorization model to use |
| `$type` | string | The type of objects to list |
| `$relation` | string | The relationship to check |
| `$user` | string | The user to check relationships for |
| `$context` | object&#124;null | Additional context for evaluation |
| `$contextualTuples` | TupleKeysInterface&lt;TupleKeyInterface&gt;&#124;null | Additional tuples for contextual evaluation |
| `$consistency` | Consistency&#124;null | Override the default consistency level |

#### Returns
FailureInterface&#124;SuccessInterface
 Success contains ListObjectsResponseInterface, Failure contains Throwable

#### listStores


```php
public function listStores(string|null $continuationToken = NULL, ?int $pageSize = NULL): FailureInterface|SuccessInterface
```

Lists all stores with pagination.

[View source](https://github.com/evansims/openfga-php/blob/main/src/ClientInterface.php#L422)

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$continuationToken` | string&#124;null | Token for pagination |
| `$pageSize` | ?int | Maximum number of stores to return |

#### Returns
FailureInterface&#124;SuccessInterface
 Success contains ListStoresResponseInterface, Failure contains Throwable

#### listTupleChanges


```php
public function listTupleChanges(StoreInterface|string $store, string|null $continuationToken = NULL, ?int $pageSize = NULL, string|null $type = NULL, DateTimeImmutable|null $startTime = NULL): FailureInterface|SuccessInterface
```

Lists changes to relationship tuples in a store.

[View source](https://github.com/evansims/openfga-php/blob/main/src/ClientInterface.php#L440)

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | StoreInterface&#124;string | The store to list changes for |
| `$continuationToken` | string&#124;null | Token for pagination |
| `$pageSize` | ?int | Maximum number of changes to return |
| `$type` | string&#124;null | Filter changes by type |
| `$startTime` | DateTimeImmutable&#124;null | Only include changes at or after this time (inclusive) |

#### Returns
FailureInterface&#124;SuccessInterface
 Success contains ListTupleChangesResponseInterface, Failure contains Throwable

#### listUsers


```php
public function listUsers(StoreInterface|string $store, AuthorizationModelInterface|string $model, string $object, string $relation, UserTypeFiltersInterface<UserTypeFilterInterface> $userFilters, object|null $context = NULL, TupleKeysInterface<TupleKeyInterface>|null $contextualTuples = NULL, Consistency|null $consistency = NULL): FailureInterface|SuccessInterface
```

Lists users that have a specific relationship with an object.


**List all users who can view a document:**
```php
$userFilters = new UserTypeFilters([
new UserTypeFilter(&#039;user&#039;) // Only include direct users, not groups
]);
$result = $client-&gt;listUsers(
store: &#039;store-id&#039;,
model: &#039;model-id&#039;,
object: &#039;document:budget&#039;,
relation: &#039;viewer&#039;,
userFilters: $userFilters
);
if ($result-&gt;success()) {
$users = $result-&gt;value()-&gt;getUsers();
echo &quot;Users who can view the budget document:\n&quot;;
foreach ($users as $user) {
echo &quot;- {$user}\n&quot;;
}
}
```

**Find both users and groups with access:**
```php
$userFilters = new UserTypeFilters([
new UserTypeFilter(&#039;user&#039;),
new UserTypeFilter(&#039;group&#039;)
]);
$result = $client-&gt;listUsers(
store: &#039;store-id&#039;,
model: &#039;model-id&#039;,
object: &#039;document:sensitive&#039;,
relation: &#039;editor&#039;,
userFilters: $userFilters
);
```

[View source](https://github.com/evansims/openfga-php/blob/main/src/ClientInterface.php#L496)

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | StoreInterface&#124;string | The store to query |
| `$model` | AuthorizationModelInterface&#124;string | The authorization model to use |
| `$object` | string | The object to check relationships for |
| `$relation` | string | The relationship to check |
| `$userFilters` | UserTypeFiltersInterface&lt;UserTypeFilterInterface&gt; | Filters for user types to include |
| `$context` | object&#124;null | Additional context for evaluation |
| `$contextualTuples` | TupleKeysInterface&lt;TupleKeyInterface&gt;&#124;null | Additional tuples for contextual evaluation |
| `$consistency` | Consistency&#124;null | Override the default consistency level |

#### Returns
FailureInterface&#124;SuccessInterface
 Success contains ListUsersResponseInterface, Failure contains Throwable

#### streamedListObjects


```php
public function streamedListObjects(StoreInterface|string $store, AuthorizationModelInterface|string $model, string $type, string $relation, string $user, object|null $context = NULL, TupleKeysInterface<TupleKeyInterface>|null $contextualTuples = NULL, Consistency|null $consistency = NULL): FailureInterface|SuccessInterface
```

Streams objects that a user has a specific relationship with. Returns all objects of a given type that the specified user has a relationship with, using a streaming response for memory-efficient processing of large result sets. This is ideal for handling thousands of objects without loading them all into memory.

[View source](https://github.com/evansims/openfga-php/blob/main/src/ClientInterface.php#L557)

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | StoreInterface&#124;string | The store to query |
| `$model` | AuthorizationModelInterface&#124;string | The authorization model to use |
| `$type` | string | The object type to find |
| `$relation` | string | The relationship to check |
| `$user` | string | The user to check relationships for |
| `$context` | object&#124;null | Additional context for evaluation |
| `$contextualTuples` | TupleKeysInterface&lt;TupleKeyInterface&gt;&#124;null | Additional tuples for contextual evaluation |
| `$consistency` | Consistency&#124;null | Override the default consistency level |

#### Returns
FailureInterface&#124;SuccessInterface
 Success contains Generator&lt;StreamedListObjectsResponseInterface&gt;, Failure contains Throwable

### Utility
#### assertLastRequest


```php
public function assertLastRequest(): HttpRequestInterface
```

Retrieves the last HTTP request made by the client.


**Accessing the last request for debugging:**
```php
$result = $client-&gt;check(
store: &#039;store-id&#039;,
model: &#039;model-id&#039;,
tupleKey: new TupleKey(&#039;user:anne&#039;, &#039;viewer&#039;, &#039;document:budget&#039;)
);
$lastRequest = $client-&gt;assertLastRequest();
echo &quot;Method: &quot; . $lastRequest-&gt;getMethod();
echo &quot;URL: &quot; . $lastRequest-&gt;getUri();
echo &quot;Headers: &quot; . json_encode($lastRequest-&gt;getHeaders());
```

[View source](https://github.com/evansims/openfga-php/blob/main/src/ClientInterface.php#L55)


#### Returns
HttpRequestInterface
 The last request

### Other
#### dsl


```php
public function dsl(string $dsl): FailureInterface|SuccessInterface
```

Parses a DSL string and returns an AuthorizationModel. The Domain Specific Language (DSL) provides a human-readable way to define authorization models using intuitive syntax for relationships and permissions. This method converts DSL text into a structured authorization model object.


**Parse a complete authorization model from DSL:**
```php
$dsl = &#039;
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
&#039;;
$result = $client-&gt;dsl($dsl);
if ($result-&gt;success()) {
$authModel = $result-&gt;value();
echo &quot;Parsed model with &quot; . count($authModel-&gt;getTypeDefinitions()) . &quot; types&quot;;
}
```

[View source](https://github.com/evansims/openfga-php/blob/main/src/ClientInterface.php#L283)

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$dsl` | string | The DSL string to parse |

#### Returns
FailureInterface&#124;SuccessInterface
 Success contains AuthorizationModelInterface, Failure contains Throwable

