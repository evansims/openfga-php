<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Models;

use OpenFGA\Models\{Condition, Conditions, ConditionMetadata, ConditionParameter, ConditionParameters, SourceInfo, TypeName};

it('can create a Condition instance with constructor', function () {
    $condition = new Condition(
        name: 'test_condition',
        expression: 'subject.age >= 18',
    );

    expect($condition->name)->toBe('test_condition')
        ->and($condition->expression)->toBe('subject.age >= 18')
        ->and($condition->parameters)->toBeNull()
        ->and($condition->metadata)->toBeNull();
});

it('can create a Condition instance with parameters', function () {
    $parameters = new ConditionParameters();
    $parameters->add(new ConditionParameter(
        typeName: TypeName::INT,
        genericTypes: null
    ));

    $condition = new Condition(
        name: 'test_condition',
        expression: 'subject.age >= required_age',
        parameters: $parameters
    );

    expect($condition->name)->toBe('test_condition')
        ->and($condition->expression)->toBe('subject.age >= required_age')
        ->and($condition->parameters)->toBe($parameters)
        ->and($condition->metadata)->toBeNull();
});

it('can create a Condition instance with metadata', function () {
    $sourceInfo = new SourceInfo(
        file: 'test_file.cel'
    );

    $metadata = new ConditionMetadata(
        module: 'test_module',
        sourceInfo: $sourceInfo
    );

    $condition = new Condition(
        name: 'test_condition',
        expression: 'subject.age >= 18',
        parameters: null,
        metadata: $metadata
    );

    expect($condition->name)->toBe('test_condition')
        ->and($condition->expression)->toBe('subject.age >= 18')
        ->and($condition->parameters)->toBeNull()
        ->and($condition->metadata)->toBe($metadata);
});

it('can convert Condition to array', function () {
    $sourceInfo = new SourceInfo(
        file: 'test_file.cel'
    );

    $metadata = new ConditionMetadata(
        module: 'test_module',
        sourceInfo: $sourceInfo
    );

    $parameters = new ConditionParameters();
    $parameters->add(new ConditionParameter(
        typeName: TypeName::INT,
        genericTypes: null
    ));

    $condition = new Condition(
        name: 'test_condition',
        expression: 'subject.age >= required_age',
        parameters: $parameters,
        metadata: $metadata
    );

    $expectedArray = [
        'name' => 'test_condition',
        'expression' => 'subject.age >= required_age',
        'parameters' => [
            [
                'type_name' => TypeName::INT->value,
                'generic_types' => null,
            ]
        ],
        'metadata' => [
            'module' => 'test_module',
            'source_info' => [
                'file' => 'test_file.cel',
            ],
        ],
    ];

    expect($condition->toArray())->toBe($expectedArray);
});

it('can create Condition from array', function () {
    $conditionData = [
        'name' => 'test_condition',
        'expression' => 'subject.age >= required_age',
        'parameters' => [
            [
                'type_name' => TypeName::INT->value,
                'generic_types' => null,
            ]
        ],
        'metadata' => [
            'module' => 'test_module',
            'source_info' => [
                'file' => 'test_file.cel',
            ],
        ],
    ];

    $condition = Condition::fromArray($conditionData);

    expect($condition->name)->toBe('test_condition')
        ->and($condition->expression)->toBe('subject.age >= required_age')
        ->and($condition->parameters)->toBeInstanceOf(ConditionParameters::class)
        ->and($condition->parameters)->toHaveCount(1)
        ->and($condition->parameters[0]->typeName)->toBe(TypeName::INT)
        ->and($condition->metadata)->toBeInstanceOf(ConditionMetadata::class)
        ->and($condition->metadata->module)->toBe('test_module')
        ->and($condition->metadata->sourceInfo)->toBeInstanceOf(SourceInfo::class)
        ->and($condition->metadata->sourceInfo->file)->toBe('test_file.cel');
});

it('can create and use a Conditions collection', function () {
    $conditions = new Conditions();

    $condition1 = new Condition(
        name: 'test_condition1',
        expression: 'subject.age >= 18',
    );

    $condition2 = new Condition(
        name: 'test_condition2',
        expression: 'subject.age < 18',
    );

    $conditions->add($condition1);
    $conditions->add($condition2);

    expect($conditions)->toHaveCount(2)
        ->and($conditions[0])->toBe($condition1)
        ->and($conditions[1])->toBe($condition2);
});

it('can get current Condition from Conditions collection', function () {
    $conditions = new Conditions();
    
    $condition = new Condition(
        name: 'test_condition',
        expression: 'subject.age >= 18',
    );
    
    $conditions->add($condition);
    
    expect($conditions->current())->toBe($condition);
});

it('can create Conditions collection from array', function () {
    $conditionsData = [
        [
            'name' => 'test_condition1',
            'expression' => 'subject.age >= 18',
        ],
        [
            'name' => 'test_condition2',
            'expression' => 'subject.age < 18',
        ],
    ];

    $conditions = Conditions::fromArray($conditionsData);

    expect($conditions)->toHaveCount(2)
        ->and($conditions[0]->name)->toBe('test_condition1')
        ->and($conditions[0]->expression)->toBe('subject.age >= 18')
        ->and($conditions[1]->name)->toBe('test_condition2')
        ->and($conditions[1]->expression)->toBe('subject.age < 18');
});

it('can create a ConditionMetadata instance with constructor', function () {
    $sourceInfo = new SourceInfo(
        file: 'test_file.cel'
    );

    $metadata = new ConditionMetadata(
        module: 'test_module',
        sourceInfo: $sourceInfo
    );

    expect($metadata->module)->toBe('test_module')
        ->and($metadata->sourceInfo)->toBe($sourceInfo);
});

it('can convert ConditionMetadata to array', function () {
    $sourceInfo = new SourceInfo(
        file: 'test_file.cel'
    );

    $metadata = new ConditionMetadata(
        module: 'test_module',
        sourceInfo: $sourceInfo
    );

    $expectedArray = [
        'module' => 'test_module',
        'source_info' => [
            'file' => 'test_file.cel',
        ],
    ];

    expect($metadata->toArray())->toBe($expectedArray);
});

it('can create ConditionMetadata from array', function () {
    $metadataData = [
        'module' => 'test_module',
        'source_info' => [
            'file' => 'test_file.cel',
        ],
    ];

    $metadata = ConditionMetadata::fromArray($metadataData);

    expect($metadata->module)->toBe('test_module')
        ->and($metadata->sourceInfo->file)->toBe('test_file.cel');
});

it('can create a ConditionParameter instance with constructor', function () {
    $parameter = new ConditionParameter(
        typeName: TypeName::INT,
        genericTypes: null
    );

    expect($parameter->typeName)->toBe(TypeName::INT)
        ->and($parameter->genericTypes)->toBeNull();
});

it('can create a ConditionParameter instance with generic types', function () {
    $genericTypes = new ConditionParameters();
    $genericTypes->add(new ConditionParameter(
        typeName: TypeName::STRING,
        genericTypes: null
    ));

    $parameter = new ConditionParameter(
        typeName: TypeName::LIST,
        genericTypes: $genericTypes
    );

    expect($parameter->typeName)->toBe(TypeName::LIST)
        ->and($parameter->genericTypes)->toBe($genericTypes);
});

it('can convert ConditionParameter to array', function () {
    $genericTypes = new ConditionParameters();
    $genericTypes->add(new ConditionParameter(
        typeName: TypeName::STRING,
        genericTypes: null
    ));

    $parameter = new ConditionParameter(
        typeName: TypeName::LIST,
        genericTypes: $genericTypes
    );

    $expectedArray = [
        'type_name' => TypeName::LIST->value,
        'generic_types' => [
            [
                'type_name' => TypeName::STRING->value,
                'generic_types' => null,
            ]
        ],
    ];

    expect($parameter->toArray())->toBe($expectedArray);
});

it('can create ConditionParameter from array', function () {
    $parameterData = [
        'type_name' => TypeName::LIST->value,
        'generic_types' => [
            [
                'type_name' => TypeName::STRING->value,
                'generic_types' => null,
            ]
        ],
    ];

    $parameter = ConditionParameter::fromArray($parameterData);

    expect($parameter->typeName)->toBe(TypeName::LIST)
        ->and($parameter->genericTypes)->toBeInstanceOf(ConditionParameters::class)
        ->and($parameter->genericTypes)->toHaveCount(1)
        ->and($parameter->genericTypes[0]->typeName)->toBe(TypeName::STRING);
});

it('can create and use a ConditionParameters collection', function () {
    $parameters = new ConditionParameters();

    $parameter1 = new ConditionParameter(
        typeName: TypeName::INT,
        genericTypes: null
    );

    $parameter2 = new ConditionParameter(
        typeName: TypeName::STRING,
        genericTypes: null
    );

    $parameters->add($parameter1);
    $parameters->add($parameter2);

    expect($parameters)->toHaveCount(2)
        ->and($parameters[0])->toBe($parameter1)
        ->and($parameters[1])->toBe($parameter2);
});

it('can get current ConditionParameter from ConditionParameters collection', function () {
    $parameters = new ConditionParameters();
    
    $parameter = new ConditionParameter(
        typeName: TypeName::INT,
        genericTypes: null
    );
    
    $parameters->add($parameter);
    
    expect($parameters->current())->toBe($parameter);
});

it('can create ConditionParameters collection from array', function () {
    $parametersData = [
        [
            'type_name' => TypeName::INT->value,
            'generic_types' => null,
        ],
        [
            'type_name' => TypeName::STRING->value,
            'generic_types' => null,
        ],
    ];

    $parameters = ConditionParameters::fromArray($parametersData);

    expect($parameters)->toHaveCount(2)
        ->and($parameters[0]->typeName)->toBe(TypeName::INT)
        ->and($parameters[1]->typeName)->toBe(TypeName::STRING);
});
