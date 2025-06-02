<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use OpenFGA\Models\Collections\{ConditionParametersInterface};
use OpenFGA\Models\Enums\TypeName;
use Override;

/**
 * Defines a parameter type for use in authorization conditions.
 *
 * ConditionParameter represents the type definition for parameters that can be
 * passed to conditions during authorization evaluation. This includes simple
 * types like strings and integers, as well as complex types like lists and maps
 * with their own generic type parameters.
 *
 * Use this interface when defining conditions that accept typed parameters,
 * ensuring type safety during authorization evaluation.
 */
interface ConditionParameterInterface extends ModelInterface
{
    /**
     * Get the generic type parameters for complex types like maps and lists.
     *
     * This provides the nested type information for complex parameter types.
     * For example, a map parameter would have generic types defining the
     * key and value types, while a list parameter would define the element type.
     *
     * @return ?ConditionParametersInterface<ConditionParameterInterface> The generic type parameters, or null for simple types
     */
    public function getGenericTypes(): ?ConditionParametersInterface;

    /**
     * Get the primary type name of the parameter.
     *
     * This returns the fundamental type of the condition parameter,
     * such as string, int, bool, list, map, etc. This type information
     * is used during condition evaluation to ensure type safety.
     *
     * @return TypeName The type name enum value for this parameter
     */
    public function getTypeName(): TypeName;

    /**
     * @return array<'generic_types'|'type_name', 'TYPE_NAME_ANY'|'TYPE_NAME_BOOL'|'TYPE_NAME_DOUBLE'|'TYPE_NAME_DURATION'|'TYPE_NAME_INT'|'TYPE_NAME_IPADDRESS'|'TYPE_NAME_LIST'|'TYPE_NAME_MAP'|'TYPE_NAME_STRING'|'TYPE_NAME_TIMESTAMP'|'TYPE_NAME_UINT'|'TYPE_NAME_UNSPECIFIED'|list<array{generic_types?: array<int, mixed>, type_name: string}>>
     */
    #[Override]
    public function jsonSerialize(): array;
}
