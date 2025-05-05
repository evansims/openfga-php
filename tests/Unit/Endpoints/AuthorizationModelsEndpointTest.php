<?php

declare(strict_types=1);

use OpenFGA\Models\{TypeDefinitions, Conditions, TypeName};
use OpenFGA\RequestOptions\{ListAuthorizationModelsOptions, GetAuthorizationModelOptions, CreateAuthorizationModelOptions};
use OpenFGA\Responses\{ListAuthorizationModelsResponse, GetAuthorizationModelResponse, CreateAuthorizationModelResponse};
use OpenFGA\Exceptions\{ApiEndpointException, ApiValidationException};

it('lists authorization models successfully', function () {
    $storeId = 'test_store_id';
    $mockResponse = [
        'authorization_models' => [
            [
                'id' => 'model_id_1',
                'schema_version' => '1.1',
                'type_definitions' => [
                    ['type' => 'document', 'relations' => ['reader' => ['this', 'user']]],
                ],
                'created_at' => '2023-01-01T10:00:00Z',
            ],
            [
                'id' => 'model_id_2',
                'schema_version' => '1.1',
                'type_definitions' => [
                    ['type' => 'folder', 'relations' => ['writer' => ['this', 'user']]],
                ],
                'created_at' => '2023-01-02T11:00:00Z',
            ],
        ],
        'continuation_token' => 'test_token',
    ];
    $this->mockHttpResponse(200, $mockResponse);

    $response = $this->client->listAuthorizationModels($storeId);

    expect($response)->toBeInstanceOf(ListAuthorizationModelsResponse::class)
        ->and($response->authorizationModels)->toHaveCount(2)
        ->and($response->authorizationModels[0]->id)->toBe('model_id_1')
        ->and($response->authorizationModels[1]->id)->toBe('model_id_2')
        ->and($response->continuationToken)->toBe('test_token');

    $this->assertLastRequest('GET', '/stores/' . $storeId . '/authorization-models');
});

it('lists authorization models with options successfully', function () {
    $storeId = 'custom_store_id';
    $mockResponse = [
        'authorization_models' => [
            [
                'id' => 'model_id_3',
                'schema_version' => '1.1',
                'type_definitions' => [
                    ['type' => 'project', 'relations' => ['viewer' => ['this', 'user']]],
                ],
                'created_at' => '2023-01-03T12:00:00Z',
            ],
        ],
        'continuation_token' => '',
    ];
    $this->mockHttpResponse(200, $mockResponse);

    $options = new ListAuthorizationModelsOptions(
        pageSize: 5,
        continuationToken: 'previous_token'
    );
    $response = $this->client->listAuthorizationModels($storeId, $options);

    expect($response)->toBeInstanceOf(ListAuthorizationModelsResponse::class)
        ->and($response->authorizationModels)->toHaveCount(1)
        ->and($response->continuationToken)->toBeEmpty();

    $this->assertLastRequest('GET', '/stores/' . $storeId . '/authorization-models');
    $this->assertLastRequestQueryContains('page_size', '5');
    $this->assertLastRequestQueryContains('continuation_token', 'previous_token');
});

it('lists authorization models using default store ID', function () {
    $mockResponse = [
        'authorization_models' => [
            [
                'id' => 'default_model_id',
                'schema_version' => '1.1',
                'type_definitions' => [
                    ['type' => 'resource', 'relations' => ['accessor' => ['this', 'user']]],
                ],
                'created_at' => '2023-01-04T13:00:00Z',
            ],
        ],
        'continuation_token' => '',
    ];
    $this->mockHttpResponse(200, $mockResponse);

    $response = $this->client->listAuthorizationModels();

    expect($response)->toBeInstanceOf(ListAuthorizationModelsResponse::class)
        ->and($response->authorizationModels)->toHaveCount(1)
        ->and($response->authorizationModels[0]->id)->toBe('default_model_id');

    // Should use default store ID from configuration
    $this->assertLastRequest('GET', '/stores/test_store_id/authorization-models');
});

it('gets an authorization model successfully', function () {
    $storeId = 'test_store_id';
    $authModelId = 'specific_model_id';
    $mockResponse = [
        'authorization_model' => [
            'id' => $authModelId,
            'schema_version' => '1.1',
            'type_definitions' => [
                ['type' => 'document', 'relations' => [
                    'reader' => ['this', 'user'],
                    'writer' => ['this', 'user'],
                ]],
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
            'created_at' => '2023-01-05T14:00:00Z',
        ],
    ];
    $this->mockHttpResponse(200, $mockResponse);

    $response = $this->client->getAuthorizationModel($storeId, $authModelId);

    expect($response)->toBeInstanceOf(GetAuthorizationModelResponse::class)
        ->and($response->authorizationModel->id)->toBe($authModelId)
        ->and($response->authorizationModel->schemaVersion)->toBe('1.1')
        ->and($response->authorizationModel->typeDefinitions)->toBeInstanceOf(TypeDefinitions::class)
        ->and($response->authorizationModel->typeDefinitions->toArray()[0]['type'])->toBe('document');

    $this->assertLastRequest('GET', '/stores/' . $storeId . '/authorization-models/' . $authModelId);
});

it('gets an authorization model with options successfully', function () {
    $storeId = 'custom_store_id';
    $authModelId = 'custom_model_id';
    $mockResponse = [
        'authorization_model' => [
            'id' => $authModelId,
            'schema_version' => '1.1',
            'type_definitions' => [
                ['type' => 'custom_type', 'relations' => ['custom_relation' => ['this', 'user']]],
            ],
            'created_at' => '2023-01-06T15:00:00Z',
        ],
    ];
    $this->mockHttpResponse(200, $mockResponse);

    $options = new GetAuthorizationModelOptions();
    $response = $this->client->getAuthorizationModel($storeId, $authModelId, $options);

    expect($response)->toBeInstanceOf(GetAuthorizationModelResponse::class)
        ->and($response->authorizationModel->id)->toBe($authModelId);

    $this->assertLastRequest('GET', '/stores/' . $storeId . '/authorization-models/' . $authModelId);
});

it('gets authorization model using default store and authorization model IDs', function () {
    $mockResponse = [
        'authorization_model' => [
            'id' => 'test_auth_model_id',
            'schema_version' => '1.1',
            'type_definitions' => [
                ['type' => 'default_type', 'relations' => ['default_relation' => ['this', 'user']]],
            ],
            'created_at' => '2023-01-07T16:00:00Z',
        ],
    ];
    $this->mockHttpResponse(200, $mockResponse);

    $response = $this->client->getAuthorizationModel();

    expect($response)->toBeInstanceOf(GetAuthorizationModelResponse::class)
        ->and($response->authorizationModel->id)->toBe('test_auth_model_id');

    // Should use default IDs from configuration
    $this->assertLastRequest('GET', '/stores/test_store_id/authorization-models/test_auth_model_id');
});

it('creates an authorization model successfully', function () {
    $storeId = 'test_store_id';
    $typeDefinitions = new TypeDefinitions([
        [
            'type' => 'document',
            'relations' => [
                'reader' => ['this', 'user'],
                'writer' => ['this', 'user'],
            ],
        ]
    ]);
    $conditions = new Conditions([
        'condition_name' => [
            'name' => 'condition_name',
            'expression' => 'param1 == 1',
            'parameters' => [
                ['name' => 'param1', 'type_name' => 'int'],
            ],
        ],
    ]);
    $schemaVersion = '1.1';

    $mockResponse = [
        'authorization_model_id' => 'new_model_id',
    ];
    $this->mockHttpResponse(201, $mockResponse);

    $response = $this->client->createAuthorizationModel(
        $typeDefinitions,
        $conditions,
        $schemaVersion,
        $storeId
    );

    expect($response)->toBeInstanceOf(CreateAuthorizationModelResponse::class)
        ->and($response->authorizationModelId)->toBe('new_model_id');

    $this->assertLastRequest(
        'POST',
        '/stores/' . $storeId . '/authorization-models',
        [
            'type_definitions' => $typeDefinitions->toArray(),
            'conditions' => $conditions->toArray(),
            'schema_version' => $schemaVersion,
        ]
    );
});

it('creates an authorization model with options successfully', function () {
    $storeId = 'custom_store_id';
    $typeDefinitions = new TypeDefinitions([
        [
            'type' => 'custom_type',
            'relations' => [
                'custom_relation' => ['this', 'user'],
            ],
        ]
    ]);
    $conditions = new Conditions([]);
    $schemaVersion = '1.1';

    $mockResponse = [
        'authorization_model_id' => 'custom_new_model_id',
    ];
    $this->mockHttpResponse(201, $mockResponse);

    $options = new CreateAuthorizationModelOptions();
    $response = $this->client->createAuthorizationModel(
        $typeDefinitions,
        $conditions,
        $schemaVersion,
        $storeId,
        $options
    );

    expect($response)->toBeInstanceOf(CreateAuthorizationModelResponse::class)
        ->and($response->authorizationModelId)->toBe('custom_new_model_id');

    $this->assertLastRequest(
        'POST',
        '/stores/' . $storeId . '/authorization-models',
        [
            'type_definitions' => $typeDefinitions->toArray(),
            'conditions' => $conditions->toArray(),
            'schema_version' => $schemaVersion,
        ]
    );
});

it('creates an authorization model using default store ID', function () {
    $typeDefinitions = new TypeDefinitions([
        [
            'type' => 'default_type',
            'relations' => [
                'default_relation' => ['this', 'user'],
            ],
        ]
    ]);
    $conditions = new Conditions([]);
    $schemaVersion = '1.1';

    $mockResponse = [
        'authorization_model_id' => 'default_new_model_id',
    ];
    $this->mockHttpResponse(201, $mockResponse);

    $response = $this->client->createAuthorizationModel(
        $typeDefinitions,
        $conditions,
        $schemaVersion
    );

    expect($response)->toBeInstanceOf(CreateAuthorizationModelResponse::class)
        ->and($response->authorizationModelId)->toBe('default_new_model_id');

    $this->assertLastRequest(
        'POST',
        '/stores/test_store_id/authorization-models',
        [
            'type_definitions' => $typeDefinitions->toArray(),
            'conditions' => $conditions->toArray(),
            'schema_version' => $schemaVersion,
        ]
    );
});

it('throws API errors when listing authorization models', function () {
    $storeId = 'invalid_store';
    $mockResponse = ['code' => 'not_found', 'message' => 'Store not found'];
    $this->mockHttpResponse(404, $mockResponse);

    $this->client->listAuthorizationModels($storeId);
})->throws(ApiEndpointException::class);

it('throws API errors when getting an authorization model', function () {
    $storeId = 'invalid_store';
    $authModelId = 'invalid_model';
    $mockResponse = ['code' => 'not_found', 'message' => 'Authorization model not found'];
    $this->mockHttpResponse(404, $mockResponse);

    $this->client->getAuthorizationModel($storeId, $authModelId);
})->throws(ApiEndpointException::class);

it('throws API errors when creating an authorization model', function () {
    $storeId = 'invalid_store';
    $typeDefinitions = new TypeDefinitions([
        ['type' => 'document', 'relations' => ['reader' => ['this', 'user']]],
    ]);
    $conditions = new Conditions([]);
    $mockResponse = ['code' => 'invalid_request', 'message' => 'Invalid authorization model'];
    $this->mockHttpResponse(400, $mockResponse);

    $this->client->createAuthorizationModel($typeDefinitions, $conditions, '1.1', $storeId);
})->throws(ApiValidationException::class);
