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
    FlexibleTestObject,
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
        $this->validator = new SchemaValidator;
        // Reset the SchemaRegistry between tests
        $reflection = new ReflectionClass(SchemaRegistry::class);
        $schemas = $reflection->getProperty('schemas');
        $schemas->setAccessible(true);
        $schemas->setValue(null, []);
    });

    test('validates required fields', function (): void {
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

    test('throws on missing required field', function (): void {
        $schema = (new SchemaBuilder(TestObject::class))
            ->string('name', required: true)
            ->integer('age', required: true)
            ->register();

        $this->validator->registerSchema($schema);

        $this->validator->validateAndTransform(['name' => 'John'], TestObject::class);
    })->throws(SerializationException::class);

    test('validates nested objects', function (): void {
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

    test('validates enum values - valid case', function (): void {
        $schema = (new SchemaBuilder(Status::class))
            ->string('status', required: true, enum: ['active', 'inactive', 'pending'])
            ->register();

        $this->validator->registerSchema($schema);

        $result = $this->validator->validateAndTransform(
            ['status' => 'active'],
            Status::class,
        );

        expect($result)
            ->toBeInstanceOf(Status::class)
            ->status->toBe('active');
    });

    test('validates enum values - invalid case', function (): void {
        $schema = (new SchemaBuilder(Status::class))
            ->string('status', required: true, enum: ['active', 'inactive', 'pending'])
            ->register();

        $this->validator->registerSchema($schema);

        $this->validator->validateAndTransform(
            ['status' => 'invalid'],
            Status::class,
        );
    })->throws(SerializationException::class);

    test('validates date format strings - valid case', function (): void {
        $schema = (new SchemaBuilder(Event::class))
            ->string('dateField', format: 'date')
            ->register();

        $this->validator->registerSchema($schema);

        $result = $this->validator->validateAndTransform(
            ['dateField' => '2023-01-01'],
            Event::class,
        );

        expect($result)
            ->toBeInstanceOf(Event::class)
            ->dateField->format('Y-m-d')->toBe('2023-01-01');
    });

    test('validates date format strings - invalid case', function (): void {
        $schema = (new SchemaBuilder(Event::class))
            ->string('dateField', format: 'date')
            ->register();

        $this->validator->registerSchema($schema);

        $this->validator->validateAndTransform(
            ['dateField' => 'not-a-date'],
            Event::class,
        );
    })->throws(SerializationException::class);

    test('accepts null in optional fields', function (array $input, ?string $expectedName, ?int $expectedAge): void {
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

    test('validates array items - valid cases', function (array $input, array $expectedNumbers): void {
        $schema = (new SchemaBuilder(TestArray::class))
            ->array('numbers', ['type' => 'number'], required: true)
            ->register();

        $this->validator->registerSchema($schema);

        $result = $this->validator->validateAndTransform($input, TestArray::class);

        expect($result)
            ->toBeInstanceOf(TestArray::class)
            ->numbers->toBe($expectedNumbers);
    })->with([
        'valid array of numbers' => [
            'input' => ['numbers' => [1, 2, 3.5]],
            'expectedNumbers' => [1, 2, 3.5],
        ],
        'empty array' => [
            'input' => ['numbers' => []],
            'expectedNumbers' => [],
        ],
    ]);

    test('validates array items - invalid case', function (): void {
        $schema = (new SchemaBuilder(TestArray::class))
            ->array('numbers', ['type' => 'number'], required: true)
            ->register();

        $this->validator->registerSchema($schema);

        $this->validator->validateAndTransform(['numbers' => [1, 'not-a-number', 3]], TestArray::class);
    })->throws(SerializationException::class);

    test('validates recursive object structures - valid cases', function (array $input, callable $assert): void {
        $treeNodeSchema = (new SchemaBuilder(TreeNode::class))
            ->string('value', required: true)
            ->array('children', [
                'type' => 'object',
                'className' => TreeNode::class,
            ])
            ->register();

        $this->validator->registerSchema($treeNodeSchema);

        $result = $this->validator->validateAndTransform($input, TreeNode::class);
        $assert($result);
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
        'empty children array' => [
            [
                'value' => 'root',
                'children' => [],
            ],
            static function ($result): void {
                expect($result)->toBeInstanceOf(TreeNode::class);
                expect($result->value)->toBe('root');
                expect($result->children)->toBe([]);
            },
        ],
    ]);

    test('validates recursive object structures - missing required field', function (): void {
        $treeNodeSchema = (new SchemaBuilder(TreeNode::class))
            ->string('value', required: true)
            ->array('children', [
                'type' => 'object',
                'className' => TreeNode::class,
            ])
            ->register();

        $this->validator->registerSchema($treeNodeSchema);

        $this->validator->validateAndTransform([
            'value' => 'root',
            'children' => [
                ['children' => []],
            ],
        ], TreeNode::class);
    })->throws(SerializationException::class);

    test('validates object arrays - valid cases', function (array $input, callable $assert): void {
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

        $result = $this->validator->validateAndTransform($input, ArrayContainer::class);
        $assert($result);
    })->with([
        'valid array of objects' => [
            [
                'items' => [
                    ['id' => 1, 'name' => 'Valid'],
                    ['id' => 2, 'name' => 'Also Valid'],
                ],
            ],
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
        'empty array' => [
            [
                'items' => [],
            ],
            static function ($result): void {
                expect($result)->toBeInstanceOf(ArrayContainer::class);
                expect($result->getItems())->toBe([]);
            },
        ],
    ]);

    test('validates object arrays - invalid array items', function (): void {
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

        $this->validator->validateAndTransform([
            'items' => [
                ['id' => 1, 'name' => 'Valid'],
                ['id' => 'not-an-integer', 'name' => 'Invalid'],
            ],
        ], ArrayContainer::class);
    })->throws(SerializationException::class);

    test('throws on invalid input type', function (mixed $input): void {
        $this->validator->validateAndTransform($input, 'SomeClass');
    })->with([
        'non-array input' => [
            'not-an-array',
        ],
        'unregistered class' => [
            [],
        ],
    ])->throws(SerializationException::class);

    test('validates nested validation errors - valid case', function (): void {
        $childSchema = (new SchemaBuilder(NestedChild::class))
            ->string('name', required: true)
            ->register();

        $parentSchema = (new SchemaBuilder(NestedParent::class))
            ->object('child', NestedChild::class, true)
            ->register();

        $this->validator->registerSchema($childSchema);
        $this->validator->registerSchema($parentSchema);

        $result = $this->validator->validateAndTransform(['child' => ['name' => 'Valid Name']], NestedParent::class);

        expect($result)->toBeInstanceOf(NestedParent::class);
        expect($result->child)->toBeInstanceOf(NestedChild::class);
        expect($result->child->name)->toBe('Valid Name');
    });

    test('validates nested validation errors - missing required field', function (): void {
        $childSchema = (new SchemaBuilder(NestedChild::class))
            ->string('name', required: true)
            ->register();

        $parentSchema = (new SchemaBuilder(NestedParent::class))
            ->object('child', NestedChild::class, true)
            ->register();

        $this->validator->registerSchema($childSchema);
        $this->validator->registerSchema($parentSchema);

        $this->validator->validateAndTransform(['child' => []], NestedParent::class);
    })->throws(SerializationException::class);

    test('converts integer types', function (): void {
        $schema = (new SchemaBuilder(TestObject::class))
            ->integer('age', required: true)
            ->string('name', required: true)
            ->register();

        $this->validator->registerSchema($schema);

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

        $result = $this->validator->validateAndTransform(['numbers' => [42, '42.5', 3.14]], TestArray::class);
        expect($result->numbers)->toHaveCount(3);
        expect($result->numbers[0])->toBe(42);
        expect($result->numbers[1])->toBe('42.5');
        expect($result->numbers[2])->toBe(3.14);
    });

    test('validates string property using status object', function (): void {
        $schema = (new SchemaBuilder(Status::class))
            ->string('status', required: true)
            ->register();

        $this->validator->registerSchema($schema);

        $result = $this->validator->validateAndTransform(['status' => 'active'], Status::class);
        expect($result->status)->toBe('active');
    });

    test('validates datetime format strings with edge cases - valid cases', function (string $input): void {
        $schema = (new SchemaBuilder(Event::class))
            ->string('dateField', format: 'datetime')
            ->register();

        $this->validator->registerSchema($schema);

        $result = $this->validator->validateAndTransform(['dateField' => $input], Event::class);
        expect($result)->toBeInstanceOf(Event::class);
        expect($result->dateField)->toBeInstanceOf(DateTimeImmutable::class);
    })->with([
        'valid ISO 8601' => ['2023-01-01T10:30:00Z'],
        'valid RFC 2822' => ['Sun, 01 Jan 2023 10:30:00 +0000'],
        'valid simple format' => ['2023-01-01 10:30:00'],
        'empty string' => [''],
    ]);

    test('validates datetime format strings with edge cases - invalid format', function (): void {
        $schema = (new SchemaBuilder(Event::class))
            ->string('dateField', format: 'datetime')
            ->register();

        $this->validator->registerSchema($schema);

        $this->validator->validateAndTransform(['dateField' => 'not-a-datetime'], Event::class);
    })->throws(SerializationException::class);

    test('validates nested object validation using existing test objects', function (): void {
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

    test('validates array items with different types using existing test classes - valid case', function (): void {
        $schema = (new SchemaBuilder(TestArray::class))
            ->array('numbers', ['type' => 'string'], required: true)
            ->register();

        $this->validator->registerSchema($schema);

        $result1 = $this->validator->validateAndTransform(['numbers' => ['a', 'b', 'c']], TestArray::class);
        expect($result1->numbers)->toBe(['a', 'b', 'c']);
    });

    test('validates array items with different types using existing test classes - invalid case', function (): void {
        $schema = (new SchemaBuilder(TestArray::class))
            ->array('numbers', ['type' => 'string'], required: true)
            ->register();

        $this->validator->registerSchema($schema);

        $this->validator->validateAndTransform(['numbers' => ['a', 1, true]], TestArray::class);
    })->throws(SerializationException::class);

    test('validates string field conversion from different types', function (): void {
        $schema = (new SchemaBuilder(TestObject::class))
            ->string('name')
            ->register();

        $this->validator->registerSchema($schema);

        $result = $this->validator->validateAndTransform(['name' => 'test'], TestObject::class);
        expect($result->name)->toBe('test');
    });

    test('handles integer validation edge cases using age field - valid cases', function (): void {
        $schema = (new SchemaBuilder(TestObject::class))
            ->integer('age')
            ->register();

        $this->validator->registerSchema($schema);

        $result1 = $this->validator->validateAndTransform(['age' => 42], TestObject::class);
        expect($result1)->toBeInstanceOf(TestObject::class);

        $result2 = $this->validator->validateAndTransform(['age' => '42'], TestObject::class);
        expect($result2)->toBeInstanceOf(TestObject::class);
    });

    test('handles integer validation edge cases using age field - invalid decimal', function (): void {
        $schema = (new SchemaBuilder(TestObject::class))
            ->integer('age')
            ->register();

        $this->validator->registerSchema($schema);

        $this->validator->validateAndTransform(['age' => '42.5'], TestObject::class);
    })->throws(SerializationException::class);

    test('handles integer validation edge cases using age field - invalid string', function (): void {
        $schema = (new SchemaBuilder(TestObject::class))
            ->integer('age')
            ->register();

        $this->validator->registerSchema($schema);

        $this->validator->validateAndTransform(['age' => 'not-a-number'], TestObject::class);
    })->throws(SerializationException::class);

    test('handles number validation in arrays - valid cases', function (): void {
        $schema = (new SchemaBuilder(TestArray::class))
            ->array('numbers', ['type' => 'number'])
            ->register();

        $this->validator->registerSchema($schema);

        $result1 = $this->validator->validateAndTransform(['numbers' => [42.5]], TestArray::class);
        expect($result1)->toBeInstanceOf(TestArray::class);

        $result2 = $this->validator->validateAndTransform(['numbers' => ['42.5']], TestArray::class);
        expect($result2)->toBeInstanceOf(TestArray::class);
    });

    test('handles number validation in arrays - invalid case', function (): void {
        $schema = (new SchemaBuilder(TestArray::class))
            ->array('numbers', ['type' => 'number'])
            ->register();

        $this->validator->registerSchema($schema);

        $this->validator->validateAndTransform(['numbers' => ['not-a-number']], TestArray::class);
    })->throws(SerializationException::class);

    test('enforces string type - valid case', function (): void {
        $schema = (new SchemaBuilder(TestObject::class))
            ->string('name', required: true)
            ->integer('age', required: true)
            ->register();

        $this->validator->registerSchema($schema);

        expect($this->validator->validateAndTransform(['name' => 'test', 'age' => 25], TestObject::class)->name)->toBe('test');
    });

    test('enforces string type - rejects integer', function (): void {
        $schema = (new SchemaBuilder(TestObject::class))
            ->string('name', required: true)
            ->integer('age', required: true)
            ->register();

        $this->validator->registerSchema($schema);

        $this->validator->validateAndTransform(['name' => 42, 'age' => 25], TestObject::class);
    })->throws(SerializationException::class);

    test('enforces string type - rejects float', function (): void {
        $schema = (new SchemaBuilder(TestObject::class))
            ->string('name', required: true)
            ->integer('age', required: true)
            ->register();

        $this->validator->registerSchema($schema);

        $this->validator->validateAndTransform(['name' => 42.5, 'age' => 25], TestObject::class);
    })->throws(SerializationException::class);

    test('enforces string type - rejects boolean', function (): void {
        $schema = (new SchemaBuilder(TestObject::class))
            ->string('name', required: true)
            ->integer('age', required: true)
            ->register();

        $this->validator->registerSchema($schema);

        $this->validator->validateAndTransform(['name' => true, 'age' => 25], TestObject::class);
    })->throws(SerializationException::class);

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

    describe('Error handling and edge cases', function (): void {
        test('handles collection data format error for indexed collections', function (): void {
            $schema = (new SchemaBuilder(TestArray::class))
                ->array('numbers', ['type' => 'number'])
                ->register();

            $this->validator->registerSchema($schema);

            // Simulate associative array that can't be converted to list for IndexedCollection
            $data = ['key1' => 1, 'key2' => 2]; // associative array
            $result = $this->validator->validateAndTransform(['numbers' => $data], TestArray::class);

            // Should convert associative to indexed array
            expect($result->numbers)->toBe([1, 2]);
        });

        test('handles nested object validation with non-array data', function (): void {
            $schema = (new SchemaBuilder(NestedParent::class))
                ->object('child', NestedChild::class, required: true)
                ->register();

            $childSchema = (new SchemaBuilder(NestedChild::class))
                ->string('name', required: true)
                ->register();

            $this->validator->registerSchema($schema);
            $this->validator->registerSchema($childSchema);

            // child is not an array
            $this->validator->validateAndTransform(['child' => 'not-an-object'], NestedParent::class);
        })->throws(SerializationException::class);

        test('handles array items validation with non-array items', function (): void {
            $schema = (new SchemaBuilder(Post::class))
                ->string('title', required: true)
                ->array('tags', ['type' => 'object', 'className' => Tag::class])
                ->register();

            $tagSchema = (new SchemaBuilder(Tag::class))
                ->string('name', required: true)
                ->register();

            $this->validator->registerSchema($schema);
            $this->validator->registerSchema($tagSchema);

            // array contains non-array items for object validation
            $this->validator->validateAndTransform([
                'title' => 'Test Post',
                'tags' => ['not-an-object'], // string instead of array
            ], Post::class);
        })->throws(SerializationException::class);

        test('handles datetime format edge cases', function (): void {
            $schema = (new SchemaBuilder(Event::class))
                ->string('dateTimeField', format: 'datetime')
                ->register();

            $this->validator->registerSchema($schema);

            // Test invalid datetime format that should return null
            expect(function (): void {
                $this->validator->validateAndTransform(['dateTimeField' => 'invalid-datetime'], Event::class);
            })->toThrow(SerializationException::class);
        });

        test('handles date format returning null on invalid input', function (): void {
            $schema = (new SchemaBuilder(Event::class))
                ->string('dateField', format: 'date')
                ->register();

            $this->validator->registerSchema($schema);

            // Test completely invalid date format
            expect(function (): void {
                $this->validator->validateAndTransform(['dateField' => 'completely-invalid'], Event::class);
            })->toThrow(SerializationException::class);
        });

        test('handles validation errors aggregation', function (): void {
            $schema = (new SchemaBuilder(TestObject::class))
                ->string('name', required: true)
                ->integer('age', required: true)
                ->register();

            $this->validator->registerSchema($schema);

            // Missing both required fields should aggregate errors
            expect(function (): void {
                $this->validator->validateAndTransform([], TestObject::class);
            })->toThrow(SerializationException::class);
        });

        test('handles array transformation for object type', function (): void {
            $schema = (new SchemaBuilder(NestedParent::class))
                ->object('child', NestedChild::class, required: true)
                ->register();

            $childSchema = (new SchemaBuilder(NestedChild::class))
                ->string('name', required: true)
                ->register();

            $this->validator->registerSchema($schema);
            $this->validator->registerSchema($childSchema);

            $result = $this->validator->validateAndTransform([
                'child' => ['name' => 'test'], // array to object transformation
            ], NestedParent::class);

            expect($result->child)->toBeInstanceOf(NestedChild::class);
            expect($result->child->name)->toBe('test');
        });

        test('handles null value transformation', function (): void {
            $schema = (new SchemaBuilder(TestObjectOptional::class))
                ->string('name', required: false)
                ->register();

            $this->validator->registerSchema($schema);

            $result = $this->validator->validateAndTransform(['name' => null], TestObjectOptional::class);
            expect($result->name)->toBeNull();
        });

        test('handles constructor parameter reflection', function (): void {
            $schema = (new SchemaBuilder(TestObject::class))
                ->string('name', required: true)
                ->integer('age', required: true)
                ->register();

            $this->validator->registerSchema($schema);

            $result = $this->validator->validateAndTransform(['name' => 'test', 'age' => 25], TestObject::class);
            expect($result)->toBeInstanceOf(TestObject::class);
            expect($result->name)->toBe('test');
            expect($result->age)->toBe(25);
        });

        test('handles non-array input for object validation', function (): void {
            $schema = (new SchemaBuilder(TestObject::class))
                ->string('name', required: true)
                ->register();

            $this->validator->registerSchema($schema);

            // Pass a non-array to validateAndTransform
            expect(function (): void {
                $this->validator->validateAndTransform('not-an-array', TestObject::class);
            })->toThrow(SerializationException::class);
        });

        test('handles unregistered schema class', function (): void {
            // Try to validate against a class that hasn't been registered
            expect(function (): void {
                $this->validator->validateAndTransform([], 'UnregisteredClass');
            })->toThrow(SerializationException::class);
        });

        test('handles integer string conversion', function (): void {
            $schema = (new SchemaBuilder(TestObject::class))
                ->integer('age', required: true)
                ->register();

            $this->validator->registerSchema($schema);

            // Test string to integer conversion
            $result = $this->validator->validateAndTransform(['age' => '25'], TestObject::class);
            expect($result->age)->toBe(25);
        });

        test('handles number type validation', function (): void {
            $schema = (new SchemaBuilder(TestObject::class))
                ->number('age') // number validates numeric values
                ->register();

            $this->validator->registerSchema($schema);

            // Test that the validation works
            $result = $this->validator->validateAndTransform(['age' => 42], TestObject::class);
            expect($result)->toBeInstanceOf(TestObject::class);
        });

        test('handles invalid number conversion', function (): void {
            $schema = (new SchemaBuilder(TestObject::class))
                ->number('age')
                ->register();

            $this->validator->registerSchema($schema);

            // Test non-numeric string (should fail)
            expect(function (): void {
                $this->validator->validateAndTransform(['age' => 'not-a-number'], TestObject::class);
            })->toThrow(SerializationException::class);
        });

        test('handles boolean field validation with string property', function (): void {
            $schema = (new SchemaBuilder(TestObject::class))
                ->boolean('name') // TestObject.name is string, so boolean gets converted
                ->register();

            $this->validator->registerSchema($schema);

            // Test boolean conversion to string
            $result = $this->validator->validateAndTransform(['name' => true], TestObject::class);
            expect($result->name)->toBe('1'); // true converts to '1' for string field

            $result2 = $this->validator->validateAndTransform(['name' => false], TestObject::class);
            expect($result2->name)->toBe(''); // false converts to '' for string field
        });

        test('handles string type strict validation', function (): void {
            $schema = (new SchemaBuilder(TestObject::class))
                ->string('name', required: true)
                ->register();

            $this->validator->registerSchema($schema);

            // Test that non-string values are rejected
            expect(function (): void {
                $this->validator->validateAndTransform(['name' => 123], TestObject::class);
            })->toThrow(SerializationException::class);

            expect(function (): void {
                $this->validator->validateAndTransform(['name' => true], TestObject::class);
            })->toThrow(SerializationException::class);

            expect(function (): void {
                $this->validator->validateAndTransform(['name' => []], TestObject::class);
            })->toThrow(SerializationException::class);
        });

        test('handles array validation with proper types', function (): void {
            $schema = (new SchemaBuilder(TestArray::class))
                ->array('numbers', ['type' => 'string'])
                ->register();

            $this->validator->registerSchema($schema);

            $result = $this->validator->validateAndTransform(['numbers' => ['a', 'b', 'c']], TestArray::class);
            expect($result->numbers)->toBe(['a', 'b', 'c']);
        });

        test('handles date time format edge cases', function (): void {
            $schema = (new SchemaBuilder(Event::class))
                ->datetime('dateTimeField')
                ->register();

            $this->validator->registerSchema($schema);

            // Test valid datetime formats
            $result1 = $this->validator->validateAndTransform(['dateTimeField' => '2023-01-01T10:00:00Z'], Event::class);
            expect($result1->dateTimeField)->toBeInstanceOf(DateTimeImmutable::class);

            // Test invalid datetime (should fail)
            expect(function (): void {
                $this->validator->validateAndTransform(['dateTimeField' => 'invalid-datetime'], Event::class);
            })->toThrow(SerializationException::class);
        });

        test('handles flexible type validation with mixed properties', function (): void {
            $schema = (new SchemaBuilder(FlexibleTestObject::class))
                ->number('value')
                ->string('data')
                ->register();

            $this->validator->registerSchema($schema);

            // Test various value types on mixed property
            $result1 = $this->validator->validateAndTransform(['value' => 42.5, 'data' => 'test'], FlexibleTestObject::class);
            expect($result1->value)->toBe(42.5);
            expect($result1->data)->toBe('test');

            $result2 = $this->validator->validateAndTransform(['value' => 123, 'data' => 'test'], FlexibleTestObject::class);
            expect($result2)->toBeInstanceOf(FlexibleTestObject::class);
        });

        test('handles object transformation edge cases', function (): void {
            $schema = (new SchemaBuilder(FlexibleTestObject::class))
                ->object('value', NestedChild::class)
                ->register();

            $childSchema = (new SchemaBuilder(NestedChild::class))
                ->string('name', required: true)
                ->register();

            $this->validator->registerSchema($schema);
            $this->validator->registerSchema($childSchema);

            // Test object transformation
            $result = $this->validator->validateAndTransform([
                'value' => ['name' => 'test-child'],
            ], FlexibleTestObject::class);

            expect($result->value)->toBeInstanceOf(NestedChild::class);
            expect($result->value->name)->toBe('test-child');
        });

        test('validates error context aggregation with multiple failures', function (): void {
            $schema = (new SchemaBuilder(TestObject::class))
                ->string('name', required: true)
                ->integer('age', required: true)
                ->register();

            $this->validator->registerSchema($schema);

            // Test multiple validation errors
            try {
                $this->validator->validateAndTransform(['name' => 123, 'age' => 'not-a-number'], TestObject::class);
                expect(false)->toBeTrue('Should have thrown exception');
            } catch (SerializationException $e) {
                expect($e->getMessage())->toContain('Invalid item type');
            }
        });
    });
});
