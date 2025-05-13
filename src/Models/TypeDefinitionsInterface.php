<?php

declare(strict_types=1);

namespace OpenFGA\Models;

interface TypeDefinitionsInterface extends ModelCollectionInterface
{
    /**
     * Add a type definition to the collection.
     *
     * @param TypeDefinitionInterface $typeDefinition
     */
    public function add(TypeDefinitionInterface $typeDefinition): void;

    /**
     * Get the current type definition in the collection.
     *
     * @return TypeDefinitionInterface
     */
    public function current(): TypeDefinitionInterface;

    /**
     * Get a type definition by offset.
     *
     * @param mixed $offset
     *
     * @return null|TypeDefinitionInterface
     */
    public function offsetGet(mixed $offset): ?TypeDefinitionInterface;

    /**
     * @return array<int, array{
     *     type: string,
     *     relations?: array<string, array{name: string, expression: string, parameters?: array{module: string, source_info: array{file: string}}, metadata?: array{module: string, source_info: array{file: string}}}>},
     *     metadata?: array{module: string, source_info: array{file: string}}}
     * }>
     */
    public function jsonSerialize(): array;

    /**
     * @param array<int, array{
     *     type: string,
     *     relations?: array<string, array{name: string, expression: string, parameters?: array{module: string, source_info: array{file: string}}, metadata?: array{module: string, source_info: array{file: string}}}>},
     *     metadata?: array{module: string, source_info: array{file: string}}}
     * }> $data
     */
    public static function fromArray(array $data): static;
}
