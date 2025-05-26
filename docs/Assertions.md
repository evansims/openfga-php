# Validating Your Model with Assertions

In OpenFGA, **Assertions** are essentially **tests for your authorization model**. They allow you to define expected outcomes for specific access scenarios, ensuring your model behaves exactly as you intend. Think of them as a safety net that helps you build and maintain a robust authorization system with confidence.

## Why are Assertions Important?

Investing a little time in writing assertions can save you a lot of headaches down the line. Here's why they are crucial:

- **Validation:** Assertions confirm that your authorization model correctly grants or denies access for critical user scenarios. For example, you can assert that an "admin" _should_ be able to delete a "document," while a "viewer" _should not_.
- **Prevent Regressions:** When you modify your authorization model (e.g., add new types, change relations), assertions act as automated tests. If a change inadvertently breaks an existing rule, your assertions will help you catch this before it impacts users.
- **Live Documentation:** Assertions serve as clear, testable documentation of your model's intended behavior. Anyone can look at the assertions to understand the expected access control outcomes for key relationships.
- **Increased Confidence:** Knowing that your model is backed by a suite of assertions gives you greater confidence in your authorization logic, especially as your application and its permission rules grow in complexity.

Assertions are stored _per authorization model_. Each assertion defines an expected outcome for a specific relationship check:

- `user:anne` **should have** `viewer` access to `document:roadmap`.
- `user:anne` **should NOT have** `editor` access to `document:roadmap`.

## Prerequisites

These examples assume:

1. You have initialized the SDK client as `$client`.
2. You have a `storeId` and have set it on the client: `$client->setStore($storeId);`.
3. You have a `modelId` (Authorization Model ID) and have set it on the client: `$client->setModel($modelId);`. Assertions are always associated with a specific authorization model.
4. Refer to [Getting Started](GettingStarted.md), [Managing Stores](Stores.md), and [Understanding Authorization Models](AuthorizationModels.md) for these initial setup steps.

For robust error handling beyond the `unwrap()` helper shown in these examples, please see our guide on [Results and Error Handling](Results.md).

```php
<?php
// Common setup for examples:
require_once __DIR__ . '/vendor/autoload.php'; // If running examples standalone

use OpenFGA\Client;
use OpenFGA\Results\unwrap;
use OpenFGA\Models\TupleKey;    // Represents the (user, relation, object) part of an assertion
use OpenFGA\Models\Assertion;   // Represents a single assertion (TupleKey + expectation)
use OpenFGA\Collections\Assertions; // Represents a collection of Assertion objects for writing
// Response interfaces for type hinting (optional but good practice)
use OpenFGA\Responses\ReadAssertionsResponseInterface;
use OpenFGA\Responses\WriteAssertionsResponseInterface; // Though Write usually returns a specific type or void

// Assuming $client is initialized and storeId & modelId are set:
// $fgaApiUrl = $_ENV['FGA_API_URL'] ?? 'http://localhost:8080';
// $storeId = $_ENV['FGA_STORE_ID'] ?? 'your_test_store_id';
// $modelId = $_ENV['FGA_MODEL_ID'] ?? 'your_test_model_id'; // Crucial for assertions
// $client = new Client(url: $fgaApiUrl);
// $client->setStore($storeId);
// $client->setModel($modelId); // Important: sets the model for these assertion operations
?>
```

## Structure of an Assertion

An assertion in OpenFGA, represented by the `OpenFGA\Models\Assertion` object in the PHP SDK, consists of two main parts:

1. **`tuple_key`** (`OpenFGA\Models\TupleKey`): This specifies the relationship being tested. It includes:

   - `user` (string): The user or userset (e.g., `user:anne`, `group:auditors#member`).
   - `relation` (string): The relation or permission (e.g., `viewer`, `can_edit`).
   - `object` (string): The resource (e.g., `document:financial_report`).

2. **`expectation`** (boolean): This defines the expected outcome of a `check()` for the given `tuple_key`.
   - `true`: You expect the relationship to exist (access should be granted).
   - `false`: You expect the relationship _not_ to exist (access should be denied).

## Writing Assertions (`Client::writeAssertions()`)

You write assertions for a specific authorization model. The `writeAssertions()` method allows you to define a set of assertions for the model ID currently configured on the client (or one passed directly).

**Important Behavior:** Calling `writeAssertions()` **overwrites all existing assertions** for the specified `authorization_model_id`. You are essentially providing the complete set of assertions for that model each time.

**Parameters:**

- `assertions` (required `OpenFGA\Collections\Assertions`): A collection of `Assertion` objects.
- `authorization_model_id` (optional string): If you haven't set the model ID on the client using `$client->setModel()`, or if you need to override it for this specific call, you **must** provide it here. Assertions are always tied to a model.

```php
<?php
// Example: Writing Assertions for the current model ($client->getModel())

// Assertion 1: User 'anne' SHOULD BE a 'viewer' of 'document:roadmap'
$assertionAnneCanViewRoadmap = new Assertion(
    tupleKey: new TupleKey(
        user: 'user:anne',
        relation: 'viewer',
        object: 'document:roadmap'
    ),
    expectation: true // true = access expected
);

// Assertion 2: User 'bob' SHOULD NOT BE an 'editor' of 'document:roadmap'
$assertionBobCannotEditRoadmap = new Assertion(
    tupleKey: new TupleKey(
        user: 'user:bob',
        relation: 'editor',
        object: 'document:roadmap'
    ),
    expectation: false // false = access NOT expected
);

// Assertion 3: Any 'user' SHOULD BE a 'viewer' of 'document:public_guide'
$assertionAnyoneCanViewPublicGuide = new Assertion(
    tupleKey: new TupleKey(
        user: 'user:*', // Using a wildcard
        relation: 'viewer',
        object: 'document:public_guide'
    ),
    expectation: true
);

$assertionsToWrite = new Assertions([
    $assertionAnneCanViewRoadmap,
    $assertionBobCannotEditRoadmap,
    $assertionAnyoneCanViewPublicGuide,
]);

try {
    // We rely on the model ID set via $client->setModel() for this call.
    // If not set on the client, you MUST pass it as a parameter:
    // $client->writeAssertions(assertions: $assertionsToWrite, authorizationModelId: 'your_specific_model_id');

    unwrap($client->writeAssertions(assertions: $assertionsToWrite));

    echo "Assertions written successfully for model ID: " . $client->getModel() . "\n";
    echo "Remember: This overwrites any previous assertions for this model.\n";

} catch (Throwable $e) {
    echo "Error writing assertions: " . $e->getMessage() . "\n";
    // This could be due to an invalid model ID or issues with the OpenFGA server.
}
?>
```

## Reading Assertions (`Client::readAssertions()`)

You can retrieve all assertions associated with a specific authorization model. This is useful for reviewing existing tests or for custom validation logic.

**Parameters:**

- `authorization_model_id` (optional string): If you haven't set the model ID on the client, or if you need to override it, provide it here.

```php
<?php
// Example: Reading Assertions for the current model ($client->getModel())

try {
    // We rely on the model ID set via $client->setModel() for this call.
    // If not set on the client, you MUST pass it as a parameter:
    // $client->readAssertions(authorizationModelId: 'your_specific_model_id');

    /** @var ReadAssertionsResponseInterface $response */
    $response = unwrap($client->readAssertions());

    $retrievedAssertions = $response->getAssertions();

    echo "Assertions for model ID '" . $client->getModel() . "':\n";
    if (empty($retrievedAssertions)) {
        echo "No assertions found for this model.\n";
    } else {
        foreach ($retrievedAssertions as $assertion) {
            $tupleKey = $assertion->getTupleKey();
            $expected = $assertion->getExpectation() ? 'SHOULD HAVE' : 'SHOULD NOT HAVE';
            echo "- User '{$tupleKey->getUser()}' {$expected} '{$tupleKey->getRelation()}' access to '{$tupleKey->getObject()}'.\n";
        }
    }
} catch (Throwable $e) {
    echo "Error reading assertions: " . $e->getMessage() . "\n";
}
?>
```

## How Assertions Are Used

While the SDK provides methods to write and read assertions, their primary execution happens on the **OpenFGA server**:

- **Model Validation:** When you create or update an authorization model that has assertions defined, the OpenFGA server can automatically run these assertions. If any assertion fails (e.g., the model grants access when the assertion expects it to be denied), the model creation/update operation may fail or return warnings, depending on the server configuration.
- **OpenFGA Playground:** The OpenFGA Playground (a UI tool for developing and testing models) heavily utilizes assertions. You can write your model, add tuples, and define assertions directly in the Playground, and it will immediately show you if your assertions pass or fail against the current model and data. This provides a very tight feedback loop during development.
- **Custom Testing:** You can use the `readAssertions()` method to fetch assertions and then, in your own testing framework, programmatically create the necessary [Relationship Tuples](RelationshipTuples.md) and perform [Queries](Queries.md) (like `check()`) to verify if the outcomes match the `expectation` in each assertion.

The SDK itself doesn't have a single "run assertions" command that triggers server-side validation of all assertions for a model after they are written. The validation is more of an implicit behavior of the server during model changes or an explicit feature of tools like the Playground.

## Next Steps

Effectively using assertions is a key part of maintaining a healthy authorization system.

- If your assertions reveal unexpected behavior, it's time to revisit your **[Authorization Model](AuthorizationModels.md)** or the test **[Relationship Tuples](RelationshipTuples.md)** you're using.
- For more complex permission checks beyond simple assertions, explore advanced **[Query Techniques](Queries.md)**.
- Regularly review and update your assertions as your application's authorization requirements evolve.
