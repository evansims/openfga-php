# Assertions

Assertions are test cases that check whether specific users should or shouldn’t have access to certain resources, based on your authorization model. They help you validate that your access rules behave as expected and catch mistakes early.

> [!TIP]
> These examples assume you're continuing from [Getting Started](/docs/GettingStarted.md), where we set up our common imports, and initialized the SDK as `$client`.

> [!TIP]
> You'll need a store and an authorization model to work with assertions. Learn how to create a store in [Stores](/docs/Stores.md), and assign it's ID as `$store`. Then, learn how to create an authorization model in [Authorization Models](/docs/AuthorizationModels.md), and assign it's ID as `$model`.

**Read assertions** with `Client::readAssertions()`:

```php
use OpenFGA\Responses\ReadAssertionsResponseInterface;

$result = $client->readAssertions(
    store: $store->getId(),
    model: $model->getId(),
);

success($result, fn(ReadAssertionsResponseInterface $response) => {
    foreach ($response->getAssertions() as $assertion) {
        echo "Assertion: {$assertion->getAssertion()} {$assertion->getRelation()} {$assertion->getObject()}\n";
    }
});
```

**Write assertions** with `Client::writeAssertions()`:

```php
use OpenFGA\Responses\WriteAssertionsResponseInterface;
use OpenFGA\Models\{Assertion, TupleKey};
use OpenFGA\Collections\Assertions;

$assertions = new Assertions([
    new Assertion(
        tupleKey: new TupleKey(
            user: 'user:anne',
            relation: 'reader',
            object: 'document:roadmap',
        ),
        expectation: true,
    ),
]);

$result = $client->writeAssertions(
    store: $store->getId(),
    model: $model->getId(),
    assertions: $assertions,
);

success($result, fn(WriteAssertionsResponseInterface $response) => {
    foreach ($response->getAssertions() as $assertion) {
        echo "Assertion: {$assertion->getAssertion()} {$assertion->getRelation()} {$assertion->getObject()}\n";
    }
});
```

## Next Steps

- [Getting Started](/docs/GettingStarted.md)
- [Authentication](/docs/Authentication.md)
- [Authorization Models](/docs/AuthorizationModels.md)
- [Relationship Tuples](/docs/RelationshipTuples.md)
- [Relationship Queries](/docs/Queries.md)
- [Assertions](/docs/Assertions.md)
- [Stores](/docs/Stores.md)
- [Results](/docs/Results.md)
