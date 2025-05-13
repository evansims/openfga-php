<?php

declare(strict_types=1);

namespace OpenFGA\Models;

interface AuthorizationModelInterface extends ModelInterface
{
    public function getConditions(): ?ConditionsInterface;

    public function getId(): string;

    public function getSchemaVersion(): string;

    public function getTypeDefinitions(): TypeDefinitionsInterface;

    /**
     * @return array{
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
     * @param array{
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
