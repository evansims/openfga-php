<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use InvalidArgumentException;

final class RelationMetadata implements RelationMetadataInterface
{
    /**
     * @param null|string                      $module
     * @param null|RelationReferencesInterface $directlyRelatedUserTypes
     * @param null|SourceInfoInterface         $sourceInfo
     */
    public function __construct(
        private ?string $module = null,
        private ?RelationReferencesInterface $directlyRelatedUserTypes = null,
        private ?SourceInfoInterface $sourceInfo = null,
    ) {
    }

    public function getDirectlyRelatedUserTypes(): ?RelationReferencesInterface
    {
        return $this->directlyRelatedUserTypes;
    }

    public function getModule(): ?string
    {
        return $this->module;
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

        if (null !== $this->getDirectlyRelatedUserTypes()) {
            $response['directly_related_user_types'] = $this->getDirectlyRelatedUserTypes()->jsonSerialize();
        }

        if (null !== $this->getSourceInfo()) {
            $response['source_info'] = $this->getSourceInfo()->jsonSerialize();
        }

        return $response;
    }

    public static function fromArray(array $data): self
    {
        $data = self::validatedRelationMetadataShape($data);

        return new self(
            module: $data['module'] ?? null,
            directlyRelatedUserTypes: isset($data['directly_related_user_types']) ? RelationReferences::fromArray($data['directly_related_user_types']) : null,
            sourceInfo: isset($data['source_info']) ? SourceInfo::fromArray($data['source_info']) : null,
        );
    }

    /**
     * Validates the shape of the relation metadata data.
     *
     * @param array{module?: string, directly_related_user_types?: RelationReferencesShape, source_info?: SourceInfoShape} $data
     *
     * @throws InvalidArgumentException
     *
     * @return RelationMetadataShape
     */
    public static function validatedRelationMetadataShape(array $data): array
    {
        if (! isset($data['module'])) {
            throw new InvalidArgumentException('Missing module');
        }

        return $data;
    }
}
