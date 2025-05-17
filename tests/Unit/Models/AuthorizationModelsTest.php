<?php

declare(strict_types=1);

use OpenFGA\Models\{AuthorizationModel, AuthorizationModels, TypeDefinition, TypeDefinitionRelations, TypeDefinitions, Userset};

test('current returns AuthorizationModelInterface or null', function (): void {
    $typeDefinitions = new TypeDefinitions([
        new TypeDefinition(
            'document',
            new TypeDefinitionRelations([
                'reader' => new Userset((object) ['this' => (object) []]),
            ]),
        ),
    ]);

    $model = new AuthorizationModel('test-id', '1.1', $typeDefinitions);
    $models = new AuthorizationModels([$model]);

    expect($models->current())->toBe($model);

    // Test with empty collection
    $emptyModels = new AuthorizationModels();
    expect($emptyModels->current())->toBeNull();
});

test('offsetGet returns AuthorizationModelInterface or null', function (): void {
    $typeDefinitions = new TypeDefinitions([
        new TypeDefinition(
            'document',
            new TypeDefinitionRelations([
                'reader' => new Userset((object) ['this' => (object) []]),
            ]),
        ),
    ]);

    $model = new AuthorizationModel('test-id', '1.1', $typeDefinitions);
    $models = new AuthorizationModels([$model]);

    expect($models[0])->toBe($model)
        ->and($models[1])->toBeNull();

    // Test with string key
    expect($models['0'])->toBe($model);

    // Test with empty collection
    $emptyModels = new AuthorizationModels();
    expect($emptyModels[0])->toBeNull();
});

test('count returns correct number of models', function (): void {
    $typeDefinitions = new TypeDefinitions([
        new TypeDefinition(
            'document',
            new TypeDefinitionRelations([
                'reader' => new Userset((object) ['this' => (object) []]),
            ]),
        ),
    ]);

    $model1 = new AuthorizationModel('test-id-1', '1.1', $typeDefinitions);
    $model2 = new AuthorizationModel('test-id-2', '1.1', $typeDefinitions);

    $models = new AuthorizationModels([$model1, $model2]);

    expect(\count($models))->toBe(2);

    // Test with empty collection
    $emptyModels = new AuthorizationModels();
    expect(\count($emptyModels))->toBe(0);
});

test('iteration works correctly', function (): void {
    $typeDefinitions = new TypeDefinitions([
        new TypeDefinition(
            'document',
            new TypeDefinitionRelations([
                'reader' => new Userset((object) ['this' => (object) []]),
            ]),
        ),
    ]);

    $model1 = new AuthorizationModel('test-id-1', '1.1', $typeDefinitions);
    $model2 = new AuthorizationModel('test-id-2', '1.1', $typeDefinitions);

    $models = new AuthorizationModels([$model1, $model2]);

    // Test iteration with foreach
    $iterated = [];
    foreach ($models as $model) {
        $iterated[] = $model;
    }

    expect($iterated)->toHaveCount(2);

    // Reset the collection's internal pointer
    $models->rewind();

    // Test manual iteration
    $manuallyIterated = [];
    while ($models->valid()) {
        $manuallyIterated[] = $models->current();
        $models->next();
    }

    expect($manuallyIterated)->toHaveCount(2)
        ->and($manuallyIterated[0])->toBe($model1)
        ->and($manuallyIterated[1])->toBe($model2);
});
