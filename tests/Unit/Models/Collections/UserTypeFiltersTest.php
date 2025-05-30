<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Models\Collections;

use OpenFGA\Models\Collections\{UserTypeFilters, UserTypeFiltersInterface};
use OpenFGA\Models\UserTypeFilter;
use OpenFGA\Schema\{CollectionSchemaInterface, SchemaInterface};

describe('UserTypeFilters Collection', function (): void {
    test('implements interface', function (): void {
        $collection = new UserTypeFilters([]);

        expect($collection)->toBeInstanceOf(UserTypeFiltersInterface::class);
    });

    test('creates empty', function (): void {
        $collection = new UserTypeFilters([]);

        expect($collection->count())->toBe(0);
        expect($collection->isEmpty())->toBeTrue();
    });

    test('creates with array of user type filters', function (): void {
        $filter1 = new UserTypeFilter(
            type: 'user',
        );

        $filter2 = new UserTypeFilter(
            type: 'group',
            relation: 'member',
        );

        $filter3 = new UserTypeFilter(
            type: 'organization',
            relation: 'admin',
        );

        $collection = new UserTypeFilters([$filter1, $filter2, $filter3]);

        expect($collection->count())->toBe(3);
        expect($collection->isEmpty())->toBeFalse();
    });

    test('adds user type filters', function (): void {
        $collection = new UserTypeFilters([]);

        $filter = new UserTypeFilter(
            type: 'service_account',
        );

        $collection->add($filter);

        expect($collection->count())->toBe(1);
        expect($collection->get(0))->toBe($filter);
    });

    test('gets filters by index', function (): void {
        $filter1 = new UserTypeFilter(type: 'user');
        $filter2 = new UserTypeFilter(type: 'group', relation: 'member');

        $collection = new UserTypeFilters([$filter1, $filter2]);

        expect($collection->get(0))->toBe($filter1);
        expect($collection->get(1))->toBe($filter2);
        expect($collection->get(2))->toBeNull();
    });

    test('checks if filter exists', function (): void {
        $filter = new UserTypeFilter(type: 'user');

        $collection = new UserTypeFilters([$filter]);

        expect(isset($collection[0]))->toBeTrue();
        expect(isset($collection[1]))->toBeFalse();
    });

    test('iterates over filters', function (): void {
        $filter1 = new UserTypeFilter(type: 'user');
        $filter2 = new UserTypeFilter(type: 'group', relation: 'member');
        $filter3 = new UserTypeFilter(type: 'team', relation: 'owner');

        $collection = new UserTypeFilters([$filter1, $filter2, $filter3]);

        $types = [];
        foreach ($collection as $filter) {
            $types[] = $filter->getType();
        }

        expect($types)->toBe(['user', 'group', 'team']);
    });

    test('toArray', function (): void {
        $filter1 = new UserTypeFilter(type: 'user');
        $filter2 = new UserTypeFilter(type: 'group', relation: 'member');

        $collection = new UserTypeFilters([$filter1, $filter2]);
        $array = $collection->toArray();

        expect($array)->toBeArray();
        expect($array)->toHaveCount(2);
        expect($array[0])->toBe($filter1);
        expect($array[1])->toBe($filter2);
    });

    test('jsonSerialize', function (): void {
        $collection = new UserTypeFilters([
            new UserTypeFilter(type: 'user'),
            new UserTypeFilter(type: 'group', relation: 'member'),
            new UserTypeFilter(type: 'organization', relation: 'admin'),
        ]);

        $json = $collection->jsonSerialize();

        expect($json)->toBeArray();
        expect($json)->toHaveCount(3);

        expect($json[0])->toBe([
            'type' => 'user',
        ]);

        expect($json[1])->toBe([
            'type' => 'group',
            'relation' => 'member',
        ]);

        expect($json[2])->toBe([
            'type' => 'organization',
            'relation' => 'admin',
        ]);
    });

    test('filters by type', function (): void {
        $collection = new UserTypeFilters([
            new UserTypeFilter(type: 'user'),
            new UserTypeFilter(type: 'group', relation: 'member'),
            new UserTypeFilter(type: 'user'),
            new UserTypeFilter(type: 'organization', relation: 'owner'),
            new UserTypeFilter(type: 'group', relation: 'admin'),
        ]);

        // Filter only 'group' types
        $groupFilters = [];
        foreach ($collection as $filter) {
            if ('group' === $filter->getType()) {
                $groupFilters[] = $filter->getRelation();
            }
        }

        expect($groupFilters)->toBe(['member', 'admin']);
    });

    test('handles filters with and without relations', function (): void {
        $collection = new UserTypeFilters([
            new UserTypeFilter(type: 'user'),
            new UserTypeFilter(type: 'group', relation: 'member'),
            new UserTypeFilter(type: 'service_account'),
            new UserTypeFilter(type: 'team', relation: 'owner'),
        ]);

        $withRelation = 0;
        $withoutRelation = 0;

        foreach ($collection as $filter) {
            if (null !== $filter->getRelation()) {
                ++$withRelation;
            } else {
                ++$withoutRelation;
            }
        }

        expect($withRelation)->toBe(2);
        expect($withoutRelation)->toBe(2);
    });

    test('schema', function (): void {
        $schema = UserTypeFilters::schema();

        expect($schema)->toBeInstanceOf(SchemaInterface::class);
        expect($schema)->toBeInstanceOf(CollectionSchemaInterface::class);
        expect($schema->getClassName())->toBe(UserTypeFilters::class);
    });

    test('schema is cached', function (): void {
        $schema1 = UserTypeFilters::schema();
        $schema2 = UserTypeFilters::schema();

        expect($schema1)->toBe($schema2);
        expect($schema1)->toBeInstanceOf(CollectionSchemaInterface::class);
        expect($schema2)->toBeInstanceOf(CollectionSchemaInterface::class);
    });

    test('handles empty collection edge cases', function (): void {
        $collection = new UserTypeFilters([]);

        expect($collection->isEmpty())->toBeTrue();
        expect($collection->toArray())->toBe([]);
        expect($collection->jsonSerialize())->toBe([]);

        // Test iteration on empty collection
        $count = 0;
        foreach ($collection as $_) {
            ++$count;
        }
        expect($count)->toBe(0);

        // Test get on empty collection
        expect($collection->get(0))->toBeNull();
    });

    test('represents typical user type configurations', function (): void {
        // Simulate a typical permission model with various user types
        $collection = new UserTypeFilters([
            // Direct user access
            new UserTypeFilter(type: 'user'),

            // Group membership access
            new UserTypeFilter(type: 'group', relation: 'member'),

            // Organization admin access
            new UserTypeFilter(type: 'organization', relation: 'admin'),

            // Team owner access
            new UserTypeFilter(type: 'team', relation: 'owner'),

            // Service account access (no relation needed)
            new UserTypeFilter(type: 'service_account'),
        ]);

        expect($collection->count())->toBe(5);

        // Verify the configuration
        $config = [];
        foreach ($collection as $filter) {
            $config[] = $filter->getType() . ($filter->getRelation() ? '#' . $filter->getRelation() : '');
        }

        expect($config)->toBe([
            'user',
            'group#member',
            'organization#admin',
            'team#owner',
            'service_account',
        ]);
    });
});
