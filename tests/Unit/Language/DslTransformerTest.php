<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Language;

use OpenFGA\Language\DslTransformer;
use OpenFGA\Models\Collections\{TypeDefinitionRelations, TypeDefinitions, Usersets};
use OpenFGA\Models\{AuthorizationModelInterface, AuthorizationModel, TypeDefinition, Userset, ObjectRelation};
use OpenFGA\Schema\SchemaValidator;

it('transforms DSL to model and back', function (): void {
    $dsl = <<<'DSL'
        model
          schema 1.1

        type user

        type document
          relations
            define viewer: self
            define writer: user
        DSL;

    $validator = new SchemaValidator();

    // Register schemas used by AuthorizationModel
    $validator
        ->registerSchema(AuthorizationModel::schema())
        ->registerSchema(TypeDefinitions::schema())
        ->registerSchema(TypeDefinition::schema())
        ->registerSchema(TypeDefinitionRelations::schema())
        ->registerSchema(Userset::schema())
        ->registerSchema(Usersets::schema())
        ->registerSchema(ObjectRelation::schema());

    $model = DslTransformer::fromDsl($dsl, $validator);

    expect($model)->toBeInstanceOf(AuthorizationModelInterface::class);
    expect($model->getSchemaVersion()->value)->toBe('1.1');

    $resultDsl = DslTransformer::toDsl($model);
    expect(trim($resultDsl))->toBe(trim($dsl));
});
