<?php

declare(strict_types=1);

namespace OpenFGA\Language;

use InvalidArgumentException;
use OpenFGA\Exceptions\{ClientThrowable, SerializationError};
use OpenFGA\Messages;
use OpenFGA\Models\{AuthorizationModel, AuthorizationModelInterface, DifferenceV1Interface, ObjectRelationInterface, RelationMetadataInterface, TupleToUsersetV1Interface, UsersetInterface};
use OpenFGA\Models\Collections\{RelationMetadataCollection, TypeDefinitionRelationsInterface};
use OpenFGA\Models\Collections\{RelationReferencesInterface, UsersetsInterface};
use OpenFGA\Models\Enums\SchemaVersion;
use OpenFGA\Schemas\SchemaValidatorInterface;
use OpenFGA\Translation\Translator;
use Override;
use ReflectionException;
use stdClass;

use function count;
use function is_array;
use function strlen;

/**
 * OpenFGA DSL Transformer implementation for authorization model conversions.
 *
 * This class provides complete implementation for converting between OpenFGA's
 * Domain Specific Language (DSL) format and structured authorization model objects.
 * It supports complex relationship definitions including unions, intersections,
 * exclusions, and computed usersets with proper precedence handling.
 *
 * The transformer parses DSL syntax including:
 * - Type definitions with relations
 * - Direct user assignments [user, organization#member]
 * - Computed usersets (owner, administrator)
 * - Tuple-to-userset relations (owner from parent)
 * - Boolean operations (and, or, but not)
 * - Parenthetical grouping for precedence
 *
 * @see TransformerInterface For the interface specification
 * @see https://openfga.dev/docs/authorization-concepts OpenFGA authorization concepts
 */
final class Transformer implements TransformerInterface
{
    /**
     * @inheritDoc
     *
     * @throws InvalidArgumentException If DSL parsing fails or translation parameters are invalid
     * @throws ReflectionException      If exception location capture fails
     */
    #[Override]
    public static function fromDsl(string $dsl, SchemaValidatorInterface $validator): AuthorizationModelInterface
    {
        $lines = preg_split('/\r?\n/', $dsl);
        $schemaVersion = SchemaVersion::V1_1;
        $typeDefinitions = [];

        $currentType = null;
        $relations = [];
        $relationMetadata = [];

        if (! is_array($lines)) {
            throw SerializationError::InvalidItemType->exception(context: ['message' => Translator::trans(Messages::DSL_PARSE_FAILED)]);
        }

        foreach ($lines as $line) {
            $line = trim($line);

            if ('' === $line) {
                continue;
            }

            if (str_starts_with($line, '#')) {
                continue;
            }

            if ('model' === $line) {
                continue;
            }

            if (str_starts_with($line, 'schema')) {
                $version = trim(substr($line, strlen('schema')));

                if ('' !== $version) {
                    $schemaVersion = SchemaVersion::from($version);
                }

                continue;
            }

            if (1 === preg_match('/^type\s+(\w+)/', $line, $m) && isset($m[1])) { /* @phpstan-ignore-line isset.offset */
                if (null !== $currentType) {
                    $typeDefinitions[] = self::buildTypeDefinition($currentType, $relations, $relationMetadata);
                    $relations = [];
                    $relationMetadata = [];
                }
                $currentType = $m[1];

                continue;
            }

            if ('relations' === $line) {
                continue;
            }

            if (1 === preg_match('/^define\s+(\w+)\s*:\s*(.+)$/', $line, $m) && isset($m[1], $m[2])) { /** @phpstan-ignore-line isset.offset */
                $relName = $m[1];
                $expr = $m[2];
                $parseResult = self::parseExpressionWithMetadata($expr);
                $relations[$relName] = $parseResult['userset'];
                $relationMetadata[$relName] = $parseResult['metadata'];
            }
        }

        if (null !== $currentType) {
            $typeDefinitions[] = self::buildTypeDefinition($currentType, $relations, $relationMetadata);
        }

        $data = [
            'id' => uniqid('model_', true),
            'schema_version' => $schemaVersion->value,
            'type_definitions' => $typeDefinitions,
            'conditions' => [],
        ];

        return $validator->validateAndTransform($data, AuthorizationModel::class);
    }

    /**
     * @inheritDoc
     *
     * @throws InvalidArgumentException If model conversion fails or translation parameters are invalid
     * @throws ReflectionException      If exception location capture fails
     */
    #[Override]
    public static function toDsl(AuthorizationModelInterface $model): string
    {
        $lines = [];
        $lines[] = 'model';
        $lines[] = '  schema ' . $model->getSchemaVersion()->value;
        $lines[] = ''; // Blank line after schema

        foreach ($model->getTypeDefinitions() as $typeDefinition) {
            $lines[] = 'type ' . $typeDefinition->getType();
            $relations = $typeDefinition->getRelations();

            if ($relations instanceof TypeDefinitionRelationsInterface && 0 < count($relations)) {
                $lines[] = '  relations';

                // Get metadata for this type to access directly_related_user_types
                $metadata = $typeDefinition->getMetadata();
                $relationMetadataCollection = $metadata?->getRelations();

                foreach ($relations as $name => $userset) {
                    $relMetadata = null;

                    // Check if the collection has the relation by key
                    if ($relationMetadataCollection instanceof RelationMetadataCollection && $relationMetadataCollection->has($name)) {
                        $metadata = $relationMetadataCollection->get($name);
                        $relMetadata = $metadata instanceof RelationMetadataInterface ? $metadata : null;
                    }
                    $lines[] = '    define ' . $name . ': ' . self::renderExpression($userset, $relMetadata);
                }
            }

            $lines[] = ''; // Blank line after each type definition
        }

        // Remove the trailing blank line
        if ('' === end($lines)) {
            array_pop($lines);
        }

        return implode("\n", $lines);
    }

    /**
     * @param  string                                          $type
     * @param  array<string, array<string, mixed>>             $relations
     * @param  array<string, array<string, mixed>>             $relationMetadata
     * @return (((array|mixed)[]|string|null)[]|string|null)[]
     *
     * @psalm-return array{type: string, relations: array<string, array<string, mixed>>, metadata: array{relations: array<string, array<string, mixed>>, module: '', source_info: null}|null}
     */
    private static function buildTypeDefinition(string $type, array $relations, array $relationMetadata): array
    {
        $typeDefinition = [
            'type' => $type,
            'relations' => $relations,
        ];

        // Build metadata if there are relations
        if ([] !== $relationMetadata) {
            $typeDefinition['metadata'] = [
                'relations' => $relationMetadata,
                'module' => '',
                'source_info' => null,
            ];
        } else {
            $typeDefinition['metadata'] = null;
        }

        return $typeDefinition;
    }

    /**
     * Determine if parentheses are needed around an expression in a given context.
     *
     * @param  UsersetInterface $userset The userset expression to check
     * @param  string           $context The context ('union', 'intersection', etc.)
     * @return bool             True if parentheses are needed
     */
    private static function needsParentheses(UsersetInterface $userset, string $context): bool
    {
        // For union context, wrap intersections in parentheses
        if ('union' === $context) {
            return $userset->getIntersection() instanceof UsersetsInterface;
        }

        // For intersection context, wrap unions in parentheses
        if ('intersection' === $context) {
            return $userset->getUnion() instanceof UsersetsInterface;
        }

        return false;
    }

    /**
     * Parse exclusion expressions (highest precedence).
     *
     * @param string $expr
     *
     * @throws ClientThrowable          If parsing fails
     * @throws InvalidArgumentException If expression is invalid or translation parameters are invalid
     * @throws ReflectionException      If exception location capture fails
     *
     * @return array{userset: array<string, mixed>, directly_related_user_types: array<array<string, mixed>>}
     */
    private static function parseExclusionExpression(string $expr): array
    {
        // Check for "but not" pattern using regular expression for flexible spacing and case-insensitive matching
        $butNotParts = array_map('trim', self::splitRespectingParenthesesWithRegex($expr, '/\s+but\s+not\s+/i'));

        if (1 < count($butNotParts)) {
            // Process multiple "but not" operations from left to right
            $result = self::parseSingleTermWithMetadata($butNotParts[0]);
            $counter = count($butNotParts);

            for ($i = 1; $i < $counter; ++$i) {
                // Handle the subtract part - strip parentheses if present
                $subtractExpr = trim($butNotParts[$i]);

                if (str_starts_with($subtractExpr, '(') && str_ends_with($subtractExpr, ')')) {
                    $subtractExpr = substr($subtractExpr, 1, -1);
                }
                $excludeResult = self::parseUnionExpression($subtractExpr);

                $result = [
                    'userset' => [
                        'difference' => [
                            'base' => $result['userset'],
                            'subtract' => $excludeResult['userset'],
                        ],
                    ],
                    'directly_related_user_types' => array_merge(
                        $result['directly_related_user_types'],
                        $excludeResult['directly_related_user_types'],
                    ),
                ];
            }

            return $result;
        }

        return self::parseSingleTermWithMetadata($expr);
    }

    /**
     * @param string $expr
     *
     * @throws ClientThrowable          If parsing fails
     * @throws InvalidArgumentException If expression is invalid or translation parameters are invalid
     * @throws ReflectionException      If exception location capture fails
     *
     * @return array[]
     *
     * @psalm-return array{userset: array<string, mixed>, metadata: array{directly_related_user_types: array<array<string, mixed>>, module: '', source_info: null}}
     */
    private static function parseExpressionWithMetadata(string $expr): array
    {
        // Parse with operator precedence: "but not" > "and" > "or"
        $result = self::parseUnionExpression($expr);

        // Build metadata
        $metadata = [
            'directly_related_user_types' => $result['directly_related_user_types'],
            'module' => '',
            'source_info' => null,
        ];

        return [
            'userset' => $result['userset'],
            'metadata' => $metadata,
        ];
    }

    /**
     * Parse intersection expressions (medium precedence).
     *
     * @param string $expr
     *
     * @throws ClientThrowable          If parsing fails
     * @throws InvalidArgumentException If expression is invalid or translation parameters are invalid
     * @throws ReflectionException      If exception location capture fails
     *
     * @return array{userset: array<string, mixed>, directly_related_user_types: array<array<string, mixed>>}
     */
    private static function parseIntersectionExpression(string $expr): array
    {
        $andTerms = array_map('trim', self::splitRespectingParenthesesWithRegex($expr, '/\s+and\s+/i'));

        if (1 === count($andTerms)) {
            return self::parseExclusionExpression($andTerms[0]);
        }

        $parsedTerms = [];
        $directlyRelatedUserTypes = [];

        foreach ($andTerms as $andTerm) {
            $termResult = self::parseExclusionExpression($andTerm);
            $parsedTerms[] = $termResult['userset'];
            $directlyRelatedUserTypes = array_merge($directlyRelatedUserTypes, $termResult['directly_related_user_types']);
        }

        return [
            'userset' => ['intersection' => ['child' => $parsedTerms]],
            'directly_related_user_types' => $directlyRelatedUserTypes,
        ];
    }

    /**
     * @param string $term
     *
     * @throws ClientThrowable          If parsing fails
     * @throws InvalidArgumentException If term is invalid or translation parameters are invalid
     * @throws ReflectionException      If exception location capture fails
     *
     * @return array{userset: array<string, mixed>, directly_related_user_types: array<array<string, mixed>>}
     */
    private static function parseSingleTermWithMetadata(string $term): array
    {
        $term = trim($term);
        $directlyRelatedUserTypes = [];

        // Handle parentheses - parse as a grouped expression
        if (str_starts_with($term, '(') && str_ends_with($term, ')')) {
            return self::parseUnionExpression(substr($term, 1, -1));
        }

        // Handle tuple-to-userset pattern: "relation from another_relation"
        if (1 === preg_match('/^(\w+)\s+from\s+(\w+)$/', $term, $m) && isset($m[1], $m[2])) { /* @phpstan-ignore-line isset.offset */
            return [
                'userset' => [
                    'tupleToUserset' => [
                        'tupleset' => [
                            'object' => '',
                            'relation' => $m[2],
                        ],
                        'computedUserset' => [
                            'object' => '',
                            'relation' => $m[1],
                        ],
                    ],
                ],
                'directly_related_user_types' => [],
            ];
        }

        // Handle direct type assignments: [user] or [user, organization#member]
        if (1 === preg_match('/^\[(.+)\]$/', $term, $m) && isset($m[1])) { /** @phpstan-ignore-line isset.offset */
            $types = array_map('trim', explode(',', $m[1]));

            foreach ($types as $type) {
                if (1 === preg_match('/^(\w+)#(\w+)$/', $type, $tm) && isset($tm[1], $tm[2])) { /* @phpstan-ignore-line isset.offset */
                    // Userset type like "organization#member"
                    $directlyRelatedUserTypes[] = [
                        'type' => $tm[1],
                        'relation' => $tm[2],
                        'condition' => '',
                    ];
                } else {
                    // Simple type like "user"
                    $directlyRelatedUserTypes[] = [
                        'type' => $type,
                        'condition' => '',
                    ];
                }
            }

            return [
                'userset' => ['this' => new stdClass],
                'directly_related_user_types' => $directlyRelatedUserTypes,
            ];
        }

        // Handle computed userset with object: "object#relation"
        if (1 === preg_match('/^(\w+)#(\w+)$/', $term, $m) && isset($m[1], $m[2])) { /* @phpstan-ignore-line isset.offset */
            return [
                'userset' => [
                    'computedUserset' => [
                        'object' => $m[1],
                        'relation' => $m[2],
                    ],
                ],
                'directly_related_user_types' => [],
            ];
        }

        // Handle simple computed userset: just "relation"
        if (1 === preg_match('/^(\w+)$/', $term, $m) && isset($m[1])) { /* @phpstan-ignore-line isset.offset */
            return [
                'userset' => [
                    'computedUserset' => [
                        'object' => '',
                        'relation' => $m[1],
                    ],
                ],
                'directly_related_user_types' => [],
            ];
        }

        // Fallback for unrecognized patterns
        throw SerializationError::InvalidItemType->exception(context: ['message' => Translator::trans(Messages::DSL_UNRECOGNIZED_TERM, ['term' => $term])]);
    }

    /**
     * Parse union expressions (lowest precedence).
     *
     * @param string $expr
     *
     * @throws ClientThrowable          If parsing fails
     * @throws InvalidArgumentException If expression is invalid or translation parameters are invalid
     * @throws ReflectionException      If exception location capture fails
     *
     * @return array{userset: array<string, mixed>, directly_related_user_types: array<array<string, mixed>>}
     */
    private static function parseUnionExpression(string $expr): array
    {
        $orTerms = array_map('trim', self::splitRespectingParenthesesWithRegex($expr, '/\s+or\s+/i'));

        if (1 === count($orTerms)) {
            return self::parseIntersectionExpression($orTerms[0]);
        }

        $parsedTerms = [];
        $directlyRelatedUserTypes = [];

        foreach ($orTerms as $orTerm) {
            $termResult = self::parseIntersectionExpression($orTerm);
            $parsedTerms[] = $termResult['userset'];
            $directlyRelatedUserTypes = array_merge($directlyRelatedUserTypes, $termResult['directly_related_user_types']);
        }

        return [
            'userset' => ['union' => ['child' => $parsedTerms]],
            'directly_related_user_types' => $directlyRelatedUserTypes,
        ];
    }

    /**
     * Render direct relationships using metadata.
     *
     * @param ?RelationMetadataInterface $metadata
     *
     * @throws ClientThrowable          If rendering fails
     * @throws InvalidArgumentException If metadata validation fails or translation parameters are invalid
     * @throws ReflectionException      If exception location capture fails
     */
    private static function renderDirectRelationships(?RelationMetadataInterface $metadata): string
    {
        if (! $metadata instanceof RelationMetadataInterface) {
            return 'self';
        }

        $directTypes = $metadata->getDirectlyRelatedUserTypes();

        if (! $directTypes instanceof RelationReferencesInterface || 0 === count($directTypes)) {
            return 'self';
        }

        $types = [];

        foreach ($directTypes as $directType) {
            $type = $directType->getType();
            $relation = $directType->getRelation();

            $types[] = null !== $relation && '' !== $relation ? $type . '#' . $relation : $type;
        }

        if ([] === $types) {
            return 'self';
        }

        return '[' . implode(', ', $types) . ']';
    }

    /**
     * @param UsersetInterface           $userset
     * @param ?RelationMetadataInterface $metadata
     *
     * @throws ClientThrowable          If rendering fails
     * @throws InvalidArgumentException If computed userset is invalid or translation parameters are invalid
     * @throws ReflectionException      If exception location capture fails
     */
    private static function renderExpression(UsersetInterface $userset, ?RelationMetadataInterface $metadata = null): string
    {
        // Handle union (or) operations
        $union = $userset->getUnion();

        if ($union instanceof UsersetsInterface) {
            $parts = [];

            foreach ($union as $child) {
                $childRendered = self::renderExpression($child, $metadata);
                // Use helper to determine if parentheses are needed
                $parts[] = self::needsParentheses($child, 'union') ? '(' . $childRendered . ')' : $childRendered;
            }

            return implode(' or ', $parts);
        }

        // Handle intersection (and) operations
        $intersection = $userset->getIntersection();

        if ($intersection instanceof UsersetsInterface) {
            $parts = [];

            foreach ($intersection as $child) {
                $childRendered = self::renderExpression($child, $metadata);

                // If child is a union, wrap in parentheses
                if ($child->getUnion() instanceof UsersetsInterface) {
                    $parts[] = '(' . $childRendered . ')';
                } else {
                    $parts[] = $childRendered;
                }
            }

            return implode(' and ', $parts);
        }

        // Handle exclusion (but not) operations
        $difference = $userset->getDifference();

        if ($difference instanceof DifferenceV1Interface) {
            $baseUserset = $difference->getBase();
            $base = self::renderExpression($baseUserset, $metadata);

            // Wrap base in parentheses if it's a union or intersection
            if ($baseUserset->getUnion() instanceof UsersetsInterface || $baseUserset->getIntersection() instanceof UsersetsInterface) {
                $base = '(' . $base . ')';
            }

            $subtractUserset = $difference->getSubtract();
            $subtract = self::renderExpression($subtractUserset, $metadata);

            // Wrap subtract in parentheses if it's a union or intersection
            if ($subtractUserset->getUnion() instanceof UsersetsInterface || $subtractUserset->getIntersection() instanceof UsersetsInterface) {
                $subtract = '(' . $subtract . ')';
            }

            return $base . ' but not ' . $subtract;
        }

        // Handle tuple-to-userset (from) operations
        $tupleToUserset = $userset->getTupleToUserset();

        if ($tupleToUserset instanceof TupleToUsersetV1Interface) {
            $tupleset = $tupleToUserset->getTupleset();
            $computedUserset = $tupleToUserset->getComputedUserset();

            $relation = $computedUserset->getRelation();
            $fromRelation = $tupleset->getRelation();

            return $relation . ' from ' . $fromRelation;
        }

        // Handle computed usersets
        $computedUserset = $userset->getComputedUserset();

        if ($computedUserset instanceof ObjectRelationInterface) {
            $object = $computedUserset->getObject();
            $relation = $computedUserset->getRelation();

            // Existing logic for constructing the string
            if (null === $object || '' === $object) {
                // $relation is guaranteed to be a non-empty string due to ObjectRelation constructor validation.
                return $relation;
            }

            // If object is not empty, relation is already validated to be non-empty string.
            return $object . '#' . $relation;
        }

        // Handle direct relationships (this)
        if (null !== $userset->getDirect()) {
            return self::renderDirectRelationships($metadata);
        }

        return 'self';
    }

    /**
     * Split a string by a regular expression pattern, but respect parentheses.
     *
     * @param string $str
     * @param string $pattern
     *
     * @throws ClientThrowable          If parsing fails
     * @throws InvalidArgumentException If input is invalid or parentheses are unbalanced or translation parameters are invalid
     * @throws ReflectionException      If exception location capture fails
     *
     * @return string[]
     *
     * @psalm-return list<string>
     */
    private static function splitRespectingParenthesesWithRegex(string $str, string $pattern): array
    {
        // Validate inputs
        if ('' === $str) {
            throw SerializationError::InvalidItemType->exception(context: ['message' => Translator::trans(Messages::DSL_INPUT_EMPTY)]);
        }

        if ('' === $pattern) {
            throw SerializationError::InvalidItemType->exception(context: ['message' => Translator::trans(Messages::DSL_PATTERN_EMPTY)]);
        }

        $parts = [];
        $current = '';
        $depth = 0;
        $len = strlen($str);

        for ($i = 0; $i < $len; ++$i) {
            if ('(' === $str[$i]) {
                ++$depth;
            } elseif (')' === $str[$i]) {
                --$depth;

                // Check for too many closing parentheses
                if (0 > $depth) {
                    throw SerializationError::InvalidItemType->exception(context: ['message' => Translator::trans(Messages::DSL_UNBALANCED_PARENTHESES_CLOSING, ['position' => $i])]);
                }
            }

            // If we're not inside parentheses, check for regular expression match
            if (0 === $depth) {
                // Look ahead to see if we have a match starting at current position
                $remaining = substr($str, $i);

                if (1 === preg_match($pattern, $remaining, $matches, PREG_OFFSET_CAPTURE) && isset($matches[0][0], $matches[0][1])) {
                    $matchStart = $matches[0][1];
                    $matchLength = strlen($matches[0][0]);

                    // If match starts at the beginning of remaining string
                    if (0 === $matchStart) {
                        $parts[] = $current;
                        $current = '';
                        $i += max(0, $matchLength - 1); // Advance by matchLength - 1 since for loop will increment

                        continue;
                    }
                }
            }

            $current .= $str[$i];
        }

        // Check for unbalanced parentheses after processing
        if (0 !== $depth) {
            throw SerializationError::InvalidItemType->exception(context: ['message' => Translator::trans(Messages::DSL_UNBALANCED_PARENTHESES_OPENING, ['count' => $depth, 'parentheses' => 1 === $depth ? 'parenthesis' : 'parentheses'])]);
        }

        if ('' !== $current) {
            $parts[] = $current;
        }

        return $parts;
    }
}
