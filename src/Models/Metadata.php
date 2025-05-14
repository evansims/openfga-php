<?php

declare(strict_types=1);

namespace OpenFGA\Models;

final class Metadata implements MetadataInterface
{
    public function __construct(
        private ?string $module = null,
        private ?RelationMetadataInterface $relations = null,
        private ?SourceInfoInterface $sourceInfo = null,
    ) {
    }

    public function getModule(): ?string
    {
        return $this->module;
    }

    public function getRelations(): ?RelationMetadataInterface
    {
        return $this->relations;
    }

    public function getSourceInfo(): ?SourceInfoInterface
    {
        return $this->sourceInfo;
    }

    public function jsonSerialize(): array
    {
        $response = [];

        if (null !== $this->getModule()) {
            $response['module'] = $this->getModule();
        }

        if (null !== $this->getRelations()) {
            $response['relations'] = $this->getRelations()->jsonSerialize();
        }

        if (null !== $this->getSourceInfo()) {
            $response['source_info'] = $this->getSourceInfo()->jsonSerialize();
        }

        return $response;
    }

    public static function fromArray(array $data): self
    {
        $data = self::validatedMetadataShape($data);

        return new self(
            module: $data['module'],
            relations: isset($data['relations']) ? RelationMetadata::fromArray($data['relations']) : null,
            sourceInfo: isset($data['source_info']) ? SourceInfo::fromArray($data['source_info']) : null,
        );
    }

    /**
     * Validates the shape of the metadata data.
     *
     * @param array{module?: string, relations?: RelationMetadataShape, source_info?: SourceInfoShape} $data
     *
     * @throws InvalidArgumentException
     *
     * @return MetadataShape
     */
    public static function validatedMetadataShape(array $data): array
    {
        return $data;
    }
}
