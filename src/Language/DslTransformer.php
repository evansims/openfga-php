<?php

declare(strict_types=1);

namespace OpenFGA\Language;

use OpenFGA\Models\{AuthorizationModel, AuthorizationModelInterface, TypeDefinitionInterface, UsersetInterface};
use OpenFGA\Models\Enums\SchemaVersion;
use OpenFGA\Schema\SchemaValidator;
use stdClass;

use function count;
use function strlen;

final class DslTransformer
{
    public static function fromDsl(string $dsl, SchemaValidator $validator): AuthorizationModelInterface
    {
        $lines = preg_split('/\r?\n/', $dsl);
        $schemaVersion = SchemaVersion::V1_1;
        $typeDefinitions = [];

        $currentType = null;
        $relations = [];

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

            if (preg_match('/^type\s+(\w+)/', $line, $m)) {
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

            if (preg_match('/^define\s+(\w+)\s*:\s*(.+)$/', $line, $m)) {
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

    public static function toDsl(AuthorizationModelInterface $model): string
    {
        $lines = [];
        $lines[] = 'model';
        $lines[] = '  schema ' . $model->getSchemaVersion()->value;

        foreach ($model->getTypeDefinitions() as $typeDefinition) {
            /** @var TypeDefinitionInterface $typeDef */
            $lines[] = 'type ' . $typeDefinition->getType();
            $relations = $typeDefinition->getRelations();
            if (null !== $relations && count($relations) > 0) {
                $lines[] = '  relations';
                foreach ($relations as $name => $userset) {
                    /** @var UsersetInterface $userset */
                    $lines[] = '    define ' . $name . ': ' . self::renderExpression($userset);
                }
            }
        }

        return implode("\n", $lines);
    }

    private static function parseExpression(string $expr): array
    {
        $terms = array_map('trim', explode('or', $expr));
        $parsed = array_map([self::class, 'parseTerm'], $terms);
        if (1 === count($parsed)) {
            return $parsed[0];
        }

        return ['union' => $parsed];
    }

    private static function parseTerm(string $term): array
    {
        if ('self' === $term) {
            return ['direct' => new stdClass()];
        }

        if (preg_match('/^(\w+)#(\w+)$/', $term, $m)) {
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
                return (string) $relation;
            }
            if (null === $relation || '' === $relation) {
                return (string) $object;
            }

            return $object . '#' . $relation;
        }

        return 'self';
    }
}
