<?php

declare(strict_types=1);

namespace OpenFGA\Models;

final class ConditionMetadata extends Model implements ConditionMetadataInterface
{
    public function __construct(
        public string $module,
        public SourceInfoInterface $sourceInfo,
    ) {
    }

    public function toArray(): array
    {
        return [
            'module' => $this->module,
            'source_info' => $this->sourceInfo->toArray(),
        ];
    }

    public static function fromArray(array $data): self
    {
        $module = $data['module'] ?? null;
        $module = $module ? (string) $module : null;

        $sourceInfo = $data['source_info'] ?? null;
        $sourceInfo = $sourceInfo ? SourceInfo::fromArray($sourceInfo) : null;

        return new self(
            module: $module,
            sourceInfo: $sourceInfo,
        );
    }
}
