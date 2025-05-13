<?php

declare(strict_types=1);

namespace OpenFGA\Models;

interface TypeDefinitionInterface extends ModelInterface
{
    public function getMetadata(): ?MetadataInterface;

    public function getRelations(): ?array;

    public function getType(): string;

    /**
     * @return array{
     *     type: string,
     *     relations?: array<string, array{name: string, expression: string, parameters?: array{module: string, source_info: array{file: string}}, metadata?: array{module: string, source_info: array{file: string}}}>},
     *     metadata?: array{module: string, source_info: array{file: string}}}
     */
    public function jsonSerialize(): array;

    /**
     * @param array{
     *     type: string,
     *     relations?: array<string, array{name: string, expression: string, parameters?: array{module: string, source_info: array{file: string}}, metadata?: array{module: string, source_info: array{file: string}}}>},
     *     metadata?: array{module: string, source_info: array{file: string}}}
     * } $data
     */
    public static function fromArray(array $data): static;
}
