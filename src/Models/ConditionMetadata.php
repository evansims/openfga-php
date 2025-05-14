<?php

declare(strict_types=1);

namespace OpenFGA\Models;

final class ConditionMetadata implements ConditionMetadataInterface
{
    public function __construct(
        private string $module,
        private SourceInfoInterface $sourceInfo,
    ) {
    }

    public function getModule(): string
    {
        return $this->module;
    }

    public function getSourceInfo(): SourceInfoInterface
    {
        return $this->sourceInfo;
    }

    public function jsonSerialize(): array
    {
        return [
            'module' => $this->module,
            'source_info' => $this->sourceInfo->jsonSerialize(),
        ];
    }

    public static function fromArray(array $data): self
    {
        $data = self::validatedConditionMetadataShape($data);

        return new self(
            module: $data['module'],
            sourceInfo: SourceInfo::fromArray($data['source_info']),
        );
    }

    /**
     * Validates the shape of the array to be used as condition metadata data. Throws an exception if the data is invalid.
     *
     * @param array{module: string, source_info: SourceInfoShape} $data
     *
     * @throws InvalidArgumentException
     *
     * @return ConditionMetadataShape
     */
    public static function validatedConditionMetadataShape(array $data): array
    {
        return $data;
    }
}
