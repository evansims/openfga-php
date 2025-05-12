<?php

declare(strict_types=1);

namespace OpenFGA\Models;

final class Metadata extends Model implements MetadataInterface
{
    public function __construct(
        private ?RelationReferencesInterface $relations = null,
        private ?string $module = null,
        private ?SourceInfoInterface $sourceInfo = null,
    ) {
    }

    public function getModule(): ?string
    {
        return $this->module;
    }

    public function getRelations(): ?RelationReferencesInterface
    {
        return $this->relations;
    }

    public function getSourceInfo(): ?SourceInfoInterface
    {
        return $this->sourceInfo;
    }

    public function toArray(): array
    {
        return [
            'relations' => $this->relations?->toArray(),
            'module' => $this->module,
            'source_info' => $this->sourceInfo?->toArray(),
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            relations: isset($data['relations']) ? RelationReferences::fromArray($data['relations']) : null,
            module: $data['module'],
            sourceInfo: isset($data['source_info']) ? SourceInfo::fromArray($data['source_info']) : null,
        );
    }
}
