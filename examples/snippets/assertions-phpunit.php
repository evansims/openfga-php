<?php

declare(strict_types=1);

use OpenFGA\Client;
use PHPUnit\Framework\TestCase;

use function OpenFGA\{dsl, model, store, tuple};

final class AuthorizationModelTest extends TestCase
{
    private Client $client;

    private string $modelId;

    private string $storeId;

    protected function setUp(): void
    {
        $this->client = new Client(
            url: $_ENV['FGA_API_URL'] ?? 'http://localhost:8080',
        );

        // Create test store and model
        $this->storeId = store(
            name: 'test-' . uniqid(),
            client: $this->client,
        );

        $authModel = dsl(file_get_contents(__DIR__ . '/../../authorization-models/main.fga'));

        $this->modelId = model(
            model: $authModel,
            store: $this->storeId,
            client: $this->client,
        );
    }

    protected function tearDown(): void
    {
        // Clean up test store
        $this->client->deleteStore($this->storeId);
    }

    public function testDocumentPermissions(): void
    {
        $assertions = require __DIR__ . '/../../authorization-models/document-system.assertions.php';

        $result = $this->client->writeAssertions(
            store: $this->storeId,
            model: $this->modelId,
            assertions: $assertions,
        );

        $this->assertTrue($result->succeeded());

        // Verify the assertions by running checks
        $checks = $this->client->readAssertions(
            store: $this->storeId,
            model: $this->modelId,
        )->unwrap();

        foreach ($checks->getAssertions() as $assertion) {
            $checkResult = $this->client->check(
                store: $this->storeId,
                model: $this->modelId,
                tupleKey: tuple(
                    user: $assertion->getUser(),
                    relation: $assertion->getRelation(),
                    object: $assertion->getObject(),
                ),
            );

            $this->assertTrue($checkResult->succeeded());
            $this->assertEquals(
                $assertion->getExpectation(),
                $checkResult->unwrap()->getAllowed(),
                sprintf(
                    'Failed assertion: %s %s %s',
                    $assertion->getUser(),
                    $assertion->getRelation(),
                    $assertion->getObject(),
                ),
            );
        }
    }
}
