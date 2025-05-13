<?php

declare(strict_types=1);

namespace OpenFGA\Models;

interface AssertionsInterface extends ModelCollectionInterface
{
    /**
     * Add an assertion to the collection.
     *
     * @param AssertionInterface $assertion
     */
    public function add(AssertionInterface $assertion): void;

    /**
     * Get the current assertion in the collection.
     *
     * @return AssertionInterface
     */
    public function current(): AssertionInterface;

    /**
     * Get an assertion by offset.
     *
     * @param mixed $offset
     *
     * @return null|AssertionInterface
     */
    public function offsetGet(mixed $offset): ?AssertionInterface;

    /**
     * @return array<int, array{tuple_key: array{user: string, relation: string, object: string, condition?: array{name: string, expression: string, parameters?: array{module: string, source_info: array{file: string}}, metadata?: array{module: string, source_info: array{file: string}}}}, expectation: bool, contextual_tuples?: array<int, array{user: string, relation: string, object: string, condition?: array{name: string, expression: string, parameters?: array{module: string, source_info: array{file: string}}, metadata?: array{module: string, source_info: array{file: string}}}}, context?: array}>
     */
    public function jsonSerialize(): array;

    /**
     * @param array<int, array{tuple_key: array{user: string, relation: string, object: string, condition?: array{name: string, expression: string, parameters?: array{module: string, source_info: array{file: string}}, metadata?: array{module: string, source_info: array{file: string}}}}, expectation: bool, contextual_tuples?: array<int, array{user: string, relation: string, object: string, condition?: array{name: string, expression: string, parameters?: array{module: string, source_info: array{file: string}}, metadata?: array{module: string, source_info: array{file: string}}}}, context?: array}> $data
     */
    public static function fromArray(array $data): static;
}
