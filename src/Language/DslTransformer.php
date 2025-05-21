<?php

declare(strict_types=1);

namespace OpenFGA\Language;

use OpenFGA\Models\{AuthorizationModel, AuthorizationModelInterface, UsersetInterface};
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
                    $typeDefinitions[] = [
                        'type' => $currentType,
                        'relations' => [] === $relations ? null : $relations,
                    ];
                    $relations = [];
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
                $relations[$relName] = self::parseExpression($expr);
            }
        }

        if (null !== $currentType) {
            $typeDefinitions[] = [
                'type' => $currentType,
                'relations' => [] === $relations ? null : $relations,
            ];
        }

        $data = [
            'id' => uniqid('model_', true),
            'schema_version' => $schemaVersion->value,
            'type_definitions' => $typeDefinitions,
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

        foreach ($model->getTypeDefinitions() as $typeDefinition) {
            $lines[] = 'type ' . $typeDefinition->getType();
            $relations = $typeDefinition->getRelations();

            if (null !== $relations && count($relations) > 0) {
                $lines[] = '  relations';

                foreach ($relations as $name => $userset) {
                    $lines[] = '    define ' . $name . ': ' . self::renderExpression($userset);
                }
            }
        }

        return implode("\n", $lines);
    }

    /**
     * @param string $expr
     *
     * @return array<string, mixed>
     */
    private static function parseExpression(string $expr): array
    {
        $terms = array_map(
            static function (string $term): array {
                if ('self' === $term) {
                    return ['direct' => new stdClass()];
                }

                if (1 === preg_match('/^(\w+)#(\w+)$/', $term, $m)) {
                    return [
                        'computed_userset' => [
                            'object' => $m[1],
                            'relation' => $m[2],
                        ],
                    ];
                }

                return [
                    'computed_userset' => [
                        'relation' => $term,
                    ],
                ];
            },
            array_map('trim', explode('or', $expr)),
        );

        return 1 === count($terms) ? $terms[0] : ['union' => $terms];
    }

    private static function renderExpression(UsersetInterface $userset): string
    {
        if ($userset->getUnion() instanceof \OpenFGA\Models\Collections\UsersetsInterface) {
            $parts = [];
            foreach ($userset->getUnion() as $child) {
                $parts[] = self::renderExpression($child);
            }

            return implode(' or ', $parts);
        }

        if (null !== $userset->getDirect()) {
            return 'self';
        }

        if ($userset->getComputedUserset() instanceof \OpenFGA\Models\ObjectRelationInterface) {
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

        return 'self';
    }
}
