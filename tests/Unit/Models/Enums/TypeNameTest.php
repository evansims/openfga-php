<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Models\Enums;

use OpenFGA\Models\Enums\TypeName;

use function count;

describe('TypeName enum', function (): void {
    describe('getPhpType()', function (): void {
        test('boolean type returns "bool"', function (): void {
            expect(TypeName::BOOL->getPhpType())->toBe('bool');
        });

        test('integer types return "int"', function (): void {
            expect(TypeName::INT->getPhpType())->toBe('int');
            expect(TypeName::UINT->getPhpType())->toBe('int');
        });

        test('double type returns "float"', function (): void {
            expect(TypeName::DOUBLE->getPhpType())->toBe('float');
        });

        test('string-based types return "string"', function (): void {
            expect(TypeName::STRING->getPhpType())->toBe('string');
            expect(TypeName::DURATION->getPhpType())->toBe('string');
            expect(TypeName::TIMESTAMP->getPhpType())->toBe('string');
            expect(TypeName::IPADDRESS->getPhpType())->toBe('string');
        });

        test('collection types return "array"', function (): void {
            expect(TypeName::LIST->getPhpType())->toBe('array');
            expect(TypeName::MAP->getPhpType())->toBe('array');
        });

        test('flexible types return "mixed"', function (): void {
            expect(TypeName::ANY->getPhpType())->toBe('mixed');
            expect(TypeName::UNSPECIFIED->getPhpType())->toBe('mixed');
        });

        test('all enum cases have valid PHP types', function (): void {
            $validPhpTypes = ['bool', 'int', 'float', 'string', 'array', 'mixed'];

            foreach (TypeName::cases() as $typeName) {
                expect($typeName->getPhpType())->toBeIn($validPhpTypes);
            }
        });
    });

    describe('isCollection()', function (): void {
        test('LIST and MAP are collections', function (): void {
            expect(TypeName::LIST->isCollection())->toBeTrue();
            expect(TypeName::MAP->isCollection())->toBeTrue();
        });

        test('primitive types are not collections', function (): void {
            expect(TypeName::BOOL->isCollection())->toBeFalse();
            expect(TypeName::INT->isCollection())->toBeFalse();
            expect(TypeName::UINT->isCollection())->toBeFalse();
            expect(TypeName::DOUBLE->isCollection())->toBeFalse();
            expect(TypeName::STRING->isCollection())->toBeFalse();
        });

        test('temporal types are not collections', function (): void {
            expect(TypeName::DURATION->isCollection())->toBeFalse();
            expect(TypeName::TIMESTAMP->isCollection())->toBeFalse();
        });

        test('special types are not collections', function (): void {
            expect(TypeName::IPADDRESS->isCollection())->toBeFalse();
            expect(TypeName::ANY->isCollection())->toBeFalse();
            expect(TypeName::UNSPECIFIED->isCollection())->toBeFalse();
        });

        test('collection status matches PHP type', function (): void {
            foreach (TypeName::cases() as $typeName) {
                $isCollection = $typeName->isCollection();
                $phpType = $typeName->getPhpType();

                if ($isCollection) {
                    expect($phpType)->toBe('array');
                }
            }
        });
    });

    describe('isFlexible()', function (): void {
        test('ANY and UNSPECIFIED are flexible', function (): void {
            expect(TypeName::ANY->isFlexible())->toBeTrue();
            expect(TypeName::UNSPECIFIED->isFlexible())->toBeTrue();
        });

        test('strongly typed values are not flexible', function (): void {
            expect(TypeName::BOOL->isFlexible())->toBeFalse();
            expect(TypeName::INT->isFlexible())->toBeFalse();
            expect(TypeName::UINT->isFlexible())->toBeFalse();
            expect(TypeName::DOUBLE->isFlexible())->toBeFalse();
            expect(TypeName::STRING->isFlexible())->toBeFalse();
            expect(TypeName::LIST->isFlexible())->toBeFalse();
            expect(TypeName::MAP->isFlexible())->toBeFalse();
            expect(TypeName::DURATION->isFlexible())->toBeFalse();
            expect(TypeName::TIMESTAMP->isFlexible())->toBeFalse();
            expect(TypeName::IPADDRESS->isFlexible())->toBeFalse();
        });

        test('flexible types have mixed PHP type', function (): void {
            foreach (TypeName::cases() as $typeName) {
                if ($typeName->isFlexible()) {
                    expect($typeName->getPhpType())->toBe('mixed');
                }
            }
        });
    });

    describe('isNumeric()', function (): void {
        test('integer and double types are numeric', function (): void {
            expect(TypeName::INT->isNumeric())->toBeTrue();
            expect(TypeName::UINT->isNumeric())->toBeTrue();
            expect(TypeName::DOUBLE->isNumeric())->toBeTrue();
        });

        test('non-numeric types return false', function (): void {
            expect(TypeName::BOOL->isNumeric())->toBeFalse();
            expect(TypeName::STRING->isNumeric())->toBeFalse();
            expect(TypeName::LIST->isNumeric())->toBeFalse();
            expect(TypeName::MAP->isNumeric())->toBeFalse();
            expect(TypeName::DURATION->isNumeric())->toBeFalse();
            expect(TypeName::TIMESTAMP->isNumeric())->toBeFalse();
            expect(TypeName::IPADDRESS->isNumeric())->toBeFalse();
            expect(TypeName::ANY->isNumeric())->toBeFalse();
            expect(TypeName::UNSPECIFIED->isNumeric())->toBeFalse();
        });

        test('numeric types have numeric PHP types', function (): void {
            $numericPhpTypes = ['int', 'float'];

            foreach (TypeName::cases() as $typeName) {
                if ($typeName->isNumeric()) {
                    expect($typeName->getPhpType())->toBeIn($numericPhpTypes);
                }
            }
        });
    });

    describe('isTemporal()', function (): void {
        test('DURATION and TIMESTAMP are temporal', function (): void {
            expect(TypeName::DURATION->isTemporal())->toBeTrue();
            expect(TypeName::TIMESTAMP->isTemporal())->toBeTrue();
        });

        test('non-temporal types return false', function (): void {
            expect(TypeName::BOOL->isTemporal())->toBeFalse();
            expect(TypeName::INT->isTemporal())->toBeFalse();
            expect(TypeName::UINT->isTemporal())->toBeFalse();
            expect(TypeName::DOUBLE->isTemporal())->toBeFalse();
            expect(TypeName::STRING->isTemporal())->toBeFalse();
            expect(TypeName::LIST->isTemporal())->toBeFalse();
            expect(TypeName::MAP->isTemporal())->toBeFalse();
            expect(TypeName::IPADDRESS->isTemporal())->toBeFalse();
            expect(TypeName::ANY->isTemporal())->toBeFalse();
            expect(TypeName::UNSPECIFIED->isTemporal())->toBeFalse();
        });

        test('temporal types have string PHP type', function (): void {
            foreach (TypeName::cases() as $typeName) {
                if ($typeName->isTemporal()) {
                    expect($typeName->getPhpType())->toBe('string');
                }
            }
        });
    });

    describe('type categorization consistency', function (): void {
        test('no type is both collection and numeric', function (): void {
            foreach (TypeName::cases() as $typeName) {
                if ($typeName->isCollection()) {
                    expect($typeName->isNumeric())->toBeFalse();
                }
                if ($typeName->isNumeric()) {
                    expect($typeName->isCollection())->toBeFalse();
                }
            }
        });

        test('no type is both numeric and temporal', function (): void {
            foreach (TypeName::cases() as $typeName) {
                if ($typeName->isNumeric()) {
                    expect($typeName->isTemporal())->toBeFalse();
                }
                if ($typeName->isTemporal()) {
                    expect($typeName->isNumeric())->toBeFalse();
                }
            }
        });

        test('no type is both collection and temporal', function (): void {
            foreach (TypeName::cases() as $typeName) {
                if ($typeName->isCollection()) {
                    expect($typeName->isTemporal())->toBeFalse();
                }
                if ($typeName->isTemporal()) {
                    expect($typeName->isCollection())->toBeFalse();
                }
            }
        });

        test('flexible types are not categorized as specific types', function (): void {
            foreach (TypeName::cases() as $typeName) {
                if ($typeName->isFlexible()) {
                    expect($typeName->isNumeric())->toBeFalse();
                    expect($typeName->isCollection())->toBeFalse();
                    expect($typeName->isTemporal())->toBeFalse();
                }
            }
        });
    });

    describe('enum completeness and values', function (): void {
        test('all expected type name cases exist', function (): void {
            $expectedTypes = [
                'TYPE_NAME_ANY',
                'TYPE_NAME_BOOL',
                'TYPE_NAME_DOUBLE',
                'TYPE_NAME_DURATION',
                'TYPE_NAME_INT',
                'TYPE_NAME_IPADDRESS',
                'TYPE_NAME_LIST',
                'TYPE_NAME_MAP',
                'TYPE_NAME_STRING',
                'TYPE_NAME_TIMESTAMP',
                'TYPE_NAME_UINT',
                'TYPE_NAME_UNSPECIFIED',
            ];

            $actualValues = array_map(fn (TypeName $type) => $type->value, TypeName::cases());

            foreach ($expectedTypes as $expectedType) {
                expect($actualValues)->toContain($expectedType);
            }

            expect(TypeName::cases())->toHaveCount(count($expectedTypes));
        });

        test('string values match expected OpenFGA type names', function (): void {
            expect(TypeName::ANY->value)->toBe('TYPE_NAME_ANY');
            expect(TypeName::BOOL->value)->toBe('TYPE_NAME_BOOL');
            expect(TypeName::DOUBLE->value)->toBe('TYPE_NAME_DOUBLE');
            expect(TypeName::DURATION->value)->toBe('TYPE_NAME_DURATION');
            expect(TypeName::INT->value)->toBe('TYPE_NAME_INT');
            expect(TypeName::IPADDRESS->value)->toBe('TYPE_NAME_IPADDRESS');
            expect(TypeName::LIST->value)->toBe('TYPE_NAME_LIST');
            expect(TypeName::MAP->value)->toBe('TYPE_NAME_MAP');
            expect(TypeName::STRING->value)->toBe('TYPE_NAME_STRING');
            expect(TypeName::TIMESTAMP->value)->toBe('TYPE_NAME_TIMESTAMP');
            expect(TypeName::UINT->value)->toBe('TYPE_NAME_UINT');
            expect(TypeName::UNSPECIFIED->value)->toBe('TYPE_NAME_UNSPECIFIED');
        });

        test('types can be created from string values', function (): void {
            expect(TypeName::from('TYPE_NAME_ANY'))->toBe(TypeName::ANY);
            expect(TypeName::from('TYPE_NAME_BOOL'))->toBe(TypeName::BOOL);
            expect(TypeName::from('TYPE_NAME_STRING'))->toBe(TypeName::STRING);
            expect(TypeName::from('TYPE_NAME_LIST'))->toBe(TypeName::LIST);
            expect(TypeName::from('TYPE_NAME_MAP'))->toBe(TypeName::MAP);
        });
    });

    describe('practical usage patterns', function (): void {
        test('can determine appropriate validation strategy', function (): void {
            foreach (TypeName::cases() as $typeName) {
                // Flexible types need runtime checking
                if ($typeName->isFlexible()) {
                    expect($typeName->getPhpType())->toBe('mixed');
                }

                // Collections need iteration logic
                if ($typeName->isCollection()) {
                    expect($typeName->getPhpType())->toBe('array');
                }

                // Numeric types need numeric validation
                if ($typeName->isNumeric()) {
                    expect($typeName->getPhpType())->toBeIn(['int', 'float']);
                }

                // Temporal types need time parsing
                if ($typeName->isTemporal()) {
                    expect($typeName->getPhpType())->toBe('string');
                }
            }
        });

        test('type classification is exhaustive', function (): void {
            // Every type should be classified as at least one category or flexible
            foreach (TypeName::cases() as $typeName) {
                $isClassified = $typeName->isFlexible()
                              || $typeName->isNumeric()
                              || $typeName->isCollection()
                              || $typeName->isTemporal()
                              || TypeName::BOOL === $typeName
                              || TypeName::STRING === $typeName
                              || TypeName::IPADDRESS === $typeName;

                expect($isClassified)->toBeTrue("Type {$typeName->value} should be classified");
            }
        });
    });
});
