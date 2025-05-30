<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Models\Enums;

use OpenFGA\Models\Enums\Consistency;

use function count;

describe('Consistency enum', function (): void {
    describe('enum values', function (): void {
        test('HIGHER_CONSISTENCY has correct string value', function (): void {
            expect(Consistency::HIGHER_CONSISTENCY->value)->toBe('HIGHER_CONSISTENCY');
        });

        test('MINIMIZE_LATENCY has correct string value', function (): void {
            expect(Consistency::MINIMIZE_LATENCY->value)->toBe('MINIMIZE_LATENCY');
        });

        test('UNSPECIFIED has correct string value', function (): void {
            expect(Consistency::UNSPECIFIED->value)->toBe('UNSPECIFIED');
        });

        test('all expected enum cases exist', function (): void {
            $expectedValues = [
                'HIGHER_CONSISTENCY',
                'MINIMIZE_LATENCY',
                'UNSPECIFIED',
            ];

            $actualValues = array_map(fn (Consistency $consistency) => $consistency->value, Consistency::cases());

            foreach ($expectedValues as $expectedValue) {
                expect($actualValues)->toContain($expectedValue);
            }

            expect(Consistency::cases())->toHaveCount(count($expectedValues));
        });

        test('consistency levels can be created from string values', function (): void {
            expect(Consistency::from('HIGHER_CONSISTENCY'))->toBe(Consistency::HIGHER_CONSISTENCY);
            expect(Consistency::from('MINIMIZE_LATENCY'))->toBe(Consistency::MINIMIZE_LATENCY);
            expect(Consistency::from('UNSPECIFIED'))->toBe(Consistency::UNSPECIFIED);
        });
    });

    describe('getDescription()', function (): void {
        test('HIGHER_CONSISTENCY returns descriptive text', function (): void {
            $description = Consistency::HIGHER_CONSISTENCY->getDescription();

            expect($description)->toBeString();
            expect($description)->not->toBeEmpty();
        });

        test('MINIMIZE_LATENCY returns descriptive text', function (): void {
            $description = Consistency::MINIMIZE_LATENCY->getDescription();

            expect($description)->toBeString();
            expect($description)->not->toBeEmpty();
        });

        test('UNSPECIFIED returns descriptive text', function (): void {
            $description = Consistency::UNSPECIFIED->getDescription();

            expect($description)->toBeString();
            expect($description)->not->toBeEmpty();
        });

        test('different consistency levels have different descriptions', function (): void {
            $higherDescription = Consistency::HIGHER_CONSISTENCY->getDescription();
            $latencyDescription = Consistency::MINIMIZE_LATENCY->getDescription();
            $unspecifiedDescription = Consistency::UNSPECIFIED->getDescription();

            expect($higherDescription)->not->toBe($latencyDescription);
            expect($higherDescription)->not->toBe($unspecifiedDescription);
            expect($latencyDescription)->not->toBe($unspecifiedDescription);
        });

        test('all enum cases have non-empty descriptions', function (): void {
            foreach (Consistency::cases() as $consistency) {
                $description = $consistency->getDescription();
                expect($description)->toBeString();
                expect($description)->not->toBeEmpty();
            }
        });
    });

    describe('prioritizesConsistency()', function (): void {
        test('HIGHER_CONSISTENCY prioritizes consistency', function (): void {
            expect(Consistency::HIGHER_CONSISTENCY->prioritizesConsistency())->toBeTrue();
        });

        test('MINIMIZE_LATENCY does not prioritize consistency', function (): void {
            expect(Consistency::MINIMIZE_LATENCY->prioritizesConsistency())->toBeFalse();
        });

        test('UNSPECIFIED does not prioritize consistency', function (): void {
            expect(Consistency::UNSPECIFIED->prioritizesConsistency())->toBeFalse();
        });

        test('only one consistency level prioritizes consistency', function (): void {
            $consistencyPrioritizers = array_filter(
                Consistency::cases(),
                fn (Consistency $consistency) => $consistency->prioritizesConsistency(),
            );

            expect($consistencyPrioritizers)->toHaveCount(1);
            expect($consistencyPrioritizers)->toContain(Consistency::HIGHER_CONSISTENCY);
        });
    });

    describe('prioritizesPerformance()', function (): void {
        test('MINIMIZE_LATENCY prioritizes performance', function (): void {
            expect(Consistency::MINIMIZE_LATENCY->prioritizesPerformance())->toBeTrue();
        });

        test('HIGHER_CONSISTENCY does not prioritize performance', function (): void {
            expect(Consistency::HIGHER_CONSISTENCY->prioritizesPerformance())->toBeFalse();
        });

        test('UNSPECIFIED does not prioritize performance', function (): void {
            expect(Consistency::UNSPECIFIED->prioritizesPerformance())->toBeFalse();
        });

        test('only one consistency level prioritizes performance', function (): void {
            $performancePrioritizers = array_filter(
                Consistency::cases(),
                fn (Consistency $consistency) => $consistency->prioritizesPerformance(),
            );

            expect($performancePrioritizers)->toHaveCount(1);
            expect($performancePrioritizers)->toContain(Consistency::MINIMIZE_LATENCY);
        });
    });

    describe('consistency and performance priority relationship', function (): void {
        test('HIGHER_CONSISTENCY and MINIMIZE_LATENCY are opposites', function (): void {
            $higher = Consistency::HIGHER_CONSISTENCY;
            $latency = Consistency::MINIMIZE_LATENCY;

            expect($higher->prioritizesConsistency())->toBe(! $latency->prioritizesConsistency());
            expect($higher->prioritizesPerformance())->toBe(! $latency->prioritizesPerformance());
        });

        test('UNSPECIFIED is neutral on both dimensions', function (): void {
            $unspecified = Consistency::UNSPECIFIED;

            expect($unspecified->prioritizesConsistency())->toBeFalse();
            expect($unspecified->prioritizesPerformance())->toBeFalse();
        });

        test('consistency levels are mutually exclusive in priorities', function (): void {
            foreach (Consistency::cases() as $consistency) {
                $prioritizesConsistency = $consistency->prioritizesConsistency();
                $prioritizesPerformance = $consistency->prioritizesPerformance();

                // Should not prioritize both consistency and performance
                expect($prioritizesConsistency && $prioritizesPerformance)->toBeFalse(
                    "Consistency level {$consistency->value} should not prioritize both consistency and performance",
                );
            }
        });

        test('each consistency level has a clear categorization', function (): void {
            foreach (Consistency::cases() as $consistency) {
                match ($consistency) {
                    Consistency::HIGHER_CONSISTENCY => [
                        expect($consistency->prioritizesConsistency())->toBeTrue(),
                        expect($consistency->prioritizesPerformance())->toBeFalse(),
                    ],
                    Consistency::MINIMIZE_LATENCY => [
                        expect($consistency->prioritizesConsistency())->toBeFalse(),
                        expect($consistency->prioritizesPerformance())->toBeTrue(),
                    ],
                    Consistency::UNSPECIFIED => [
                        expect($consistency->prioritizesConsistency())->toBeFalse(),
                        expect($consistency->prioritizesPerformance())->toBeFalse(),
                    ],
                };
            }
        });
    });

    describe('practical usage scenarios', function (): void {
        test('can identify consistency-focused queries', function (): void {
            $consistencyFocused = array_filter(
                Consistency::cases(),
                fn (Consistency $consistency) => $consistency->prioritizesConsistency(),
            );

            expect($consistencyFocused)->toContain(Consistency::HIGHER_CONSISTENCY);
            expect($consistencyFocused)->not->toContain(Consistency::MINIMIZE_LATENCY);
            expect($consistencyFocused)->not->toContain(Consistency::UNSPECIFIED);
        });

        test('can identify performance-focused queries', function (): void {
            $performanceFocused = array_filter(
                Consistency::cases(),
                fn (Consistency $consistency) => $consistency->prioritizesPerformance(),
            );

            expect($performanceFocused)->toContain(Consistency::MINIMIZE_LATENCY);
            expect($performanceFocused)->not->toContain(Consistency::HIGHER_CONSISTENCY);
            expect($performanceFocused)->not->toContain(Consistency::UNSPECIFIED);
        });

        test('can identify neutral consistency levels', function (): void {
            $neutral = array_filter(
                Consistency::cases(),
                fn (Consistency $consistency) => ! $consistency->prioritizesConsistency() && ! $consistency->prioritizesPerformance(),
            );

            expect($neutral)->toContain(Consistency::UNSPECIFIED);
            expect($neutral)->not->toContain(Consistency::HIGHER_CONSISTENCY);
            expect($neutral)->not->toContain(Consistency::MINIMIZE_LATENCY);
        });

        test('supports query optimization decisions', function (): void {
            foreach (Consistency::cases() as $consistency) {
                if ($consistency->prioritizesConsistency()) {
                    // This query may have higher latency but fresher data
                    expect($consistency)->toBe(Consistency::HIGHER_CONSISTENCY);
                } elseif ($consistency->prioritizesPerformance()) {
                    // This query should be faster but may use stale data
                    expect($consistency)->toBe(Consistency::MINIMIZE_LATENCY);
                } else {
                    // This query uses server defaults
                    expect($consistency)->toBe(Consistency::UNSPECIFIED);
                }
            }
        });

        test('helps with service level agreement planning', function (): void {
            // Critical operations might need higher consistency
            $criticalOperationConsistency = Consistency::HIGHER_CONSISTENCY;
            expect($criticalOperationConsistency->prioritizesConsistency())->toBeTrue();

            // Dashboard queries might prioritize performance
            $dashboardConsistency = Consistency::MINIMIZE_LATENCY;
            expect($dashboardConsistency->prioritizesPerformance())->toBeTrue();

            // General operations might use defaults
            $generalConsistency = Consistency::UNSPECIFIED;
            expect($generalConsistency->prioritizesConsistency())->toBeFalse();
            expect($generalConsistency->prioritizesPerformance())->toBeFalse();
        });

        test('supports configuration and monitoring', function (): void {
            foreach (Consistency::cases() as $consistency) {
                // All consistency levels should provide meaningful descriptions for configuration
                $description = $consistency->getDescription();
                expect($description)->toBeString();
                expect($description)->not->toBeEmpty();

                // All consistency levels should have clear behavioral characteristics
                $hasExplicitPriority = $consistency->prioritizesConsistency() || $consistency->prioritizesPerformance();
                $isNeutral = ! $consistency->prioritizesConsistency() && ! $consistency->prioritizesPerformance();

                expect($hasExplicitPriority || $isNeutral)->toBeTrue();
            }
        });
    });

    describe('enum completeness and edge cases', function (): void {
        test('enum has exactly three consistency levels', function (): void {
            expect(Consistency::cases())->toHaveCount(3);
        });

        test('enum values follow OpenFGA naming convention', function (): void {
            foreach (Consistency::cases() as $consistency) {
                expect($consistency->value)->toBeIn([
                    'HIGHER_CONSISTENCY',
                    'MINIMIZE_LATENCY',
                    'UNSPECIFIED',
                ]);
            }
        });

        test('string representation matches enum value', function (): void {
            expect(Consistency::HIGHER_CONSISTENCY->value)->toBe('HIGHER_CONSISTENCY');
            expect(Consistency::MINIMIZE_LATENCY->value)->toBe('MINIMIZE_LATENCY');
            expect(Consistency::UNSPECIFIED->value)->toBe('UNSPECIFIED');
        });

        test('all methods return appropriate types', function (): void {
            foreach (Consistency::cases() as $consistency) {
                expect($consistency->getDescription())->toBeString();
                expect($consistency->prioritizesConsistency())->toBeBool();
                expect($consistency->prioritizesPerformance())->toBeBool();
            }
        });

        test('enum covers expected consistency trade-offs', function (): void {
            $consistencyValues = array_map(fn ($c) => $c->value, Consistency::cases());

            // Should include explicit consistency prioritization
            expect($consistencyValues)->toContain('HIGHER_CONSISTENCY');

            // Should include explicit performance prioritization
            expect($consistencyValues)->toContain('MINIMIZE_LATENCY');

            // Should include neutral/default option
            expect($consistencyValues)->toContain('UNSPECIFIED');

            // Should not contain unexpected values
            foreach ($consistencyValues as $value) {
                expect($value)->toBeIn([
                    'HIGHER_CONSISTENCY',
                    'MINIMIZE_LATENCY',
                    'UNSPECIFIED',
                ]);
            }
        });

        test('priority distribution is balanced', function (): void {
            $consistencyPrioritizers = array_filter(
                Consistency::cases(),
                fn (Consistency $c) => $c->prioritizesConsistency(),
            );

            $performancePrioritizers = array_filter(
                Consistency::cases(),
                fn (Consistency $c) => $c->prioritizesPerformance(),
            );

            $neutral = array_filter(
                Consistency::cases(),
                fn (Consistency $c) => ! $c->prioritizesConsistency() && ! $c->prioritizesPerformance(),
            );

            // Should have one of each type
            expect($consistencyPrioritizers)->toHaveCount(1);
            expect($performancePrioritizers)->toHaveCount(1);
            expect($neutral)->toHaveCount(1);

            // Should cover all cases
            expect(
                count($consistencyPrioritizers) + count($performancePrioritizers) + count($neutral),
            )->toBe(count(Consistency::cases()));
        });
    });

    describe('consistency level selection guidance', function (): void {
        test('provides clear decision matrix for consistency selection', function (): void {
            // When data freshness is critical
            $whenDataFreshnessMatters = Consistency::HIGHER_CONSISTENCY;
            expect($whenDataFreshnessMatters->prioritizesConsistency())->toBeTrue();

            // When query speed is critical
            $whenSpeedMatters = Consistency::MINIMIZE_LATENCY;
            expect($whenSpeedMatters->prioritizesPerformance())->toBeTrue();

            // When using server defaults
            $whenUsingDefaults = Consistency::UNSPECIFIED;
            expect($whenUsingDefaults->prioritizesConsistency())->toBeFalse();
            expect($whenUsingDefaults->prioritizesPerformance())->toBeFalse();
        });

        test('supports consistency level comparison', function (): void {
            $levels = Consistency::cases();
            $hasDifferentProfiles = false;

            foreach ($levels as $level1) {
                foreach ($levels as $level2) {
                    if ($level1 === $level2) {
                        continue;
                    }

                    // Different levels should have different priority profiles
                    $same = (
                        $level1->prioritizesConsistency() === $level2->prioritizesConsistency()
                        && $level1->prioritizesPerformance() === $level2->prioritizesPerformance()
                    );

                    if ($same) {
                        // Only UNSPECIFIED should match with itself in both priorities being false
                        expect($level1)->toBe(Consistency::UNSPECIFIED);
                        expect($level2)->toBe(Consistency::UNSPECIFIED);
                    } else {
                        $hasDifferentProfiles = true;
                    }
                }
            }

            // Ensure we found at least some different profiles to validate test logic
            expect($hasDifferentProfiles)->toBeTrue();
        });
    });
});
