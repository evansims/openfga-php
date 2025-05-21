<?php

declare(strict_types=1);

namespace OpenFGA\Language;

use OpenFGA\Models\{AuthorizationModel, AuthorizationModelInterface, TypeDefinitionInterface, UsersetInterface};
use OpenFGA\Models\Collections\{TypeDefinitions, TypeDefinitionRelations, Usersets};
use OpenFGA\Models\Enums\SchemaVersion;
use OpenFGA\Schema\SchemaValidator;
use stdClass;

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
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            if ($line === 'model') {
                continue;
            }

            if (str_starts_with($line, 'schema')) {
                $version = trim(substr($line, strlen('schema')));
                if ($version !== '') {
                    $schemaVersion = SchemaVersion::from($version);
                }
                continue;
            }

            if (preg_match('/^type\s+(\w+)/', $line, $m)) {
                if ($currentType !== null) {
                    $typeDefinitions[] = [
                        'type' => $currentType,
                        'relations' => $relations === [] ? null : $relations,
                    ];
                    $relations = [];
                }
                $currentType = $m[1];
                continue;
            }

            if ($line === 'relations') {
                continue;
            }

            if (preg_match('/^define\s+(\w+)\s*:\s*(.+)$/', $line, $m)) {
                $relName = $m[1];
                $expr = $m[2];
                $relations[$relName] = self::parseExpression($expr);
            }
        }

        if ($currentType !== null) {
            $typeDefinitions[] = [
                'type' => $currentType,
                'relations' => $relations === [] ? null : $relations,
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

        foreach ($model->getTypeDefinitions() as $typeDef) {
            /** @var TypeDefinitionInterface $typeDef */
            $lines[] = 'type ' . $typeDef->getType();
            $relations = $typeDef->getRelations();
            if ($relations !== null && count($relations) > 0) {
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
        if (count($parsed) === 1) {
            return $parsed[0];
        }

        return ['union' => $parsed];
    }

    private static function parseTerm(string $term): array
    {
        if ($term === 'self') {
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
        if ($userset->getUnion() !== null) {
            $parts = [];
            foreach ($userset->getUnion() as $child) {
                $parts[] = self::renderExpression($child);
            }
            return implode(' or ', $parts);
        }

        if ($userset->getDirect() !== null) {
            return 'self';
        }

        if ($userset->getComputedUserset() !== null) {
            $cu = $userset->getComputedUserset();
            $object = $cu->getObject();
            $relation = $cu->getRelation();
            if ($object === null || $object === '') {
                return (string) $relation;
            }
            if ($relation === null || $relation === '') {
                return (string) $object;
            }
            return $object . '#' . $relation;
        }

        return 'self';
    }
}
