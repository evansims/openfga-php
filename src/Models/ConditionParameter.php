<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use OpenFGA\Models\Collections\{ConditionParameters, ConditionParametersInterface};
use OpenFGA\Models\Enums\TypeName;
use OpenFGA\Schema\{Schema, SchemaInterface, SchemaProperty};
use Override;

final class ConditionParameter implements ConditionParameterInterface
{
    public const OPENAPI_MODEL = 'ConditionParamTypeRef';

    private static ?SchemaInterface $schema = null;

    /**
     * @param TypeName                                                  $typeName
     * @param ConditionParametersInterface<ConditionParameterInterface> $genericTypes
     */
    public function __construct(
        private readonly TypeName $typeName,
        private readonly ?ConditionParametersInterface $genericTypes = null,
    ) {
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getGenericTypes(): ?ConditionParametersInterface
    {
        return $this->genericTypes;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getTypeName(): TypeName
    {
        return $this->typeName;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function jsonSerialize(): array
    {
        return array_filter([
            'type_name' => $this->typeName->value,
            'generic_types' => $this->genericTypes?->jsonSerialize(),
        ], static fn ($value): bool => null !== $value);
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
                new SchemaProperty(name: 'type_name', type: 'object', className: TypeName::class, required: true),
                new SchemaProperty(name: 'generic_types', type: 'object', className: ConditionParameters::class, required: false),
            ],
        );
    }
}
