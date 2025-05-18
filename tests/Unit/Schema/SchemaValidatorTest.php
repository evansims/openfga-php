<?php

declare(strict_types=1);

use OpenFGA\Exceptions\SchemaValidationException;
use OpenFGA\Schema\{SchemaBuilder, SchemaRegistry, SchemaValidator};
use OpenFGA\Tests\Support\Schema\{
    Address,
    ArrayContainer,
    ArrayItem,
    Event,
    NestedChild,
    NestedParent,
    Post,
    Status,
    Tag,
    TestArray,
    TestObject,
    TestObjectOptional,
    TreeNode,
    User,
};
use ReflectionClass;

beforeEach(function (): void {
    $this->validator = new SchemaValidator();
    // Reset the SchemaRegistry between tests by clearing the static $schemas array
    $reflection = new ReflectionClass(SchemaRegistry::class);
    $schemas = $reflection->getProperty('schemas');
    $schemas->setAccessible(true);
    $schemas->setValue(null, []);
});

test('validates simple object with required fields', function (): void {
    $schema = (new SchemaBuilder(TestObject::class))
        ->string('name', required: true)
        ->integer('age', required: true)
        ->register();

    $this->validator->registerSchema($schema);

    $validData = ['name' => 'John', 'age' => 30];
    $result = $this->validator->validateAndTransform($validData, TestObject::class);

    expect($result)
        ->toBeInstanceOf(TestObject::class)
        ->name->toBe('John')
        ->age->toBe(30);
});

test('throws SchemaValidationException when required field is missing', function (): void {
    $schema = (new SchemaBuilder(TestObject::class))
        ->string('name', required: true)
        ->integer('age', required: true)
        ->register();

    $this->validator->registerSchema($schema);

    $this->expectException(SchemaValidationException::class);
    $this->validator->validateAndTransform(['name' => 'John'], TestObject::class);
});

test('validates nested object structures', function (): void {
    $addressSchema = (new SchemaBuilder(Address::class))
        ->string('street', required: true)
        ->string('city', required: true)
        ->string('zip', required: true)
        ->register();

    $userSchema = (new SchemaBuilder(User::class))
        ->string('name', required: true)
        ->object('address', Address::class, required: true)
        ->register();

    $this->validator->registerSchema($addressSchema);
    $this->validator->registerSchema($userSchema);

    $validData = [
        'name' => 'John',
        'address' => [
            'street' => '123 Main St',
            'city' => 'Anytown',
            'zip' => '12345',
        ],
    ];

    $result = $this->validator->validateAndTransform($validData, User::class);

    expect($result)
        ->toBeInstanceOf(User::class)
        ->name->toBe('John')
        ->address->toBeInstanceOf(Address::class)
        ->address->street->toBe('123 Main St')
        ->address->city->toBe('Anytown')
        ->address->zip->toBe('12345');
});

test('validates arrays of objects', function (): void {
    $tagSchema = (new SchemaBuilder(Tag::class))
        ->string('name', required: true)
        ->string('color')
        ->register();

    $postSchema = (new SchemaBuilder(Post::class))
        ->string('title', required: true)
        ->array('tags', ['type' => 'object', 'className' => Tag::class])
        ->register();

    $this->validator->registerSchema($tagSchema);
    $this->validator->registerSchema($postSchema);

    $validData = [
        'title' => 'My First Post',
        'tags' => [
            ['name' => 'php', 'color' => 'blue'],
            ['name' => 'testing', 'color' => 'green'],
        ],
    ];

    $result = $this->validator->validateAndTransform($validData, Post::class);

    expect($result)->toBeInstanceOf(Post::class);
    expect($result->title)->toBe('My First Post');
    expect($result->tags)->toHaveCount(2);
    expect($result->tags[0])->toBeInstanceOf(Tag::class);
    expect($result->tags[0]->name)->toBe('php');
    expect($result->tags[0]->color)->toBe('blue');
    expect($result->tags[1])->toBeInstanceOf(Tag::class);
    expect($result->tags[1]->name)->toBe('testing');
    expect($result->tags[1]->color)->toBe('green');
});

test('validates enum values', function (): void {
    $schema = (new SchemaBuilder(Status::class))
        ->string('status', required: true, enum: ['active', 'inactive', 'pending'])
        ->register();

    $this->validator->registerSchema($schema);

    // Test valid enum value
    $result = $this->validator->validateAndTransform(
        ['status' => 'active'],
        Status::class,
    );

    expect($result)
        ->toBeInstanceOf(Status::class)
        ->status->toBe('active');

    // Test invalid enum value
    $this->expectException(SchemaValidationException::class);
    $this->validator->validateAndTransform(
        ['status' => 'invalid'],
        Status::class,
    );
});

test('validates date format strings', function (): void {
    $schema = (new SchemaBuilder(Event::class))
        ->string('dateField', format: 'date')
        ->register();

    $this->validator->registerSchema($schema);

    // Test valid date format
    $result = $this->validator->validateAndTransform(
        ['dateField' => '2023-01-01'],
        Event::class,
    );

    $expectedDate = new DateTimeImmutable('2023-01-01');
    expect($result)
        ->toBeInstanceOf(Event::class)
        ->dateField->format('Y-m-d')->toBe('2023-01-01');

    // Test invalid date format
    $this->expectException(SchemaValidationException::class);
    $this->validator->validateAndTransform(
        ['dateField' => 'not-a-date'],
        Event::class,
    );
});

test('handles null values for optional fields', function (array $input, ?string $expectedName, ?int $expectedAge): void {
    $schema = (new SchemaBuilder(TestObjectOptional::class))
        ->string('name', required: false)
        ->integer('age', required: false)
        ->register();

    $this->validator->registerSchema($schema);

    $result = $this->validator->validateAndTransform($input, TestObjectOptional::class);

    expect($result)
        ->toBeInstanceOf(TestObjectOptional::class)
        ->name->toBe($expectedName)
        ->age->toBe($expectedAge);
})->with([
    'all fields null' => [
        'input' => ['name' => null, 'age' => null],
        'expectedName' => null,
        'expectedAge' => null,
    ],
    'some fields null' => [
        'input' => ['name' => 'John', 'age' => null],
        'expectedName' => 'John',
        'expectedAge' => null,
    ],
    'all fields present' => [
        'input' => ['name' => 'John', 'age' => 30],
        'expectedName' => 'John',
        'expectedAge' => 30,
    ],
]);

test('validates array items', function (array $input, bool $shouldPass, ?array $expectedNumbers = null): void {
    $schema = (new SchemaBuilder(TestArray::class))
        ->array('numbers', ['type' => 'number'], required: true)
        ->register();

    $this->validator->registerSchema($schema);

    if (! $shouldPass) {
        $this->expectException(SchemaValidationException::class);
    }

    $result = $this->validator->validateAndTransform($input, TestArray::class);

    if ($shouldPass) {
        expect($result)
            ->toBeInstanceOf(TestArray::class)
            ->numbers->toBe($expectedNumbers);
    }
})->with([
    'valid array of numbers' => [
        'input' => ['numbers' => [1, 2, 3.5]],
        'shouldPass' => true,
        'expectedNumbers' => [1, 2, 3.5],
    ],
    'invalid array items' => [
        'input' => ['numbers' => [1, 'not-a-number', 3]],
        'shouldPass' => false,
    ],
    'empty array' => [
        'input' => ['numbers' => []],
        'shouldPass' => true,
        'expectedNumbers' => [],
    ],
]);

test('validates recursive object structures', function (array $input, bool $shouldPass, ?callable $assert = null): void {
    $treeNodeSchema = (new SchemaBuilder(TreeNode::class))
        ->string('value', required: true)
        ->array('children', [
            'type' => 'object',
            'className' => TreeNode::class,
        ])
        ->register();

    $this->validator->registerSchema($treeNodeSchema);

    if (! $shouldPass) {
        $this->expectException(SchemaValidationException::class);
    }

    $result = $this->validator->validateAndTransform($input, TreeNode::class);

    if ($shouldPass && $assert) {
        $assert($result);
    }
})->with([
    'valid recursive structure' => [
        [
            'value' => 'root',
            'children' => [
                ['value' => 'child1', 'children' => []],
                [
                    'value' => 'child2',
                    'children' => [
                        ['value' => 'grandchild', 'children' => []],
                    ],
                ],
            ],
        ],
        true,
        static function ($result): void {
            expect($result)->toBeInstanceOf(TreeNode::class);
            expect($result->value)->toBe('root');
            expect($result->children)->toHaveCount(2);
            expect($result->children[0])->toBeInstanceOf(TreeNode::class);
            expect($result->children[0]->value)->toBe('child1');
            expect($result->children[1])->toBeInstanceOf(TreeNode::class);
            expect($result->children[1]->value)->toBe('child2');
            expect($result->children[1]->children[0])->toBeInstanceOf(TreeNode::class);
            expect($result->children[1]->children[0]->value)->toBe('grandchild');
        },
    ],
    'missing required field in nested object' => [
        [
            'value' => 'root',
            'children' => [
                ['children' => []], // Missing 'value' field
            ],
        ],
        false,
        null,
    ],
    'empty children array' => [
        [
            'value' => 'root',
            'children' => [],
        ],
        true,
        static function ($result): void {
            expect($result)->toBeInstanceOf(TreeNode::class);
            expect($result->value)->toBe('root');
            expect($result->children)->toBe([]);
        },
    ],
]);

test('validates arrays with complex object items', function (array $input, bool $shouldPass, ?callable $assert = null): void {
    $itemSchema = (new SchemaBuilder(ArrayItem::class))
        ->integer('id', required: true)
        ->string('name', required: true)
        ->register();

    $containerSchema = (new SchemaBuilder(ArrayContainer::class))
        ->array('items', [
            'type' => 'object',
            'className' => ArrayItem::class,
        ], required: true)
        ->register();

    $this->validator->registerSchema($itemSchema);
    $this->validator->registerSchema($containerSchema);

    if (! $shouldPass) {
        $this->expectException(SchemaValidationException::class);
    }

    $result = $this->validator->validateAndTransform($input, ArrayContainer::class);

    if ($shouldPass && $assert) {
        $assert($result);
    }
})->with([
    'valid array of objects' => [
        [
            'items' => [
                ['id' => 1, 'name' => 'Valid'],
                ['id' => 2, 'name' => 'Also Valid'],
            ],
        ],
        true,
        static function ($result): void {
            expect($result)->toBeInstanceOf(ArrayContainer::class);
            $items = $result->getItems();
            expect($items)->toHaveCount(2);
            expect($items[0])->toBeInstanceOf(ArrayItem::class);
            expect($items[0]->id)->toBe(1);
            expect($items[0]->name)->toBe('Valid');
            expect($items[1])->toBeInstanceOf(ArrayItem::class);
            expect($items[1]->id)->toBe(2);
            expect($items[1]->name)->toBe('Also Valid');
        },
    ],
    'invalid array items' => [
        [
            'items' => [
                ['id' => 1, 'name' => 'Valid'],
                ['id' => 'not-an-integer', 'name' => 'Invalid'],
            ],
        ],
        false,
        null,
    ],
    'empty array' => [
        [
            'items' => [],
        ],
        true,
        static function ($result): void {
            expect($result)->toBeInstanceOf(ArrayContainer::class);
            expect($result->getItems())->toBe([]);
        },
    ],
]);

test('throws exception for invalid input types', function (mixed $input, string $expectedException, string $expectedMessage): void {
    $this->expectException($expectedException);
    $this->expectExceptionMessage($expectedMessage);

    $this->validator->validateAndTransform($input, 'SomeClass');
})->with([
    'non-array input' => [
        'not-an-array',
        InvalidArgumentException::class,
        'Data must be an array',
    ],
    'unregistered class' => [
        [],
        InvalidArgumentException::class,
        'No schema registered for class: SomeClass',
    ],
]);

test('handles nested object validation with errors', function (array $input, bool $shouldPass, ?callable $assert = null): void {
    $childSchema = (new SchemaBuilder(NestedChild::class))
        ->string('name', required: true)
        ->register();

    $parentSchema = (new SchemaBuilder(NestedParent::class))
        ->object('child', NestedChild::class, true)
        ->register();

    $this->validator->registerSchema($childSchema);
    $this->validator->registerSchema($parentSchema);

    if (! $shouldPass) {
        $this->expectException(SchemaValidationException::class);
    }

    $result = $this->validator->validateAndTransform($input, NestedParent::class);

    if ($shouldPass && $assert) {
        $assert($result);
    }
})->with([
    'valid nested object' => [
        ['child' => ['name' => 'Valid Name']],
        true,
        static function ($result): void {
            expect($result)->toBeInstanceOf(NestedParent::class);
            expect($result->child)->toBeInstanceOf(NestedChild::class);
            expect($result->child->name)->toBe('Valid Name');
        },
    ],
    'missing required field in nested object' => [
        ['child' => []], // Missing 'name' field
        false,
        null,
    ],
]);
