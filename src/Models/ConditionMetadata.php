<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use OpenFGA\Schemas\{Schema, SchemaInterface, SchemaProperty};
use Override;

/**
 * Contains metadata information about conditions in your authorization model.
 *
 * ConditionMetadata provides context about ABAC (Attribute-Based Access Control)
 * conditions, including module organization and source information for debugging.
 * This helps you understand where conditions are defined and how they're
 * structured within your authorization model.
 *
 * Use this when working with conditional authorization rules that depend on
 * runtime attributes and context data.
 */
final class ConditionMetadata implements ConditionMetadataInterface
{
    public const string OPENAPI_MODEL = 'ConditionMetadata';

    private static ?SchemaInterface $schema = null;

    /**
     * @param string              $module     The module name for the condition metadata
     * @param SourceInfoInterface $sourceInfo The source information for the condition metadata
     */
    public function __construct(
        private readonly string $module,
        private readonly SourceInfoInterface $sourceInfo,
    ) {
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public static function schema(): SchemaInterface
    {
        return self::$schema ??= new Schema(
            className: self::class,
            properties: [
                new SchemaProperty(name: 'module', type: 'string', required: true),
                new SchemaProperty(name: 'source_info', type: 'object', className: SourceInfo::class, required: true),
            ],
        );
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getModule(): string
    {
        return $this->module;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getSourceInfo(): SourceInfoInterface
    {
        return $this->sourceInfo;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function jsonSerialize(): array
    {
        return [
            'module' => $this->module,
            'source_info' => $this->sourceInfo->jsonSerialize(),
        ];
    }
}
