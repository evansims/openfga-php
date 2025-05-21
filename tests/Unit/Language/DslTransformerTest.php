<?php

declare(strict_types=1);

use OpenFGA\Language\DslTransformer;
use OpenFGA\Models\AuthorizationModelInterface;
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
        ->registerSchema(OpenFGA\Models\AuthorizationModel::schema())
        ->registerSchema(OpenFGA\Models\Collections\TypeDefinitions::schema())
        ->registerSchema(OpenFGA\Models\TypeDefinition::schema())
        ->registerSchema(OpenFGA\Models\Collections\TypeDefinitionRelations::schema())
        ->registerSchema(OpenFGA\Models\Userset::schema())
        ->registerSchema(OpenFGA\Models\Collections\Usersets::schema())
        ->registerSchema(OpenFGA\Models\ObjectRelation::schema());

    $model = DslTransformer::fromDsl($dsl, $validator);

    expect($model)->toBeInstanceOf(AuthorizationModelInterface::class);
    expect($model->getSchemaVersion()->value)->toBe('1.1');

    $resultDsl = DslTransformer::toDsl($model);
    expect(trim($resultDsl))->toBe(trim($dsl));
});
