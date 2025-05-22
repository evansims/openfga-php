<?php

namespace OpenFGATests\Unit\Models;

use OpenFGA\Models\TypeDefinition;
use OpenFGA\Models\Collections\TypeDefinitionRelations;
use OpenFGA\Models\Collections\TypeDefinitionRelationsInterface;
use OpenFGA\Models\Metadata;
use OpenFGA\Models\MetadataInterface;
use OpenFGA\Schema\SchemaInterface;
use JsonSerializable;
use ArrayIterator;
use Countable;
use IteratorAggregate;

// Dummy Interfaces & Classes for TypeDefinitionTest

if (!interface_exists(TypeDefinitionRelationsInterface::class)) {
    interface TypeDefinitionRelationsInterface extends JsonSerializable, Countable, IteratorAggregate {
        // Define methods based on actual interface for full compatibility if needed
    }
}

class DummyTypeDefinitionRelations implements TypeDefinitionRelationsInterface {
    private array $relations;

    public function __construct(array $relations = ['relation1' => []]) {
        $this->relations = $relations;
    }

    public function jsonSerialize(): array {
        return $this->relations;
    }

    public function count(): int {
        return count($this->relations);
    }

    public function getIterator(): ArrayIterator {
        return new ArrayIterator($this->relations);
    }
}

if (!interface_exists(MetadataInterface::class)) {
    interface MetadataInterface extends JsonSerializable {
        // Define methods based on actual interface
    }
}

class DummyMetadata implements MetadataInterface {
    private array $data;

    public function __construct(array $data = ['module' => 'test_module']) {
        $this->data = $data;
    }

    public function jsonSerialize(): array {
        return $this->data;
    }
}

describe('TypeDefinition', function () {
    describe('constructor', function () {
        it('constructs with only type', function () {
            $td = new TypeDefinition(type: 'user');
            expect($td->getType())->toBe('user')
                ->and($td->getRelations())->toBeNull()
                ->and($td->getMetadata())->toBeNull();
        });

        it('constructs with type and relations', function () {
            $relations = new DummyTypeDefinitionRelations(['viewer' => []]);
            $td = new TypeDefinition(type: 'document', relations: $relations);
            expect($td->getType())->toBe('document')
                ->and($td->getRelations())->toBe($relations)
                ->and($td->getMetadata())->toBeNull();
        });

        it('constructs with type and metadata', function () {
            $metadata = new DummyMetadata(['source' => 'definition_file']);
            $td = new TypeDefinition(type: 'folder', metadata: $metadata);
            expect($td->getType())->toBe('folder')
                ->and($td->getRelations())->toBeNull()
                ->and($td->getMetadata())->toBe($metadata);
        });

        it('constructs with all parameters', function () {
            $relations = new DummyTypeDefinitionRelations();
            $metadata = new DummyMetadata();
            $td = new TypeDefinition(type: 'organization', relations: $relations, metadata: $metadata);
            expect($td->getType())->toBe('organization')
                ->and($td->getRelations())->toBe($relations)
                ->and($td->getMetadata())->toBe($metadata);
        });
    });

    describe('getters', function () {
        $relations = new DummyTypeDefinitionRelations();
        $metadata = new DummyMetadata();
        $tdWithAll = new TypeDefinition('test_type', $relations, $metadata);
        $tdOnlyType = new TypeDefinition('only_type');

        it('getType returns the correct value', function () use ($tdWithAll, $tdOnlyType) {
            expect($tdWithAll->getType())->toBe('test_type')
                ->and($tdOnlyType->getType())->toBe('only_type');
        });

        it('getRelations returns the correct value or null', function () use ($tdWithAll, $relations, $tdOnlyType) {
            expect($tdWithAll->getRelations())->toBe($relations)
                ->and($tdOnlyType->getRelations())->toBeNull();
        });

        it('getMetadata returns the correct value or null', function () use ($tdWithAll, $metadata, $tdOnlyType) {
            expect($tdWithAll->getMetadata())->toBe($metadata)
                ->and($tdOnlyType->getMetadata())->toBeNull();
        });
    });

    describe('jsonSerialize', function () {
        it('serializes with only type set', function () {
            $td = new TypeDefinition(type: 'user');
            expect($td->jsonSerialize())->toBe(['type' => 'user']);
        });

        it('serializes with relations set', function () {
            $relationsData = ['editor' => ['this' => []]];
            $relations = new DummyTypeDefinitionRelations($relationsData);
            $td = new TypeDefinition(type: 'document', relations: $relations);
            expect($td->jsonSerialize())->toBe([
                'type' => 'document',
                'relations' => $relationsData,
            ]);
        });

        it('serializes with metadata set', function () {
            $metadataData = ['version' => '1.0'];
            $metadata = new DummyMetadata($metadataData);
            $td = new TypeDefinition(type: 'config', metadata: $metadata);
            expect($td->jsonSerialize())->toBe([
                'type' => 'config',
                'metadata' => $metadataData,
            ]);
        });

        it('serializes with all parameters set', function () {
            $relationsData = ['owner' => []];
            $metadataData = ['author' => 'tester'];
            $relations = new DummyTypeDefinitionRelations($relationsData);
            $metadata = new DummyMetadata($metadataData);
            $td = new TypeDefinition(type: 'project', relations: $relations, metadata: $metadata);
            expect($td->jsonSerialize())->toBe([
                'type' => 'project',
                'relations' => $relationsData,
                'metadata' => $metadataData,
            ]);
        });
    });

    describe('static schema()', function () {
        $schema = TypeDefinition::schema();

        it('returns a SchemaInterface instance', function () use ($schema) {
            expect($schema)->toBeInstanceOf(SchemaInterface::class);
        });

        it('has the correct className', function () use ($schema) {
            expect($schema->getClassName())->toBe(TypeDefinition::class);
        });

        it('has "type" property defined correctly', function () use ($schema) {
            $properties = $schema->getProperties();
            expect($properties)->toHaveKey('type');
            $prop = $properties['type'];
            expect($prop->getName())->toBe('type')
                ->and($prop->getTypes())->toBe(['string'])
                ->and($prop->isRequired())->toBeTrue();
        });

        it('has "relations" property defined correctly', function () use ($schema) {
            $properties = $schema->getProperties();
            expect($properties)->toHaveKey('relations');
            $prop = $properties['relations'];
            expect($prop->getName())->toBe('relations')
                ->and($prop->getTypes())->toBe([TypeDefinitionRelations::class])
                ->and($prop->isRequired())->toBeFalse();
        });

        it('has "metadata" property defined correctly', function () use ($schema) {
            $properties = $schema->getProperties();
            expect($properties)->toHaveKey('metadata');
            $prop = $properties['metadata'];
            expect($prop->getName())->toBe('metadata')
                ->and($prop->getTypes())->toBe([Metadata::class])
                ->and($prop->isRequired())->toBeFalse();
        });
    });
});

?>
