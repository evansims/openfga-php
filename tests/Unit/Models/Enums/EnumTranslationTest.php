<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Models\Enums;

use OpenFGA\Models\Enums\{Consistency, TupleOperation};
use OpenFGA\Translation\Translator;

describe('Enum Translation', function (): void {
    beforeEach(function (): void {
        // Reset translator state before each test
        Translator::reset();
    });

    afterEach(function (): void {
        // Reset to default locale after each test
        Translator::setDefaultLocale('en');
    });

    describe('Consistency enum', function (): void {
        test('getDescription() returns translated strings in English', function (): void {
            Translator::setDefaultLocale('en');

            $higherConsistency = Consistency::HIGHER_CONSISTENCY;
            $minimizeLatency = Consistency::MINIMIZE_LATENCY;
            $unspecified = Consistency::UNSPECIFIED;

            expect($higherConsistency->getDescription())->toBe(
                'Prioritizes data consistency over query performance, ensuring the most up-to-date results',
            );
            expect($minimizeLatency->getDescription())->toBe(
                'Prioritizes query performance over data consistency, potentially using slightly stale data',
            );
            expect($unspecified->getDescription())->toBe(
                'Uses the default consistency level determined by the OpenFGA server configuration',
            );
        });

        test('getDescription() returns translated strings in Spanish', function (): void {
            Translator::setDefaultLocale('es');

            $higherConsistency = Consistency::HIGHER_CONSISTENCY;
            $minimizeLatency = Consistency::MINIMIZE_LATENCY;
            $unspecified = Consistency::UNSPECIFIED;

            expect($higherConsistency->getDescription())->toBe(
                'Prioriza la consistencia de datos sobre el rendimiento de consultas, asegurando los resultados más actualizados',
            );
            expect($minimizeLatency->getDescription())->toBe(
                'Prioriza el rendimiento de consultas sobre la consistencia de datos, potencialmente usando datos ligeramente obsoletos',
            );
            expect($unspecified->getDescription())->toBe(
                'Usa el nivel de consistencia predeterminado determinado por la configuración del servidor OpenFGA',
            );
        });

        test('switching locales changes translation output', function (): void {
            // Test English first
            Translator::setDefaultLocale('en');
            $englishDescription = Consistency::HIGHER_CONSISTENCY->getDescription();
            expect($englishDescription)->toContain('Prioritizes data consistency');

            // Switch to Spanish
            Translator::setDefaultLocale('es');
            $spanishDescription = Consistency::HIGHER_CONSISTENCY->getDescription();
            expect($spanishDescription)->toContain('Prioriza la consistencia de datos');

            // Verify they are different
            expect($englishDescription)->not()->toBe($spanishDescription);
        });
    });

    describe('TupleOperation enum', function (): void {
        test('getDescription() returns translated strings in English', function (): void {
            Translator::setDefaultLocale('en');

            $write = TupleOperation::TUPLE_OPERATION_WRITE;
            $delete = TupleOperation::TUPLE_OPERATION_DELETE;

            expect($write->getDescription())->toBe(
                'Adds a new relationship tuple, granting permissions or establishing relationships',
            );
            expect($delete->getDescription())->toBe(
                'Removes an existing relationship tuple, revoking permissions or removing relationships',
            );
        });

        test('getDescription() returns translated strings in Spanish', function (): void {
            Translator::setDefaultLocale('es');

            $write = TupleOperation::TUPLE_OPERATION_WRITE;
            $delete = TupleOperation::TUPLE_OPERATION_DELETE;

            expect($write->getDescription())->toBe(
                'Agrega una nueva tupla de relación, otorgando permisos o estableciendo relaciones',
            );
            expect($delete->getDescription())->toBe(
                'Elimina una tupla de relación existente, revocando permisos o eliminando relaciones',
            );
        });

        test('switching locales changes translation output', function (): void {
            // Test English first
            Translator::setDefaultLocale('en');
            $englishDescription = TupleOperation::TUPLE_OPERATION_WRITE->getDescription();
            expect($englishDescription)->toContain('Adds a new relationship tuple');

            // Switch to Spanish
            Translator::setDefaultLocale('es');
            $spanishDescription = TupleOperation::TUPLE_OPERATION_WRITE->getDescription();
            expect($spanishDescription)->toContain('Agrega una nueva tupla de relación');

            // Verify they are different
            expect($englishDescription)->not()->toBe($spanishDescription);
        });
    });

    describe('Cross-enum translation consistency', function (): void {
        test('all enum descriptions use translation system correctly', function (): void {
            // Test that all enum cases return non-empty strings in both locales
            $consistencyValues = [
                Consistency::HIGHER_CONSISTENCY,
                Consistency::MINIMIZE_LATENCY,
                Consistency::UNSPECIFIED,
            ];

            $tupleOperationValues = [
                TupleOperation::TUPLE_OPERATION_WRITE,
                TupleOperation::TUPLE_OPERATION_DELETE,
            ];

            foreach (['en', 'es'] as $locale) {
                Translator::setDefaultLocale($locale);

                foreach ($consistencyValues as $consistency) {
                    $description = $consistency->getDescription();
                    expect($description)->toBeString();
                    expect($description)->not()->toBeEmpty();
                    expect($description)->not()->toBe($consistency->value); // Should not return raw enum value
                }

                foreach ($tupleOperationValues as $operation) {
                    $description = $operation->getDescription();
                    expect($description)->toBeString();
                    expect($description)->not()->toBeEmpty();
                    expect($description)->not()->toBe($operation->value); // Should not return raw enum value
                }
            }
        });

        test('descriptions are different between English and Spanish', function (): void {
            // Test that each enum case has different descriptions in different locales
            $testCases = [
                Consistency::HIGHER_CONSISTENCY,
                Consistency::MINIMIZE_LATENCY,
                Consistency::UNSPECIFIED,
                TupleOperation::TUPLE_OPERATION_WRITE,
                TupleOperation::TUPLE_OPERATION_DELETE,
            ];

            foreach ($testCases as $enumCase) {
                Translator::setDefaultLocale('en');
                $englishDescription = $enumCase->getDescription();

                Translator::setDefaultLocale('es');
                $spanishDescription = $enumCase->getDescription();

                expect($englishDescription)->not()->toBe($spanishDescription);
                expect($englishDescription)->toBeString();
                expect($spanishDescription)->toBeString();
                expect($englishDescription)->not()->toBeEmpty();
                expect($spanishDescription)->not()->toBeEmpty();
            }
        });

        test('locale reset works correctly', function (): void {
            // Set to Spanish and get a description
            Translator::setDefaultLocale('es');
            $spanishDescription = Consistency::HIGHER_CONSISTENCY->getDescription();

            // Reset and verify we get English again
            Translator::setDefaultLocale('en');
            $englishDescription = Consistency::HIGHER_CONSISTENCY->getDescription();

            expect($spanishDescription)->toContain('Prioriza la consistencia');
            expect($englishDescription)->toContain('Prioritizes data consistency');
            expect($englishDescription)->not()->toBe($spanishDescription);
        });
    });
});
