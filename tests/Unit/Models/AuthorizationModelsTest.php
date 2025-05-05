<?php

declare(strict_types=1);

use OpenFGA\Models\{
    AuthorizationModel,
    AuthorizationModels,
    TypeDefinitions,
    Conditions,
    TypeName
};

// AuthorizationModel Tests
it('creates an authorization model instance correctly', function () {
    $id = 'test-model-id';
    $schemaVersion = '1.1';
    $typeDefinitions = new TypeDefinitions([
        ['type' => 'document', 'relations' => ['reader' => ['this', 'user']]]
    ]);
    $conditions = new Conditions([
        'condition_name' => [
            'name' => 'condition_name',
            'expression' => 'param1 == 1',
            'parameters' => [
                ['name' => 'param1', 'type_name' => TypeName::INT->value],
            ],
        ],
    ]);

    $model = new AuthorizationModel(
        id: $id,
        schemaVersion: $schemaVersion,
        typeDefinitions: $typeDefinitions,
        conditions: $conditions
    );

    expect($model)->toBeInstanceOf(AuthorizationModel::class)
        ->and($model->id)->toBe($id)
        ->and($model->schemaVersion)->toBe($schemaVersion)
        ->and($model->typeDefinitions)->toBe($typeDefinitions)
        ->and($model->conditions)->toBe($conditions);
});

it('creates an authorization model without conditions', function () {
    $id = 'test-model-id';
    $schemaVersion = '1.1';
    $typeDefinitions = new TypeDefinitions([
        ['type' => 'document', 'relations' => ['reader' => ['this', 'user']]]
    ]);

    $model = new AuthorizationModel(
        id: $id,
        schemaVersion: $schemaVersion,
        typeDefinitions: $typeDefinitions
    );

    expect($model)->toBeInstanceOf(AuthorizationModel::class)
        ->and($model->id)->toBe($id)
        ->and($model->schemaVersion)->toBe($schemaVersion)
        ->and($model->typeDefinitions)->toBe($typeDefinitions)
        ->and($model->conditions)->toBeNull();
});

it('converts authorization model to array', function () {
    $id = 'test-model-id';
    $schemaVersion = '1.1';
    $typeDefinitions = new TypeDefinitions([
        ['type' => 'document', 'relations' => ['reader' => ['this', 'user']]]
    ]);
    $conditions = new Conditions([
        'condition_name' => [
            'name' => 'condition_name',
            'expression' => 'param1 == 1',
            'parameters' => [
                ['name' => 'param1', 'type_name' => TypeName::INT->value],
            ],
        ],
    ]);

    $model = new AuthorizationModel(
        id: $id,
        schemaVersion: $schemaVersion,
        typeDefinitions: $typeDefinitions,
        conditions: $conditions
    );

    $array = $model->toArray();

    expect($array)->toBeArray()
        ->and($array['id'])->toBe($id)
        ->and($array['schema_version'])->toBe($schemaVersion)
        ->and($array['type_definitions'])->toBe($typeDefinitions->toArray())
        ->and($array['conditions'])->toBe($conditions->toArray());
});

it('converts authorization model with null conditions to array', function () {
    $id = 'test-model-id';
    $schemaVersion = '1.1';
    $typeDefinitions = new TypeDefinitions([
        ['type' => 'document', 'relations' => ['reader' => ['this', 'user']]]
    ]);

    $model = new AuthorizationModel(
        id: $id,
        schemaVersion: $schemaVersion,
        typeDefinitions: $typeDefinitions
    );

    $array = $model->toArray();

    expect($array)->toBeArray()
        ->and($array['id'])->toBe($id)
        ->and($array['schema_version'])->toBe($schemaVersion)
        ->and($array['type_definitions'])->toBe($typeDefinitions->toArray())
        ->and($array['conditions'])->toBeNull();
});

it('creates authorization model from array', function () {
    $data = [
        'id' => 'test-model-id',
        'schema_version' => '1.1',
        'type_definitions' => [
            ['type' => 'document', 'relations' => ['reader' => ['this', 'user']]],
        ],
        'conditions' => [
            'condition_name' => [
                'name' => 'condition_name',
                'expression' => 'param1 == 1',
                'parameters' => [
                    ['name' => 'param1', 'type_name' => TypeName::INT->value],
                ],
            ],
        ],
    ];

    $model = AuthorizationModel::fromArray($data);

    expect($model)->toBeInstanceOf(AuthorizationModel::class)
        ->and($model->id)->toBe($data['id'])
        ->and($model->schemaVersion)->toBe($data['schema_version'])
        ->and($model->typeDefinitions)->toBeInstanceOf(TypeDefinitions::class)
        ->and($model->conditions)->toBeInstanceOf(Conditions::class);
        
    // Assert specific parts of type definitions instead of exact match
    expect($model->typeDefinitions[0]->type)->toBe('document')
        ->and(array_keys($model->typeDefinitions[0]->relations))->toContain('reader');
        
    // Assert specific parts of conditions instead of exact array match
    $conditions = $model->conditions->toArray();
    $firstCondition = reset($conditions);
    expect($firstCondition['expression'])->toBe('param1 == 1')
        ->and($firstCondition['parameters'][0]['type_name'])->toBe(TypeName::INT->value);
});

it('creates authorization model from array without conditions', function () {
    $data = [
        'id' => 'test-model-id',
        'schema_version' => '1.1',
        'type_definitions' => [
            ['type' => 'document', 'relations' => ['reader' => ['this', 'user']]],
        ],
    ];

    $model = AuthorizationModel::fromArray($data);

    expect($model)->toBeInstanceOf(AuthorizationModel::class)
        ->and($model->id)->toBe($data['id'])
        ->and($model->schemaVersion)->toBe($data['schema_version'])
        ->and($model->typeDefinitions)->toBeInstanceOf(TypeDefinitions::class)
        ->and($model->conditions)->toBeNull();
        
    // Assert specific parts of type definitions instead of exact match
    expect($model->typeDefinitions[0]->type)->toBe('document')
        ->and(array_keys($model->typeDefinitions[0]->relations))->toContain('reader');
});

// AuthorizationModels Collection Tests
it('creates an empty authorization models collection', function () {
    $collection = new AuthorizationModels();

    expect($collection)->toBeInstanceOf(AuthorizationModels::class)
        ->and($collection->count())->toBe(0)
        ->and($collection->toArray())->toBe([]);
});

it('adds authorization model to collection', function () {
    $model = new AuthorizationModel(
        id: 'test-model-id',
        schemaVersion: '1.1',
        typeDefinitions: new TypeDefinitions([
            ['type' => 'document', 'relations' => ['reader' => ['this', 'user']]],
        ])
    );

    $collection = new AuthorizationModels();
    $collection->add($model);

    expect($collection->count())->toBe(1)
        ->and($collection[0])->toBe($model);
});

it('gets current authorization model from collection', function () {
    $model1 = new AuthorizationModel(
        id: 'model-1',
        schemaVersion: '1.1',
        typeDefinitions: new TypeDefinitions([
            ['type' => 'document', 'relations' => ['reader' => ['this', 'user']]],
        ])
    );

    $model2 = new AuthorizationModel(
        id: 'model-2',
        schemaVersion: '1.1',
        typeDefinitions: new TypeDefinitions([
            ['type' => 'folder', 'relations' => ['writer' => ['this', 'user']]],
        ])
    );

    $collection = new AuthorizationModels();
    $collection->add($model1);
    $collection->add($model2);

    // Test iterator functionality
    $collection->rewind();
    expect($collection->current())->toBe($model1);

    $collection->next();
    expect($collection->current())->toBe($model2);
});

it('gets authorization model by offset', function () {
    $model1 = new AuthorizationModel(
        id: 'model-1',
        schemaVersion: '1.1',
        typeDefinitions: new TypeDefinitions([
            ['type' => 'document', 'relations' => ['reader' => ['this', 'user']]],
        ])
    );

    $model2 = new AuthorizationModel(
        id: 'model-2',
        schemaVersion: '1.1',
        typeDefinitions: new TypeDefinitions([
            ['type' => 'folder', 'relations' => ['writer' => ['this', 'user']]],
        ])
    );

    $collection = new AuthorizationModels();
    $collection->add($model1);
    $collection->add($model2);

    expect($collection->offsetGet(0))->toBe($model1)
        ->and($collection->offsetGet(1))->toBe($model2)
        ->and($collection->offsetGet(2))->toBeNull();
});

it('creates authorization models collection from array', function () {
    $data = [
        [
            'id' => 'model-1',
            'schema_version' => '1.1',
            'type_definitions' => [
                ['type' => 'document', 'relations' => ['reader' => ['this', 'user']]],
            ],
        ],
        [
            'id' => 'model-2',
            'schema_version' => '1.1',
            'type_definitions' => [
                ['type' => 'folder', 'relations' => ['writer' => ['this', 'user']]],
            ],
            'conditions' => [
                'condition_name' => [
                    'name' => 'condition_name',
                    'expression' => 'param1 == 1',
                    'parameters' => [
                        ['name' => 'param1', 'type_name' => TypeName::INT->value],
                    ],
                ],
            ],
        ],
    ];

    $collection = AuthorizationModels::fromArray($data);

    expect($collection)->toBeInstanceOf(AuthorizationModels::class)
        ->and($collection->count())->toBe(2)
        ->and($collection[0])->toBeInstanceOf(AuthorizationModel::class)
        ->and($collection[0]->id)->toBe('model-1')
        ->and($collection[1])->toBeInstanceOf(AuthorizationModel::class)
        ->and($collection[1]->id)->toBe('model-2')
        ->and($collection[1]->conditions)->not->toBeNull();
});

it('converts authorization models collection to array', function () {
    $model1 = new AuthorizationModel(
        id: 'model-1',
        schemaVersion: '1.1',
        typeDefinitions: new TypeDefinitions([
            ['type' => 'document', 'relations' => ['reader' => ['this', 'user']]],
        ])
    );

    $model2 = new AuthorizationModel(
        id: 'model-2',
        schemaVersion: '1.1',
        typeDefinitions: new TypeDefinitions([
            ['type' => 'folder', 'relations' => ['writer' => ['this', 'user']]],
        ]),
        conditions: new Conditions([
            'condition_name' => [
                'name' => 'condition_name',
                'expression' => 'param1 == 1',
                'parameters' => [
                    ['name' => 'param1', 'type_name' => TypeName::INT->value],
                ],
            ],
        ])
    );

    $collection = new AuthorizationModels();
    $collection->add($model1);
    $collection->add($model2);

    $array = $collection->toArray();

    expect($array)->toBeArray()
        ->and($array)->toHaveCount(2)
        ->and($array[0])->toBe($model1->toArray())
        ->and($array[1])->toBe($model2->toArray());
});

it('iterates over authorization models collection', function () {
    $model1 = new AuthorizationModel(
        id: 'model-1',
        schemaVersion: '1.1',
        typeDefinitions: new TypeDefinitions([
            ['type' => 'document', 'relations' => ['reader' => ['this', 'user']]],
        ])
    );

    $model2 = new AuthorizationModel(
        id: 'model-2',
        schemaVersion: '1.1',
        typeDefinitions: new TypeDefinitions([
            ['type' => 'folder', 'relations' => ['writer' => ['this', 'user']]],
        ])
    );

    $collection = new AuthorizationModels();
    $collection->add($model1);
    $collection->add($model2);

    $models = [];
    foreach ($collection as $model) {
        $models[] = $model;
    }

    expect($models)->toHaveCount(2)
        ->and($models[0])->toBe($model1)
        ->and($models[1])->toBe($model2);
});
