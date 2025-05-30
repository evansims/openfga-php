<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Models\Enums;

use OpenFGA\Models\Enums\TupleOperation;

use function count;

describe('TupleOperation enum', function (): void {
    describe('enum values', function (): void {
        test('TUPLE_OPERATION_WRITE has correct string value', function (): void {
            expect(TupleOperation::TUPLE_OPERATION_WRITE->value)->toBe('TUPLE_OPERATION_WRITE');
        });

        test('TUPLE_OPERATION_DELETE has correct string value', function (): void {
            expect(TupleOperation::TUPLE_OPERATION_DELETE->value)->toBe('TUPLE_OPERATION_DELETE');
        });

        test('all expected enum cases exist', function (): void {
            $expectedValues = [
                'TUPLE_OPERATION_WRITE',
                'TUPLE_OPERATION_DELETE',
            ];

            $actualValues = array_map(fn (TupleOperation $operation) => $operation->value, TupleOperation::cases());

            foreach ($expectedValues as $expectedValue) {
                expect($actualValues)->toContain($expectedValue);
            }

            expect(TupleOperation::cases())->toHaveCount(count($expectedValues));
        });

        test('operations can be created from string values', function (): void {
            expect(TupleOperation::from('TUPLE_OPERATION_WRITE'))->toBe(TupleOperation::TUPLE_OPERATION_WRITE);
            expect(TupleOperation::from('TUPLE_OPERATION_DELETE'))->toBe(TupleOperation::TUPLE_OPERATION_DELETE);
        });
    });

    describe('getDescription()', function (): void {
        test('TUPLE_OPERATION_WRITE returns descriptive text', function (): void {
            $description = TupleOperation::TUPLE_OPERATION_WRITE->getDescription();

            expect($description)->toBeString();
            expect($description)->not->toBeEmpty();
        });

        test('TUPLE_OPERATION_DELETE returns descriptive text', function (): void {
            $description = TupleOperation::TUPLE_OPERATION_DELETE->getDescription();

            expect($description)->toBeString();
            expect($description)->not->toBeEmpty();
        });

        test('different operations have different descriptions', function (): void {
            $writeDescription = TupleOperation::TUPLE_OPERATION_WRITE->getDescription();
            $deleteDescription = TupleOperation::TUPLE_OPERATION_DELETE->getDescription();

            expect($writeDescription)->not->toBe($deleteDescription);
        });

        test('all enum cases have non-empty descriptions', function (): void {
            foreach (TupleOperation::cases() as $operation) {
                $description = $operation->getDescription();
                expect($description)->toBeString();
                expect($description)->not->toBeEmpty();
            }
        });
    });

    describe('grantsPermissions()', function (): void {
        test('TUPLE_OPERATION_WRITE grants permissions', function (): void {
            expect(TupleOperation::TUPLE_OPERATION_WRITE->grantsPermissions())->toBeTrue();
        });

        test('TUPLE_OPERATION_DELETE does not grant permissions', function (): void {
            expect(TupleOperation::TUPLE_OPERATION_DELETE->grantsPermissions())->toBeFalse();
        });

        test('operations are mutually exclusive for granting permissions', function (): void {
            $grantingOperations = array_filter(
                TupleOperation::cases(),
                fn (TupleOperation $operation) => $operation->grantsPermissions(),
            );

            $nonGrantingOperations = array_filter(
                TupleOperation::cases(),
                fn (TupleOperation $operation) => ! $operation->grantsPermissions(),
            );

            expect($grantingOperations)->toContain(TupleOperation::TUPLE_OPERATION_WRITE);
            expect($nonGrantingOperations)->toContain(TupleOperation::TUPLE_OPERATION_DELETE);

            // Ensure all operations are classified
            expect(count($grantingOperations) + count($nonGrantingOperations))->toBe(count(TupleOperation::cases()));
        });
    });

    describe('revokesPermissions()', function (): void {
        test('TUPLE_OPERATION_DELETE revokes permissions', function (): void {
            expect(TupleOperation::TUPLE_OPERATION_DELETE->revokesPermissions())->toBeTrue();
        });

        test('TUPLE_OPERATION_WRITE does not revoke permissions', function (): void {
            expect(TupleOperation::TUPLE_OPERATION_WRITE->revokesPermissions())->toBeFalse();
        });

        test('operations are mutually exclusive for revoking permissions', function (): void {
            $revokingOperations = array_filter(
                TupleOperation::cases(),
                fn (TupleOperation $operation) => $operation->revokesPermissions(),
            );

            $nonRevokingOperations = array_filter(
                TupleOperation::cases(),
                fn (TupleOperation $operation) => ! $operation->revokesPermissions(),
            );

            expect($revokingOperations)->toContain(TupleOperation::TUPLE_OPERATION_DELETE);
            expect($nonRevokingOperations)->toContain(TupleOperation::TUPLE_OPERATION_WRITE);

            // Ensure all operations are classified
            expect(count($revokingOperations) + count($nonRevokingOperations))->toBe(count(TupleOperation::cases()));
        });
    });

    describe('isIdempotent()', function (): void {
        test('TUPLE_OPERATION_WRITE is idempotent', function (): void {
            expect(TupleOperation::TUPLE_OPERATION_WRITE->isIdempotent())->toBeTrue();
        });

        test('TUPLE_OPERATION_DELETE is idempotent', function (): void {
            expect(TupleOperation::TUPLE_OPERATION_DELETE->isIdempotent())->toBeTrue();
        });

        test('all operations are idempotent', function (): void {
            foreach (TupleOperation::cases() as $operation) {
                expect($operation->isIdempotent())->toBeTrue();
            }
        });
    });

    describe('operation classification consistency', function (): void {
        test('operations are either granting or revoking but not both', function (): void {
            foreach (TupleOperation::cases() as $operation) {
                $grants = $operation->grantsPermissions();
                $revokes = $operation->revokesPermissions();

                // Each operation should either grant or revoke, but not both or neither
                expect($grants xor $revokes)->toBeTrue(
                    "Operation {$operation->value} should either grant OR revoke permissions, not both or neither",
                );
            }
        });

        test('WRITE and DELETE operations have opposite effects', function (): void {
            $write = TupleOperation::TUPLE_OPERATION_WRITE;
            $delete = TupleOperation::TUPLE_OPERATION_DELETE;

            // Write grants what delete revokes
            expect($write->grantsPermissions())->toBe(! $delete->grantsPermissions());
            expect($write->revokesPermissions())->toBe(! $delete->revokesPermissions());

            // Both should be idempotent
            expect($write->isIdempotent())->toBe($delete->isIdempotent());
        });

        test('every operation is categorized correctly', function (): void {
            foreach (TupleOperation::cases() as $operation) {
                match ($operation) {
                    TupleOperation::TUPLE_OPERATION_WRITE => [
                        expect($operation->grantsPermissions())->toBeTrue(),
                        expect($operation->revokesPermissions())->toBeFalse(),
                        expect($operation->isIdempotent())->toBeTrue(),
                    ],
                    TupleOperation::TUPLE_OPERATION_DELETE => [
                        expect($operation->grantsPermissions())->toBeFalse(),
                        expect($operation->revokesPermissions())->toBeTrue(),
                        expect($operation->isIdempotent())->toBeTrue(),
                    ],
                };
            }
        });
    });

    describe('practical usage scenarios', function (): void {
        test('can identify permission-granting operations', function (): void {
            $grantingOperations = array_filter(
                TupleOperation::cases(),
                fn (TupleOperation $operation) => $operation->grantsPermissions(),
            );

            expect($grantingOperations)->toContain(TupleOperation::TUPLE_OPERATION_WRITE);
            expect($grantingOperations)->not->toContain(TupleOperation::TUPLE_OPERATION_DELETE);
        });

        test('can identify permission-revoking operations', function (): void {
            $revokingOperations = array_filter(
                TupleOperation::cases(),
                fn (TupleOperation $operation) => $operation->revokesPermissions(),
            );

            expect($revokingOperations)->toContain(TupleOperation::TUPLE_OPERATION_DELETE);
            expect($revokingOperations)->not->toContain(TupleOperation::TUPLE_OPERATION_WRITE);
        });

        test('can identify safe-to-retry operations', function (): void {
            $safeOperations = array_filter(
                TupleOperation::cases(),
                fn (TupleOperation $operation) => $operation->isIdempotent(),
            );

            // All operations should be safe to retry
            expect($safeOperations)->toHaveCount(count(TupleOperation::cases()));
            expect($safeOperations)->toContain(TupleOperation::TUPLE_OPERATION_WRITE);
            expect($safeOperations)->toContain(TupleOperation::TUPLE_OPERATION_DELETE);
        });

        test('can determine operation impact on authorization state', function (): void {
            foreach (TupleOperation::cases() as $operation) {
                if ($operation->grantsPermissions()) {
                    // Granting operations increase access
                    expect($operation->revokesPermissions())->toBeFalse();
                    expect($operation)->toBe(TupleOperation::TUPLE_OPERATION_WRITE);
                }

                if ($operation->revokesPermissions()) {
                    // Revoking operations decrease access
                    expect($operation->grantsPermissions())->toBeFalse();
                    expect($operation)->toBe(TupleOperation::TUPLE_OPERATION_DELETE);
                }
            }
        });

        test('supports audit and logging requirements', function (): void {
            foreach (TupleOperation::cases() as $operation) {
                // All operations should provide meaningful descriptions for audit logs
                $description = $operation->getDescription();
                expect($description)->toBeString();
                expect($description)->not->toBeEmpty();

                // All operations should have clear impact categorization
                $isGrant = $operation->grantsPermissions();
                $isRevoke = $operation->revokesPermissions();
                expect($isGrant || $isRevoke)->toBeTrue();
                expect($isGrant && $isRevoke)->toBeFalse();
            }
        });

        test('handles batch operation planning', function (): void {
            $writeOp = TupleOperation::TUPLE_OPERATION_WRITE;
            $deleteOp = TupleOperation::TUPLE_OPERATION_DELETE;

            // Both operations can be safely batched (both idempotent)
            expect($writeOp->isIdempotent())->toBeTrue();
            expect($deleteOp->isIdempotent())->toBeTrue();

            // Can categorize operations for batch optimization
            $operations = [$writeOp, $deleteOp, $writeOp];
            $grantCount = count(array_filter($operations, fn ($op) => $op->grantsPermissions()));
            $revokeCount = count(array_filter($operations, fn ($op) => $op->revokesPermissions()));

            expect($grantCount)->toBe(2);
            expect($revokeCount)->toBe(1);
        });
    });

    describe('enum completeness and edge cases', function (): void {
        test('enum has exactly two operations', function (): void {
            expect(TupleOperation::cases())->toHaveCount(2);
        });

        test('enum values follow OpenFGA naming convention', function (): void {
            foreach (TupleOperation::cases() as $operation) {
                expect($operation->value)->toStartWith('TUPLE_OPERATION_');
                expect($operation->value)->toBeIn([
                    'TUPLE_OPERATION_WRITE',
                    'TUPLE_OPERATION_DELETE',
                ]);
            }
        });

        test('string representation matches enum value', function (): void {
            expect(TupleOperation::TUPLE_OPERATION_WRITE->value)->toBe('TUPLE_OPERATION_WRITE');
            expect(TupleOperation::TUPLE_OPERATION_DELETE->value)->toBe('TUPLE_OPERATION_DELETE');
        });

        test('all methods return appropriate types', function (): void {
            foreach (TupleOperation::cases() as $operation) {
                expect($operation->getDescription())->toBeString();
                expect($operation->grantsPermissions())->toBeBool();
                expect($operation->revokesPermissions())->toBeBool();
                expect($operation->isIdempotent())->toBeBool();
            }
        });

        test('enum covers all expected tuple operations', function (): void {
            // Based on OpenFGA documentation, there should be write and delete operations
            $operationValues = array_map(fn ($op) => $op->value, TupleOperation::cases());

            expect($operationValues)->toContain('TUPLE_OPERATION_WRITE');
            expect($operationValues)->toContain('TUPLE_OPERATION_DELETE');

            // Should not contain unexpected operations
            foreach ($operationValues as $value) {
                expect($value)->toBeIn([
                    'TUPLE_OPERATION_WRITE',
                    'TUPLE_OPERATION_DELETE',
                ]);
            }
        });
    });
});
