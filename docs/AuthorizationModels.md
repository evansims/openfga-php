# Understanding Authorization Models in OpenFGA

An **Authorization Model** is like the DNA of your permission system - it defines the rules about who can do what, without specifying individual permissions. Think of it as creating the grammar for a language before writing the sentences.

**🏗️ What does a model define?**
- **Types of things** in your system (users, documents, teams)
- **Relationships** between them (owner, viewer, member)  
- **Permission rules** (editors can also view, team members inherit folder access)

**🔄 Model vs. Tuples:**
- **Model:** "Documents can have viewers and editors" (the rules)
- **Tuples:** "Alice is a viewer of Document123" (specific permissions)

**Quick Navigation:** [🎯 Model Basics](#core-components-of-an-authorization-model) • [📝 DSL Guide](#the-openfga-dsl-domain-specific-language) • [⚡ Quick Examples](#workflow-for-authorization-models) • [🔧 Management API](#1-transforming-dsl-to-an-authorizationmodel-object-client-side)

**New to authorization models?** Start with [DSL Examples](#the-openfga-dsl-domain-specific-language) to see models in action.

**Ready to build?** Jump to [Creating Your First Model](#workflow-for-authorization-models).

## Core Components of an Authorization Model

### 📦 1. Type Definitions
The "things" in your system that can have permissions:

```
user     → Alice, Bob, Charlie
document → Budget2024.pdf, ProjectPlan.doc  
folder   → /Projects, /Archive
team     → Engineering, Marketing
```

### 🔗 2. Relations  
The "verbs" that connect types together:

**Direct Relations:**
```
define owner: [user]           → "Alice owns Budget2024.pdf"
define member: [user]          → "Bob is a member of Engineering team"  
```

**Computed Relations:**
```
define viewer: editor          → "Anyone who can edit can also view"
define viewer: owner or member → "Owners OR members can view"
```

### ⚡ 3. Conditions (Advanced)
Context-based rules for dynamic permissions:

```
define viewer: [user] with valid_ip
define editor: [user] with business_hours
```

**💡 Start Simple:** Most applications only need types and relations. Add conditions later when you need context-aware permissions.

## The OpenFGA DSL (Domain Specific Language)

OpenFGA provides a human-readable Domain Specific Language (DSL) to define your authorization model. It's a clear and concise way to express your types and their relationships. The PHP SDK allows you to write your model in this DSL and then transform it into the structured format that the OpenFGA server requires.

**A Simple DSL Example:**

Let's consider a basic document sharing system:

```fsharp
// model_schema_version defines which version of the FGA DSL syntax is used.
// 1.1 is a common version.
model
  schema 1.1

// 'type user' defines a type. Here, it's just a placeholder for users.
// It has no relations itself in this simple model.
type user

// 'type document' defines another type, representing documents.
type document
  // 'relations' block defines how other types (or this type) can relate to 'document'.
  relations
    // 'define owner: [user]' means a 'document' can have an 'owner',
    // and an 'owner' must be of type 'user'. This is a direct relationship.
    define owner: [user]

    // 'define editor: [user] or owner' means an 'editor' can be:
    // 1. Directly assigned as a 'user' (e.g., "user:bob is an editor of doc:budget").
    // 2. OR, anyone who is an 'owner' is automatically also an 'editor'.
    // This is a computed relation (owner implies editor) and a direct assignment.
    define editor: [user] or owner

    // 'define viewer: [user] or editor' means a 'viewer' can be:
    // 1. Directly assigned as a 'user'.
    // 2. OR, anyone who is an 'editor' (and thus also anyone who is an 'owner')
    //    is automatically also a 'viewer'.
    define viewer: [user] or editor
```

**Key DSL Concepts Illustrated:**

- `model schema 1.1`: Specifies the version of the OpenFGA language. `1.1` is a common modern version.
- `type <name>`: Declares a new type (e.g., `user`, `document`).
- `relations`: A block within a type definition that lists its possible relationships.
- `define <relation_name>: [<assignable_type>]`: Defines a direct relationship. An object of type `<assignable_type>` can be directly assigned this relation.
- `define <relation_name>: <another_relation>`: Defines a computed relationship. Users who have `<another_relation>` automatically gain `<relation_name>`.
- `define <relation_name>: [<assignable_type>] or <another_relation>`: A union. The relation can be satisfied either by direct assignment of `<assignable_type>` OR by having `<another_relation>`.
- Tuples-to-Usersets (e.g., `define X: Y from Z`): A more advanced concept where relation `Y` on an object `Z` (which itself is related to the current object) grants relation `X`. This is useful for hierarchical permissions (e.g., permissions inherited from a parent folder). Our simple example doesn't use this, but you'll see it in more complex models.

## Prerequisites

These examples assume:

1. You have initialized the SDK client as `$client`.
2. You have a `storeId` and have set it on the client: `$client->setStore($storeId);`.
3. Refer to [Getting Started](GettingStarted.md) for client initialization and [Stores](Stores.md) for creating/managing stores and getting a `$storeId`.
4. The variable `$modelId` in later examples refers to the unique identifier of an authorization model after it has been created on the server.

For robust error handling beyond the `unwrap()` helper shown, please see our guide on [Results and Error Handling](Results.md).

```php
<?php
// Common setup for examples:
require_once __DIR__ . '/vendor/autoload.php'; // If running examples standalone

use OpenFGA\Client;
use OpenFGA\Models\AuthorizationModelInterface; // For type hinting
use OpenFGA\Responses\CreateAuthorizationModelResponseInterface;
use OpenFGA\Responses\GetAuthorizationModelResponseInterface;
use OpenFGA\Responses\ListAuthorizationModelsResponseInterface;

use function OpenFGA\Results\unwrap;

// Assuming $client is initialized as shown in GettingStarted.md
// $client = new Client(url: $_ENV['FGA_API_URL'] ?? 'http://localhost:8080');
?>
```

## Workflow for Authorization Models

Working with authorization models generally follows these steps:

1. **Write your model in DSL:** Define your types and relations.
2. **Transform DSL to an `AuthorizationModel` object (Client-Side):** Use `Client::dsl()` to parse your DSL string into an object the SDK can work with. This is a local operation.
3. **Create the Authorization Model on the Server:** Use `Client::createAuthorizationModel()` to send the transformed model (specifically its `typeDefinitions` and `conditions`) to your OpenFGA server, saving it within your selected store. This returns a `$modelId`.
4. **Use the `$modelId`:** For most subsequent operations (writing tuples, checks, listing objects), you'll need this `$modelId`. You can set it on the client for convenience using `Client::setModel($modelId)`.

## 1. Transforming DSL to an `AuthorizationModel` Object (Client-Side)

The `Client::dsl()` method takes your DSL string and parses it into an `OpenFGA\Models\AuthorizationModelInterface` object. This object contains the structured `typeDefinitions` and `conditions` that are needed to actually create the model on the server.

```php
<?php
$dslString = <<<DSL
model
  schema 1.1
type user
type document
  relations
    define owner: [user]
    define editor: [user] or owner
    define viewer: [user] or editor
DSL;

try {
    /** @var AuthorizationModelInterface $modelObject */
    $modelObject = unwrap($client->dsl($dslString));

    echo "DSL transformed successfully (client-side)!\n";

    // These are the parts you'll need to send to the server:
    $typeDefinitions = $modelObject->getTypeDefinitions();
    $conditions = $modelObject->getConditions(); // Will be empty for this simple DSL

    // You can inspect them if needed:
    // print_r($typeDefinitions->toArray());
    // print_r($conditions->toArray());

} catch (Throwable $e) {
    echo "Error transforming DSL: " . $e->getMessage() . "\n";
    // This typically happens if there's a syntax error in your DSL.
}
?>
```

This step is purely client-side; no request is made to the OpenFGA server yet.

## 2. Creating an Authorization Model (Server-Side)

Once you have the `typeDefinitions` (and optionally `conditions`) from your transformed DSL, you can create the model on the OpenFGA server using `Client::createAuthorizationModel()`. This action stores the model in the currently selected Store (as set by `$client->setStore()`).

The server will assign a unique ID to this model, which you'll need for most other operations.

```php
<?php
// Assuming $modelObject (containing typeDefinitions and conditions)
// is available from the previous step.

// Ensure $typeDefinitions and $conditions are available
if (!isset($typeDefinitions)) {
    echo "Please run the DSL transformation step first.\n";
} else {
    try {
        /** @var CreateAuthorizationModelResponseInterface $response */
        $response = unwrap($client->createAuthorizationModel(
            store: $storeId,
            typeDefinitions: $typeDefinitions,
            conditions: $conditions // Pass $conditions, even if empty
        ));

        $modelId = $response->getId(); // <-- This ID is very important!
        echo "Authorization Model created successfully on the server!\n";
        echo "Model ID: " . $modelId . "\n";

        // You will typically save this $modelId or set it on the client.

    } catch (Throwable $e) {
        echo "Error creating authorization model on server: " . $e->getMessage() . "\n";
        // This could be due to issues connecting to FGA, an invalid store ID, etc.
    }
}
?>
```

## 3. Setting the Active Model ID on the Client

Similar to `Client::setStore()`, you can use `Client::setModel(string $modelId)` to tell the client which authorization model to use for subsequent operations like checks, writes, or listing objects. This is convenient as you won't have to pass the `$modelId` in every call.

```php
<?php
// Assuming $modelId is available from the createAuthorizationModel step.
if (empty($modelId)) {
    echo "Please ensure a model is created and \$modelId is set to run this example.\n";
} else {
    $client->setModel($modelId);
    echo "Client is now configured to use Model ID: {$modelId} for subsequent operations.\n";
}
?>
```

## 4. Listing Authorization Models

You can list all authorization models within the currently selected store. This is useful for finding existing model IDs or for management purposes.

The `listAuthorizationModels()` method supports pagination.

```php
<?php
// Assumes client store is set: $client->setStore($storeId);
try {
    /** @var ListAuthorizationModelsResponseInterface $response */
    $response = unwrap($client->listAuthorizationModels(store: $storeId, pageSize: 5)); // Get up to 5 models

    echo "Authorization Models in store '{$client->getStore()}':\n";
    if (empty($response->getModels())) {
        echo "No models found in this store.\n";
    } else {
        foreach ($response->getModels() as $model) {
            echo "- Model ID: " . $model->getId() . "\n";
        }
    }

    // For pagination, use $response->getContinuationToken() and pass it
    // in the next call via the `continuationToken` parameter.

} catch (Throwable $e) {
    echo "Error listing authorization models: " . $e->getMessage() . "\n";
}
?>
```

## 5. Getting a Specific Authorization Model

If you have a model's ID, you can fetch its full definition from the server. This is useful for inspecting an existing model. The response includes the type definitions and conditions.

You can also convert the server-side model object back into its DSL representation using the `AuthorizationModel::dsl()` method on the model object returned by `getAuthorizationModel()`.

```php
<?php
// Assuming $modelId is known (e.g., from creation or listing)
// and client store is set: $client->setStore($storeId);

if (empty($modelId)) { // Use the one set on the client if available
    $modelId = $client->getModel();
}

if (empty($modelId)) {
    echo "Please ensure a \$modelId is available (e.g., create one or set on client) to run this example.\n";
} else {
    try {
        /** @var GetAuthorizationModelResponseInterface $response */
        $response = unwrap($client->getAuthorizationModel(store: $storeId, model: $modelId));

        $retrievedModel = $response->getModel();

        echo "Successfully retrieved Model ID: " . $retrievedModel->getId() . "\n";

        // You can now inspect its properties, e.g.:
        // print_r($retrievedModel->getTypeDefinitions()->toArray());

        // Or convert it back to DSL:
        echo "Model DSL representation:\n";
        echo $retrievedModel->dsl();

    } catch (Throwable $e) {
        // This could be an OpenFGA\Errors\ModelNotFoundError for example
        echo "Error getting model '{$modelId}': " . $e->getMessage() . "\n";
    }
}
?>
```

## Next Steps

Once your Authorization Model is defined, created on the server, and its ID is set on the client, you're ready to define who has access to what. This is done by:

- **[Writing Relationship Tuples](RelationshipTuples.md):** These are the data that link specific users to specific relations on specific objects (e.g., "user:anne is an editor of document:budget").

After you have some tuples, you can:

- **[Perform Queries](Queries.md):** Ask questions like "Can user:anne view document:budget?" (`check()`) or "What documents can user:anne view?" (`listObjects()`).
- **[Write Assertions](Assertions.md):** Test your model to ensure it behaves as expected under various scenarios.

A well-defined Authorization Model is the foundation for all authorization decisions in OpenFGA.
