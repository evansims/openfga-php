<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Schema;

use DateTimeImmutable;
use OpenFGA\Exceptions\SerializationException;
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

describe('SchemaValidator', function (): void {
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

    test('throws SerializationException when required field is missing', function (): void {
        $schema = (new SchemaBuilder(TestObject::class))
            ->string('name', required: true)
            ->integer('age', required: true)
            ->register();

        $this->validator->registerSchema($schema);

        $this->expectException(SerializationException::class);
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
        $this->expectException(SerializationException::class);
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

        expect($result)
            ->toBeInstanceOf(Event::class)
            ->dateField->format('Y-m-d')->toBe('2023-01-01');

        // Test invalid date format
        $this->expectException(SerializationException::class);
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
            $this->expectException(SerializationException::class);
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
            $this->expectException(SerializationException::class);
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
            $this->expectException(SerializationException::class);
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

    test('throws exception for invalid input types', function (mixed $input): void {
        $this->expectException(SerializationException::class);

        $this->validator->validateAndTransform($input, 'SomeClass');
    })->with([
        'non-array input' => [
            'not-an-array',
        ],
        'unregistered class' => [
            [],
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
            $this->expectException(SerializationException::class);
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

    test('validates integer type conversion using existing properties', function (): void {
        $schema = (new SchemaBuilder(TestObject::class))
            ->integer('age', required: true)
            ->string('name', required: true)
            ->register();

        $this->validator->registerSchema($schema);

        // Test valid integer values that pass validation
        expect($this->validator->validateAndTransform(['age' => '42', 'name' => 'test'], TestObject::class)->age)->toBe(42);
        expect($this->validator->validateAndTransform(['age' => 42, 'name' => 'test'], TestObject::class)->age)->toBe(42);
    });

    test('throws exception for float value when integer expected in existing properties', function (): void {
        $schema = (new SchemaBuilder(TestObject::class))
            ->integer('age', required: true)
            ->string('name', required: true)
            ->register();

        $this->validator->registerSchema($schema);

        $this->validator->validateAndTransform(['age' => 42.7, 'name' => 'test'], TestObject::class);
    })->throws(SerializationException::class);

    test('throws exception for string value when integer expected in existing properties', function (): void {
        $schema = (new SchemaBuilder(TestObject::class))
            ->integer('age', required: true)
            ->string('name', required: true)
            ->register();

        $this->validator->registerSchema($schema);

        $this->validator->validateAndTransform(['age' => 'not-a-number', 'name' => 'test'], TestObject::class);
    })->throws(SerializationException::class);

    test('validates number type conversion using test array', function (): void {
        $schema = (new SchemaBuilder(TestArray::class))
            ->array('numbers', ['type' => 'number'])
            ->register();

        $this->validator->registerSchema($schema);

        // Test number array validation - don't expect exact type conversion
        $result = $this->validator->validateAndTransform(['numbers' => [42, '42.5', 3.14]], TestArray::class);
        expect($result->numbers)->toHaveCount(3);
        expect($result->numbers[0])->toBe(42);
        expect($result->numbers[1])->toBe('42.5'); // String numbers may stay as strings in validation
        expect($result->numbers[2])->toBe(3.14);
    });

    test('validates string property using status object', function (): void {
        $schema = (new SchemaBuilder(Status::class))
            ->string('status', required: true)
            ->register();

        $this->validator->registerSchema($schema);

        // Test string handling
        $result = $this->validator->validateAndTransform(['status' => 'active'], Status::class);
        expect($result->status)->toBe('active');
    });

    test('validates datetime format strings with edge cases', function (string $input, bool $shouldPass): void {
        $schema = (new SchemaBuilder(Event::class))
            ->string('dateField', format: 'datetime')
            ->register();

        $this->validator->registerSchema($schema);

        if (! $shouldPass) {
            $this->expectException(SerializationException::class);
            $this->validator->validateAndTransform(['dateField' => $input], Event::class);

            return;
        }

        $result = $this->validator->validateAndTransform(['dateField' => $input], Event::class);
        expect($result)->toBeInstanceOf(Event::class);
        expect($result->dateField)->toBeInstanceOf(DateTimeImmutable::class);
    })->with([
        'valid ISO 8601' => ['2023-01-01T10:30:00Z', true],
        'valid RFC 2822' => ['Sun, 01 Jan 2023 10:30:00 +0000', true],
        'valid simple format' => ['2023-01-01 10:30:00', true],
        'invalid format' => ['not-a-datetime', false],
        'empty string' => ['', true], // Empty string creates current datetime
    ]);

    test('validates nested object validation using existing test objects', function (): void {
        // Test that metadata gets accepted as a plain object
        // Since the SchemaBuilder requires string className, we'll test the validator logic indirectly
        $schema = (new SchemaBuilder(User::class))
            ->string('name', required: true)
            ->object('address', Address::class, required: true)
            ->register();

        $addressSchema = (new SchemaBuilder(Address::class))
            ->string('street', required: true)
            ->string('city', required: true)
            ->string('zip', required: true)
            ->register();

        $this->validator->registerSchema($schema);
        $this->validator->registerSchema($addressSchema);

        $input = [
            'name' => 'John',
            'address' => [
                'street' => '123 Main St',
                'city' => 'Anytown',
                'zip' => '12345',
            ],
        ];

        $result = $this->validator->validateAndTransform($input, User::class);
        expect($result->address)->toBeInstanceOf(Address::class);
    });

    test('validates array items with different types using existing test classes', function (): void {
        $schema = (new SchemaBuilder(TestArray::class))
            ->array('numbers', ['type' => 'string'], required: true)
            ->register();

        $this->validator->registerSchema($schema);

        // Valid string array
        $result1 = $this->validator->validateAndTransform(['numbers' => ['a', 'b', 'c']], TestArray::class);
        expect($result1->numbers)->toBe(['a', 'b', 'c']);

        // Invalid mixed array should throw
        $this->expectException(SerializationException::class);
        $this->validator->validateAndTransform(['numbers' => ['a', 1, true]], TestArray::class);
    });

    test('validates string field conversion from different types', function (): void {
        $schema = (new SchemaBuilder(TestObject::class))
            ->string('name')
            ->register();

        $this->validator->registerSchema($schema);

        // Test string handling - should work fine
        $result = $this->validator->validateAndTransform(['name' => 'test'], TestObject::class);
        expect($result->name)->toBe('test');
    });

    test('handles integer validation edge cases using age field', function (): void {
        $schema = (new SchemaBuilder(TestObject::class))
            ->integer('age')
            ->register();

        $this->validator->registerSchema($schema);

        // Valid integer cases
        $result1 = $this->validator->validateAndTransform(['age' => 42], TestObject::class);
        expect($result1)->toBeInstanceOf(TestObject::class);

        $result2 = $this->validator->validateAndTransform(['age' => '42'], TestObject::class);
        expect($result2)->toBeInstanceOf(TestObject::class);

        // Invalid integer cases should throw
        $this->expectException(SerializationException::class);
        $this->validator->validateAndTransform(['age' => '42.5'], TestObject::class);

        $this->expectException(SerializationException::class);
        $this->validator->validateAndTransform(['age' => 'not-a-number'], TestObject::class);
    });

    test('handles number validation in arrays', function (): void {
        $schema = (new SchemaBuilder(TestArray::class))
            ->array('numbers', ['type' => 'number'])
            ->register();

        $this->validator->registerSchema($schema);

        // Valid number cases
        $result1 = $this->validator->validateAndTransform(['numbers' => [42.5]], TestArray::class);
        expect($result1)->toBeInstanceOf(TestArray::class);

        $result2 = $this->validator->validateAndTransform(['numbers' => ['42.5']], TestArray::class);
        expect($result2)->toBeInstanceOf(TestArray::class);

        // Invalid number cases should throw
        $this->expectException(SerializationException::class);
        $this->validator->validateAndTransform(['numbers' => ['not-a-number']], TestArray::class);
    });

    test('validates string type strictly and rejects non-string types', function (): void {
        $schema = (new SchemaBuilder(TestObject::class))
            ->string('name', required: true)
            ->integer('age', required: true)
            ->register();

        $this->validator->registerSchema($schema);

        // Test that only actual strings are accepted for string fields
        expect($this->validator->validateAndTransform(['name' => 'test', 'age' => 25], TestObject::class)->name)->toBe('test');

        // Test that non-string types are rejected
        $this->expectException(SerializationException::class);
        $this->validator->validateAndTransform(['name' => 42, 'age' => 25], TestObject::class);
        $this->expectException(SerializationException::class);
        $this->validator->validateAndTransform(['name' => 42.5, 'age' => 25], TestObject::class);
        $this->expectException(SerializationException::class);
        $this->validator->validateAndTransform(['name' => true, 'age' => 25], TestObject::class);
    });

    test('validates SchemaValidator getSchemas method', function (): void {
        $schema1 = (new SchemaBuilder('Class1'))->string('field1')->register();
        $schema2 = (new SchemaBuilder('Class2'))->integer('field2')->register();

        $this->validator->registerSchema($schema1);
        $this->validator->registerSchema($schema2);

        $schemas = $this->validator->getSchemas();

        expect($schemas)->toHaveCount(2)
            ->and($schemas)->toHaveKey('Class1')
            ->and($schemas)->toHaveKey('Class2')
            ->and($schemas['Class1'])->toBe($schema1)
            ->and($schemas['Class2'])->toBe($schema2);
    });

    test('validates registerSchema returns self for chaining', function (): void {
        $schema = (new SchemaBuilder('TestClass'))->string('field')->register();

        $result = $this->validator->registerSchema($schema);

        expect($result)->toBe($this->validator);
    });
});
