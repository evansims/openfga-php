<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit;

use Exception;
use OpenFGA\Language;
use OpenFGA\Translation\Translator;

describe('Language', function (): void {
    beforeEach(function (): void {
        Translator::reset();
    });

    afterEach(function (): void {
        Translator::reset();
        Translator::setDefaultLocale('en');
    });

    test('Language enum has all expected cases', function (): void {
        $cases = Language::cases();
        $values = array_map(fn ($case) => $case->value, $cases);

        expect($values)->toContain('de', 'en', 'es', 'fr', 'it', 'ja', 'ko', 'nl', 'pt_BR', 'ru', 'sv', 'tr', 'uk', 'zh_CN');
        expect($cases)->toHaveCount(14);
    });

    test('Language::default() returns English', function (): void {
        expect(Language::default())->toBe(Language::English);
    });

    test('Language::fromLocale() works with valid locales', function (): void {
        expect(Language::fromLocale('en'))->toBe(Language::English);
        expect(Language::fromLocale('es'))->toBe(Language::Spanish);
        expect(Language::fromLocale('pt_BR'))->toBe(Language::PortugueseBrazilian);
        expect(Language::fromLocale('pt-BR'))->toBe(Language::PortugueseBrazilian); // Hyphen normalization
    });

    test('Language::fromLocale() returns null for invalid locales', function (): void {
        expect(Language::fromLocale('xx'))->toBeNull();
        expect(Language::fromLocale('invalid'))->toBeNull();
    });

    test('Language locale() returns correct values', function (): void {
        expect(Language::English->locale())->toBe('en');
        expect(Language::Spanish->locale())->toBe('es');
        expect(Language::PortugueseBrazilian->locale())->toBe('pt_BR');
        expect(Language::ChineseSimplified->locale())->toBe('zh_CN');
    });

    test('Language displayName() returns English names', function (): void {
        expect(Language::English->displayName())->toBe('English');
        expect(Language::Spanish->displayName())->toBe('Spanish');
        expect(Language::German->displayName())->toBe('German');
        expect(Language::PortugueseBrazilian->displayName())->toBe('Portuguese (Brazilian)');
        expect(Language::ChineseSimplified->displayName())->toBe('Chinese (Simplified)');
    });

    test('Language nativeName() returns native names', function (): void {
        expect(Language::English->nativeName())->toBe('English');
        expect(Language::Spanish->nativeName())->toBe('Español');
        expect(Language::German->nativeName())->toBe('Deutsch');
        expect(Language::French->nativeName())->toBe('Français');
        expect(Language::Japanese->nativeName())->toBe('日本語');
        expect(Language::Korean->nativeName())->toBe('한국어');
        expect(Language::ChineseSimplified->nativeName())->toBe('简体中文');
    });

    test('Language isoCode() returns correct language codes', function (): void {
        expect(Language::English->isoCode())->toBe('en');
        expect(Language::Spanish->isoCode())->toBe('es');
        expect(Language::PortugueseBrazilian->isoCode())->toBe('pt');
        expect(Language::ChineseSimplified->isoCode())->toBe('zh');
    });

    test('Language regionCode() works correctly', function (): void {
        expect(Language::English->regionCode())->toBeNull();
        expect(Language::Spanish->regionCode())->toBeNull();
        expect(Language::PortugueseBrazilian->regionCode())->toBe('BR');
        expect(Language::ChineseSimplified->regionCode())->toBe('CN');
    });

    test('Language isRightToLeft() returns false for all current languages', function (): void {
        foreach (Language::cases() as $language) {
            expect($language->isRightToLeft())->toBeFalse();
        }
    });

    test('Language apply() sets the translator locale', function (): void {
        expect(Translator::getDefaultLocale())->toBe('en');

        Language::Spanish->apply();
        expect(Translator::getDefaultLocale())->toBe('es');

        Language::German->apply();
        expect(Translator::getDefaultLocale())->toBe('de');
    });

    test('Language isActive() detects current locale', function (): void {
        expect(Language::English->isActive())->toBeTrue();
        expect(Language::Spanish->isActive())->toBeFalse();

        Language::Spanish->apply();
        expect(Language::English->isActive())->toBeFalse();
        expect(Language::Spanish->isActive())->toBeTrue();
    });

    test('Language withLocale() temporarily changes locale', function (): void {
        expect(Translator::getDefaultLocale())->toBe('en');

        $result = Language::Spanish->withLocale(function () {
            expect(Translator::getDefaultLocale())->toBe('es');

            return 'test-result';
        });

        expect($result)->toBe('test-result');
        expect(Translator::getDefaultLocale())->toBe('en'); // Restored
    });

    test('Language withLocale() restores locale even on exception', function (): void {
        expect(Translator::getDefaultLocale())->toBe('en');

        try {
            Language::Spanish->withLocale(function (): void {
                expect(Translator::getDefaultLocale())->toBe('es');

                throw new Exception('Test exception');
            });
        } catch (Exception $e) {
            expect($e->getMessage())->toBe('Test exception');
        }

        expect(Translator::getDefaultLocale())->toBe('en'); // Restored even after exception
    });
});
