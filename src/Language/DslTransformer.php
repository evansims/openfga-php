<?php

declare(strict_types=1);

namespace OpenFGA\Language;

use OpenFGA\Models\{AuthorizationModel, AuthorizationModelInterface, DifferenceV1Interface, ObjectRelationInterface, RelationMetadataInterface, TupleToUsersetV1Interface, UsersetInterface};
use OpenFGA\Models\Collections\{RelationReferencesInterface, UsersetsInterface};
use OpenFGA\Models\Enums\SchemaVersion;
use OpenFGA\Schema\SchemaValidator;
use Override;
use RuntimeException;

use stdClass;

use function count;
use function is_array;
use function is_string;
use function strlen;

final class DslTransformer implements DslTransformerInterface
{
    #[Override]
    /**
     * @inheritDoc
     */
    public static function fromDsl(string $dsl, SchemaValidator $validator): AuthorizationModelInterface
    {
        $lines = preg_split('/\r?\n/', $dsl);
        $schemaVersion = SchemaVersion::V1_1;
        $typeDefinitions = [];

        $currentType = null;
        $relations = [];
        $relationMetadata = [];

        if (! is_array($lines)) {
            throw new RuntimeException('Failed to parse DSL input.');
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

            if (1 === preg_match('/^type\s+(\w+)/', $line, $m)) {
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

            if (1 === preg_match('/^define\s+(\w+)\s*:\s*(.+)$/', $line, $m)) {
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

    #[Override]
    /**
     * @inheritDoc
     */
    public static function toDsl(AuthorizationModelInterface $model): string
    {
        $lines = [];
        $lines[] = 'model';
        $lines[] = '  schema ' . $model->getSchemaVersion()->value;
        $lines[] = ''; // Blank line after schema

        foreach ($model->getTypeDefinitions() as $typeDefinition) {
            $lines[] = 'type ' . $typeDefinition->getType();
            $relations = $typeDefinition->getRelations();

            if (null !== $relations && count($relations) > 0) {
                $lines[] = '  relations';

                // Get metadata for this type to access directly_related_user_types
                $metadata = $typeDefinition->getMetadata();
                $relationMetadataCollection = $metadata?->getRelations();

                foreach ($relations as $name => $userset) {
                    $relMetadata = null;
                    // Check if the collection has the relation by key
                    if (null !== $relationMetadataCollection && (method_exists($relationMetadataCollection, 'has') && $relationMetadataCollection->has($name))) {
                        $relMetadata = $relationMetadataCollection->get($name);
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
     * @param string                              $type
     * @param array<string, array<string, mixed>> $relations
     * @param array<string, array<string, mixed>> $relationMetadata
     *
     * @return array<string, mixed>
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
     * Parse exclusion expressions (highest precedence).
     *
     * @param string $expr
     *
     * @return array{userset: array<string, mixed>, directly_related_user_types: array<array<string, mixed>>}
     */
    private static function parseExclusionExpression(string $expr): array
    {
        // Check for "but not" pattern using regex for flexible spacing and case-insensitive matching
        $butNotParts = array_map('trim', self::splitRespectingParenthesesWithRegex($expr, '/\s+but\s+not\s+/i'));

        if (count($butNotParts) > 1) {
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
     * @return array{userset: array<string, mixed>, metadata: array<string, mixed>}
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
        if (1 === preg_match('/^(\w+)\s+from\s+(\w+)$/', $term, $m)) {
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
        if (1 === preg_match('/^\[(.+)\]$/', $term, $m)) {
            $types = array_map('trim', explode(',', $m[1]));
            foreach ($types as $type) {
                if (1 === preg_match('/^(\w+)#(\w+)$/', $type, $tm)) {
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
                'userset' => ['this' => new stdClass()],
                'directly_related_user_types' => $directlyRelatedUserTypes,
            ];
        }

        // Handle computed userset with object: "object#relation"
        if (1 === preg_match('/^(\w+)#(\w+)$/', $term, $m)) {
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
        if (1 === preg_match('/^(\w+)$/', $term, $m)) {
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
        throw new RuntimeException('Unrecognized DSL term: ' . $term);
    }

    /**
     * Parse union expressions (lowest precedence).
     *
     * @param string $expr
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

    private static function renderExpression(UsersetInterface $userset, ?RelationMetadataInterface $metadata = null): string
    {
        // Handle union (or) operations
        if ($userset->getUnion() instanceof UsersetsInterface) {
            $parts = [];
            foreach ($userset->getUnion() as $child) {
                $parts[] = self::renderExpression($child, $metadata);
            }

            return implode(' or ', $parts);
        }

        // Handle intersection (and) operations
        if ($userset->getIntersection() instanceof UsersetsInterface) {
            $parts = [];
            foreach ($userset->getIntersection() as $child) {
                $parts[] = self::renderExpression($child, $metadata);
            }

            return implode(' and ', $parts);
        }

        // Handle exclusion (but not) operations
        if ($userset->getDifference() instanceof DifferenceV1Interface) {
            $base = self::renderExpression($userset->getDifference()->getBase(), $metadata);
            $subtract = self::renderExpression($userset->getDifference()->getSubtract(), $metadata);

            // Wrap subtract in parentheses if it's a union or intersection
            $subtractUserset = $userset->getDifference()->getSubtract();
            if ($subtractUserset->getUnion() instanceof UsersetsInterface || $subtractUserset->getIntersection() instanceof UsersetsInterface) {
                $subtract = '(' . $subtract . ')';
            }

            return $base . ' but not ' . $subtract;
        }

        // Handle tuple-to-userset (from) operations
        if ($userset->getTupleToUserset() instanceof TupleToUsersetV1Interface) {
            $ttu = $userset->getTupleToUserset();
            $tupleset = $ttu->getTupleset();
            $computedUserset = $ttu->getComputedUserset();

            $relation = $computedUserset->getRelation();
            $fromRelation = $tupleset->getRelation();

            return $relation . ' from ' . $fromRelation;
        }

        // Handle computed usersets
        if ($userset->getComputedUserset() instanceof ObjectRelationInterface) {
            $cu = $userset->getComputedUserset();
            $object = $cu->getObject();
            $relation = $cu->getRelation();

            if (null === $object || '' === $object) {
                if (! is_string($relation)) {
                    throw new RuntimeException('Invalid computed userset.');
                }

                return $relation;
            }

            if (null === $relation || '' === $relation) {
                return $object;
            }

            return $object . '#' . $relation;
        }

        // Handle direct relationships (this)
        if (null !== $userset->getDirect()) {
            return self::renderDirectRelationships($metadata);
        }

        return 'self';
    }

    /**
     * Split a string by a regex pattern, but respect parentheses.
     *
     * @param string $str
     * @param string $pattern
     *
     * @throws RuntimeException if input is invalid or parentheses are unbalanced
     *
     * @return array<string>
     */
    private static function splitRespectingParenthesesWithRegex(string $str, string $pattern): array
    {
        // Validate inputs
        if ('' === $str) {
            throw new RuntimeException('Input string cannot be empty');
        }

        if ('' === $pattern) {
            throw new RuntimeException('Pattern cannot be empty');
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
                if ($depth < 0) {
                    throw new RuntimeException('Unbalanced parentheses: too many closing parentheses at position ' . $i);
                }
            }

            // If we're not inside parentheses, check for regex match
            if (0 === $depth) {
                // Look ahead to see if we have a match starting at current position
                $remaining = substr($str, $i);
                if (1 === preg_match($pattern, $remaining, $matches, PREG_OFFSET_CAPTURE)) {
                    $matchStart = $matches[0][1];
                    $matchLength = strlen($matches[0][0]);

                    // If match starts at the beginning of remaining string
                    if (0 === $matchStart) {
                        $parts[] = $current;
                        $current = '';
                        $i += $matchLength - 1; // Skip the matched delimiter

                        continue;
                    }
                }
            }

            $current .= $str[$i];
        }

        // Check for unbalanced parentheses after processing
        if (0 !== $depth) {
            throw new RuntimeException('Unbalanced parentheses: ' . $depth . ' unclosed opening ' . (1 === $depth ? 'parenthesis' : 'parentheses'));
        }

        if ('' !== $current) {
            $parts[] = $current;
        }

        return $parts;
    }
}
