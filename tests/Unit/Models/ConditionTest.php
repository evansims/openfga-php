<?php

declare(strict_types=1);

use OpenFGA\Models\Collections\ConditionParameters;
use OpenFGA\Models\{Condition, ConditionInterface, ConditionMetadata, ConditionParameter, SourceInfo};
use OpenFGA\Models\Enums\TypeName;
use OpenFGA\Schema\SchemaInterface;

describe('Condition Model', function (): void {
    test('implements ConditionInterface', function (): void {
        $condition = new Condition(
            name: 'inRegion',
            expression: 'params.region == "us-east"',
        );

        expect($condition)->toBeInstanceOf(ConditionInterface::class);
    });

    test('constructs with required parameters only', function (): void {
        $condition = new Condition(
            name: 'inRegion',
            expression: 'params.region == "us-east"',
        );

        expect($condition->getName())->toBe('inRegion');
        expect($condition->getExpression())->toBe('params.region == "us-east"');
        expect($condition->getParameters())->toBeNull();
        expect($condition->getMetadata())->toBeNull();
    });

    test('constructs with parameters', function (): void {
        $param1 = new ConditionParameter(typeName: TypeName::STRING);
        $param2 = new ConditionParameter(typeName: TypeName::STRING);
        $parameters = new ConditionParameters([$param1, $param2]);

        $condition = new Condition(
            name: 'inRegion',
            expression: 'params.region == "us-east" && params.userId != ""',
            parameters: $parameters,
        );

        expect($condition->getParameters())->toBe($parameters);
        expect($condition->getParameters()->count())->toBe(2);
    });

    test('constructs with metadata', function (): void {
        $sourceInfo = new SourceInfo(file: 'conditions.fga');
        $metadata = new ConditionMetadata(
            module: 'auth',
            sourceInfo: $sourceInfo,
        );

        $condition = new Condition(
            name: 'inRegion',
            expression: 'params.region == "us-east"',
            metadata: $metadata,
        );

        expect($condition->getMetadata())->toBe($metadata);
    });

    test('handles complex CEL expressions', function (): void {
        $expressions = [
            'params.age >= 18',
            'params.region in ["us-east", "us-west", "eu-central"]',
            'params.startTime < now() && params.endTime > now()',
            'has(params.customField) && params.customField != ""',
            'params.score > 0.75 || params.isAdmin == true',
            'size(params.tags) > 0 && "premium" in params.tags',
        ];

        foreach ($expressions as $expr) {
            $condition = new Condition(
                name: 'testCondition',
                expression: $expr,
            );

            expect($condition->getExpression())->toBe($expr);
        }
    });

    test('serializes to JSON without optional fields', function (): void {
        $condition = new Condition(
            name: 'inRegion',
            expression: 'params.region == "us-east"',
        );

        $json = $condition->jsonSerialize();

        expect($json)->toBe([
            'name' => 'inRegion',
            'expression' => 'params.region == "us-east"',
        ]);
        expect($json)->not->toHaveKeys(['parameters', 'metadata']);
    });

    test('serializes to JSON with all fields', function (): void {
        $param = new ConditionParameter(typeName: TypeName::STRING);
        $parameters = new ConditionParameters([$param]);

        $sourceInfo = new SourceInfo(file: 'conditions.fga');
        $metadata = new ConditionMetadata(module: 'default', sourceInfo: $sourceInfo);

        $condition = new Condition(
            name: 'inRegion',
            expression: 'params.region == "us-east"',
            parameters: $parameters,
            metadata: $metadata,
        );

        $json = $condition->jsonSerialize();

        expect($json)->toHaveKeys(['name', 'expression', 'parameters', 'metadata']);
        expect($json['parameters'])->toBe($parameters->jsonSerialize());
        expect($json['metadata'])->toBe($metadata->jsonSerialize());
    });

    test('handles empty name and expression', function (): void {
        $condition = new Condition(
            name: '',
            expression: '',
        );

        expect($condition->getName())->toBe('');
        expect($condition->getExpression())->toBe('');
    });

    test('preserves whitespace in expression', function (): void {
        $condition = new Condition(
            name: 'complexCondition',
            expression: '  params.value > 0  &&  params.enabled == true  ',
        );

        expect($condition->getExpression())->toBe('  params.value > 0  &&  params.enabled == true  ');
    });

    test('handles condition names with various formats', function (): void {
        $names = [
            'simple',
            'with_underscore',
            'with-dash',
            'camelCase',
            'PascalCase',
            'with.dot',
            'with123numbers',
        ];

        foreach ($names as $name) {
            $condition = new Condition(
                name: $name,
                expression: 'true',
            );

            expect($condition->getName())->toBe($name);
        }
    });

    test('returns schema instance', function (): void {
        $schema = Condition::schema();

        expect($schema)->toBeInstanceOf(SchemaInterface::class);
        expect($schema->getClassName())->toBe(Condition::class);

        $properties = $schema->getProperties();
        expect($properties)->toHaveCount(4);

        $propertyNames = array_keys($properties);
        expect($propertyNames)->toBe(['name', 'expression', 'parameters', 'metadata']);
    });

    test('schema properties have correct configuration', function (): void {
        $schema = Condition::schema();
        $properties = $schema->getProperties();

        // Name property
        $nameProp = $properties[array_keys($properties)[0]];
        expect($nameProp->name)->toBe('name');
        expect($nameProp->type)->toBe('string');
        expect($nameProp->required)->toBe(true);

        // Expression property
        $exprProp = $properties[array_keys($properties)[1]];
        expect($exprProp->name)->toBe('expression');
        expect($exprProp->type)->toBe('string');
        expect($exprProp->required)->toBe(true);

        // Parameters property
        $paramsProp = $properties[array_keys($properties)[2]];
        expect($paramsProp->name)->toBe('parameters');
        expect($paramsProp->type)->toBe('object');
        expect($paramsProp->required)->toBe(false);

        // Metadata property
        $metadataProp = $properties[array_keys($properties)[3]];
        expect($metadataProp->name)->toBe('metadata');
        expect($metadataProp->type)->toBe('object');
        expect($metadataProp->required)->toBe(false);
    });

    test('schema is cached', function (): void {
        $schema1 = Condition::schema();
        $schema2 = Condition::schema();

        expect($schema1)->toBe($schema2);
    });

    test('handles multiline expressions', function (): void {
        $expression = 'params.age >= 18 &&
params.country == "US" &&
params.verified == true';

        $condition = new Condition(
            name: 'eligibility',
            expression: $expression,
        );

        expect($condition->getExpression())->toBe($expression);
    });

    test('with empty parameters collection', function (): void {
        $parameters = new ConditionParameters([]);

        $condition = new Condition(
            name: 'alwaysTrue',
            expression: 'true',
            parameters: $parameters,
        );

        expect($condition->getParameters())->toBe($parameters);
        expect($condition->getParameters()->isEmpty())->toBe(true);
    });
});
