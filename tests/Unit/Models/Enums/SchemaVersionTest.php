<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Models\Enums;

use OpenFGA\Models\Enums\SchemaVersion;

describe('SchemaVersion enum', function (): void {
    describe('compareTo()', function (): void {
        test('compares V1_0 with V1_1 returns -1', function (): void {
            $v10 = SchemaVersion::V1_0;
            $v11 = SchemaVersion::V1_1;

            expect($v10->compareTo($v11))->toBe(-1);
        });

        test('compares V1_1 with V1_0 returns 1', function (): void {
            $v10 = SchemaVersion::V1_0;
            $v11 = SchemaVersion::V1_1;

            expect($v11->compareTo($v10))->toBe(1);
        });

        test('compares same versions returns 0', function (): void {
            $v10a = SchemaVersion::V1_0;
            $v10b = SchemaVersion::V1_0;
            $v11a = SchemaVersion::V1_1;
            $v11b = SchemaVersion::V1_1;

            expect($v10a->compareTo($v10b))->toBe(0);
            expect($v11a->compareTo($v11b))->toBe(0);
        });

        test('comparison is consistent and transitive', function (): void {
            $v10 = SchemaVersion::V1_0;
            $v11 = SchemaVersion::V1_1;

            // Test symmetry: if a < b, then b > a
            expect($v10->compareTo($v11))->toBe(-1);
            expect($v11->compareTo($v10))->toBe(1);

            // Test reflexivity: a == a
            expect($v10->compareTo($v10))->toBe(0);
            expect($v11->compareTo($v11))->toBe(0);
        });
    });

    describe('getNumericVersion()', function (): void {
        test('V1_0 returns 1.0 as float', function (): void {
            $v10 = SchemaVersion::V1_0;

            expect($v10->getNumericVersion())->toBe(1.0);
            expect($v10->getNumericVersion())->toBeFloat();
        });

        test('V1_1 returns 1.1 as float', function (): void {
            $v11 = SchemaVersion::V1_1;

            expect($v11->getNumericVersion())->toBe(1.1);
            expect($v11->getNumericVersion())->toBeFloat();
        });

        test('numeric versions are correctly ordered', function (): void {
            $v10 = SchemaVersion::V1_0;
            $v11 = SchemaVersion::V1_1;

            expect($v10->getNumericVersion())->toBeLessThan($v11->getNumericVersion());
        });
    });

    describe('isLatest()', function (): void {
        test('V1_1 is the latest version', function (): void {
            $v11 = SchemaVersion::V1_1;

            expect($v11->isLatest())->toBeTrue();
        });

        test('V1_0 is not the latest version', function (): void {
            $v10 = SchemaVersion::V1_0;

            expect($v10->isLatest())->toBeFalse();
        });

        test('only one version can be latest', function (): void {
            $allVersions = [SchemaVersion::V1_0, SchemaVersion::V1_1];
            $latestCount = 0;

            foreach ($allVersions as $version) {
                if ($version->isLatest()) {
                    $latestCount++;
                }
            }

            expect($latestCount)->toBe(1);
        });
    });

    describe('isLegacy()', function (): void {
        test('V1_0 is a legacy version', function (): void {
            $v10 = SchemaVersion::V1_0;

            expect($v10->isLegacy())->toBeTrue();
        });

        test('V1_1 is not a legacy version', function (): void {
            $v11 = SchemaVersion::V1_1;

            expect($v11->isLegacy())->toBeFalse();
        });

        test('latest version is never legacy', function (): void {
            $allVersions = [SchemaVersion::V1_0, SchemaVersion::V1_1];

            foreach ($allVersions as $version) {
                if ($version->isLatest()) {
                    expect($version->isLegacy())->toBeFalse();
                }
            }
        });
    });

    describe('supportsConditions()', function (): void {
        test('V1_1 supports conditions', function (): void {
            $v11 = SchemaVersion::V1_1;

            expect($v11->supportsConditions())->toBeTrue();
        });

        test('V1_0 does not support conditions', function (): void {
            $v10 = SchemaVersion::V1_0;

            expect($v10->supportsConditions())->toBeFalse();
        });

        test('latest version supports conditions', function (): void {
            $allVersions = [SchemaVersion::V1_0, SchemaVersion::V1_1];

            foreach ($allVersions as $version) {
                if ($version->isLatest()) {
                    expect($version->supportsConditions())->toBeTrue();
                }
            }
        });
    });

    describe('version relationships and feature matrix', function (): void {
        test('feature availability is consistent with version ordering', function (): void {
            $v10 = SchemaVersion::V1_0;
            $v11 = SchemaVersion::V1_1;

            // Newer versions should have more or equal features
            expect($v11->supportsConditions())->toBeTrue();
            expect($v10->supportsConditions())->toBeFalse();

            // Latest version should have most features
            $latestVersion = $v11->isLatest() ? $v11 : $v10;
            expect($latestVersion->supportsConditions())->toBeTrue();
        });

        test('all enum cases are covered', function (): void {
            $allVersions = SchemaVersion::cases();

            expect($allVersions)->toHaveCount(2);
            expect($allVersions)->toContain(SchemaVersion::V1_0);
            expect($allVersions)->toContain(SchemaVersion::V1_1);
        });

        test('string values are correctly set', function (): void {
            expect(SchemaVersion::V1_0->value)->toBe('1.0');
            expect(SchemaVersion::V1_1->value)->toBe('1.1');
        });

        test('versions can be created from string values', function (): void {
            expect(SchemaVersion::from('1.0'))->toBe(SchemaVersion::V1_0);
            expect(SchemaVersion::from('1.1'))->toBe(SchemaVersion::V1_1);
        });

        test('legacy detection is inverse of latest for current versions', function (): void {
            // This test assumes only two versions exist
            $v10 = SchemaVersion::V1_0;
            $v11 = SchemaVersion::V1_1;

            expect($v10->isLegacy())->toBe(! $v10->isLatest());
            expect($v11->isLegacy())->toBe(! $v11->isLatest());
        });
    });

    describe('edge cases and validation', function (): void {
        test('numeric version conversion is precise', function (): void {
            $v10 = SchemaVersion::V1_0;
            $v11 = SchemaVersion::V1_1;

            // Ensure precision in floating point conversion
            expect($v10->getNumericVersion())->toBe(1.0);
            expect($v11->getNumericVersion())->toBe(1.1);

            // Test that we can distinguish between versions numerically
            $diff = $v11->getNumericVersion() - $v10->getNumericVersion();
            expect(abs($diff - 0.1))->toBeLessThan(0.0001);
        });

        test('comparison results are within expected range', function (): void {
            $v10 = SchemaVersion::V1_0;
            $v11 = SchemaVersion::V1_1;

            $result1 = $v10->compareTo($v11);
            $result2 = $v11->compareTo($v10);
            $result3 = $v10->compareTo($v10);

            // Results should be -1, 0, or 1
            expect($result1)->toBeIn([-1, 0, 1]);
            expect($result2)->toBeIn([-1, 0, 1]);
            expect($result3)->toBeIn([-1, 0, 1]);

            // Specific expectations
            expect($result1)->toBe(-1);
            expect($result2)->toBe(1);
            expect($result3)->toBe(0);
        });
    });
});
