<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Models;

use OpenFGA\Models\{UserTypeFilter, UserTypeFilterInterface};
use OpenFGA\Schemas\SchemaInterface;

describe('UserTypeFilter Model', function (): void {
    test('implements UserTypeFilterInterface', function (): void {
        $filter = new UserTypeFilter(type: 'user');

        expect($filter)->toBeInstanceOf(UserTypeFilterInterface::class);
    });

    test('constructs with type', function (): void {
        $filter = new UserTypeFilter(type: 'user');

        expect($filter->getType())->toBe('user');
        expect($filter->getRelation())->toBeNull();
    });

    test('constructs with type and relation', function (): void {
        $filter = new UserTypeFilter(
            type: 'group',
            relation: 'member',
        );

        expect($filter->getType())->toBe('group');
        expect($filter->getRelation())->toBe('member');
    });

    test('serializes to JSON with only type', function (): void {
        $filter = new UserTypeFilter(type: 'user');

        expect($filter->jsonSerialize())->toBe([
            'type' => 'user',
        ]);
    });

    test('serializes to JSON with type and relation', function (): void {
        $filter = new UserTypeFilter(
            type: 'group',
            relation: 'member',
        );

        expect($filter->jsonSerialize())->toBe([
            'type' => 'group',
            'relation' => 'member',
        ]);
    });

    test('returns schema instance', function (): void {
        $schema = UserTypeFilter::schema();

        expect($schema)->toBeInstanceOf(SchemaInterface::class);
        expect($schema->getClassName())->toBe(UserTypeFilter::class);

        $properties = $schema->getProperties();
        expect($properties)->toHaveCount(2);

        $propertyNames = array_keys($properties);
        expect($propertyNames)->toBe(['type', 'relation']);
    });

    test('schema properties have correct configuration', function (): void {
        $schema = UserTypeFilter::schema();
        $properties = $schema->getProperties();

        // Type property
        $typeProp = $properties['type'];
        expect($typeProp->name)->toBe('type');
        expect($typeProp->type)->toBe('string');
        expect($typeProp->required)->toBe(true);

        // Relation property
        $relationProp = $properties['relation'];
        expect($relationProp->name)->toBe('relation');
        expect($relationProp->type)->toBe('string');
        expect($relationProp->required)->toBe(false);
    });

    test('schema is cached', function (): void {
        $schema1 = UserTypeFilter::schema();
        $schema2 = UserTypeFilter::schema();

        expect($schema1)->toBe($schema2);
    });

    test('handles typical filter patterns', function (): void {
        // Pattern 1: Simple user type filter
        $userFilter = new UserTypeFilter(type: 'user');
        expect($userFilter->jsonSerialize())->toBe(['type' => 'user']);

        // Pattern 2: Group member filter
        $groupMemberFilter = new UserTypeFilter(
            type: 'group',
            relation: 'member',
        );
        expect($groupMemberFilter->jsonSerialize())->toBe([
            'type' => 'group',
            'relation' => 'member',
        ]);

        // Pattern 3: Organization admin filter
        $orgAdminFilter = new UserTypeFilter(
            type: 'organization',
            relation: 'admin',
        );
        expect($orgAdminFilter->jsonSerialize())->toBe([
            'type' => 'organization',
            'relation' => 'admin',
        ]);

        // Pattern 4: Team lead filter
        $teamLeadFilter = new UserTypeFilter(
            type: 'team',
            relation: 'lead',
        );
        expect($teamLeadFilter->jsonSerialize())->toBe([
            'type' => 'team',
            'relation' => 'lead',
        ]);
    });

    test('handles various type formats', function (): void {
        // Standard type
        $standardFilter = new UserTypeFilter(type: 'document');
        expect($standardFilter->getType())->toBe('document');

        // Type with namespace
        $namespacedFilter = new UserTypeFilter(type: 'company:department');
        expect($namespacedFilter->getType())->toBe('company:department');

        // Type with special characters
        $specialFilter = new UserTypeFilter(type: 'user_group');
        expect($specialFilter->getType())->toBe('user_group');
    });

    test('differentiates between no relation and empty relation', function (): void {
        // No relation provided
        $noRelationFilter = new UserTypeFilter(type: 'user');
        expect($noRelationFilter->getRelation())->toBeNull();
        expect($noRelationFilter->jsonSerialize())->not->toHaveKey('relation');

        // Empty relation is still included
        $emptyRelationFilter = new UserTypeFilter(
            type: 'user',
            relation: '',
        );
        expect($emptyRelationFilter->getRelation())->toBe('');
        expect($emptyRelationFilter->jsonSerialize())->toHaveKey('relation');
    });
});
