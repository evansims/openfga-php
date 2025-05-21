# DSL Transformer

Transform DSL strings to authorization models and back.

```php
use OpenFGA\Language\DslTransformer;
use OpenFGA\Schema\SchemaValidator;

$dsl = <<'DSL'
model
  schema 1.1

type user

type document
  relations
    define viewer: self
DSL;

$validator = new SchemaValidator();
$validator
    ->registerSchema(OpenFGA\Models\AuthorizationModel::schema())
    ->registerSchema(OpenFGA\Models\Collections\TypeDefinitions::schema())
    ->registerSchema(OpenFGA\Models\TypeDefinition::schema())
    ->registerSchema(OpenFGA\Models\Collections\TypeDefinitionRelations::schema())
    ->registerSchema(OpenFGA\Models\Userset::schema())
    ->registerSchema(OpenFGA\Models\Collections\Usersets::schema())
    ->registerSchema(OpenFGA\Models\ObjectRelation::schema());

$model = DslTransformer::fromDsl($dsl, $validator);

$dslString = DslTransformer::toDsl($model);
```
