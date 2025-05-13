<?php

declare(strict_types=1);

namespace OpenFGA\Models;

final class ConditionMetadata implements ConditionMetadataInterface
{
    use ModelTrait;

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
