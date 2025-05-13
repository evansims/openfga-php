<?php

declare(strict_types=1);

namespace OpenFGA\Models;

interface AuthorizationModelsInterface extends ModelCollectionInterface
{
    /**
     * Add an authorization model to the collection.
     *
     * @param AuthorizationModelInterface $authorizationModel
     */
    public function add(AuthorizationModelInterface $authorizationModel): void;

    /**
     * Get the current authorization model in the collection.
     *
     * @return AuthorizationModelInterface
     */
    public function current(): AuthorizationModelInterface;

    /**
     * Get an authorization model by offset.
     *
     * @param mixed $offset
     *
     * @return null|AuthorizationModelInterface
     */
    public function offsetGet(mixed $offset): ?AuthorizationModelInterface;

    /**
     * @return array<int, array{
     *     id: string,
     *     schema_version: string,
     *     type_definitions: array<int, array{
     *         type: string,
     *         relations?: array<string, array{name: string, expression: string, parameters?: array{module: string, source_info: array{file: string}}, metadata?: array{module: string, source_info: array{file: string}}}>},
     *         metadata?: array{module: string, source_info: array{file: string}}}
     *     >},
     *     conditions?: array<int, array{name: string, expression: string, parameters?: array{module: string, source_info: array{file: string}}, metadata?: array{module: string, source_info: array{file: string}}}>}
     * }
     */
    public function jsonSerialize(): array;

    /**
     * @param array<int, array{
     *     id: string,
     *     schema_version: string,
     *     type_definitions: array<int, array{
     *         type: string,
     *         relations?: array<string, array{name: string, expression: string, parameters?: array{module: string, source_info: array{file: string}}, metadata?: array{module: string, source_info: array{file: string}}}>},
     *         metadata?: array{module: string, source_info: array{file: string}}}
     *     >},
     *     conditions?: array<int, array{name: string, expression: string, parameters?: array{module: string, source_info: array{file: string}}, metadata?: array{module: string, source_info: array{file: string}}}>}
     * } $data
     */
    public static function fromArray(array $data): static;
}
