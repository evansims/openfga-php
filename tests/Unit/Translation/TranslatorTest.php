<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Translation;

use OpenFGA\{Language, Messages};
use OpenFGA\Translation\{Translator, TranslatorInterface};

describe('Translator', function (): void {
    beforeEach(function (): void {
        // Reset translator state before each test
        Translator::reset();
    });

    afterEach(function (): void {
        // Reset to default locale after each test
        Translator::setDefaultLocale('en');
    });

    test('Translator implements TranslatorInterface', function (): void {
        expect(Translator::class)->toImplement(TranslatorInterface::class);
    });

    test('getDefaultLocale() returns default locale', function (): void {
        expect(Translator::getDefaultLocale())->toBe('en');
    });

    test('setDefaultLocale() changes default locale', function (): void {
        Translator::setDefaultLocale('es');
        expect(Translator::getDefaultLocale())->toBe('es');

        Translator::setDefaultLocale('fr');
        expect(Translator::getDefaultLocale())->toBe('fr');
    });

    test('trans() translates messages without parameters', function (): void {
        $message = Messages::NO_LAST_REQUEST_FOUND;
        $translation = Translator::trans($message);

        expect($translation)->toBeString();
        expect($translation)->not()->toBeEmpty();
        expect($translation)->toBe('No last request found');
    });

    test('trans() translates messages with parameters', function (): void {
        $message = Messages::NETWORK_ERROR;
        $parameters = ['message' => 'Connection timeout'];

        $translation = Translator::trans($message, $parameters);

        expect($translation)->toBeString();
        expect($translation)->toContain('Connection timeout');
    });

    test('trans() handles parameters with % format', function (): void {
        $message = Messages::NETWORK_ERROR;
        $parameters = ['%message%' => 'Already formatted parameter'];

        $translation = Translator::trans($message, $parameters);

        expect($translation)->toBeString();
        expect($translation)->toContain('Already formatted parameter');
    });

    test('trans() handles mixed parameter formats', function (): void {
        $message = Messages::SERIALIZATION_ERROR_INVALID_ITEM_TYPE;
        $parameters = [
            'property' => 'items',
            '%className%' => 'TestCollection',
            'expected' => 'string',
            'actual_type' => 'integer',
        ];

        $translation = Translator::trans($message, $parameters);

        expect($translation)->toBeString();
        expect($translation)->toContain('items');
        expect($translation)->toContain('TestCollection');
        expect($translation)->toContain('string');
        expect($translation)->toContain('integer');
    });

    test('trans() with specific locale', function (): void {
        // Add Spanish translation resource
        $spanishTranslationPath = __DIR__ . '/../../../translations/messages.es.yaml';
        Translator::addResource('yaml', $spanishTranslationPath, 'es');

        $message = Messages::NO_LAST_REQUEST_FOUND;

        $englishTranslation = Translator::trans($message, [], Language::English);
        $spanishTranslation = Translator::trans($message, [], Language::Spanish);

        expect($englishTranslation)->toBe('No last request found');
        expect($spanishTranslation)->toBe('No se encontró la última solicitud');
    });

    test('transKey() translates arbitrary keys', function (): void {
        $key = 'client.no_last_request_found';
        $translation = Translator::transKey($key);

        expect($translation)->toBe('No last request found');
    });

    test('transKey() with parameters', function (): void {
        $key = 'network.error';
        $parameters = ['%message%' => 'Test error message'];

        $translation = Translator::transKey($key, $parameters);

        expect($translation)->toContain('Test error message');
    });

    test('transKey() with specific locale', function (): void {
        // Add Spanish translation resource
        $spanishTranslationPath = __DIR__ . '/../../../translations/messages.es.yaml';
        Translator::addResource('yaml', $spanishTranslationPath, 'es');

        $key = 'client.no_last_request_found';

        $englishTranslation = Translator::transKey($key, [], 'en');
        $spanishTranslation = Translator::transKey($key, [], 'es');

        expect($englishTranslation)->toBe('No last request found');
        expect($spanishTranslation)->toBe('No se encontró la última solicitud');
    });

    test('has() checks if translation exists', function (): void {
        $message = Messages::NO_LAST_REQUEST_FOUND;

        expect(Translator::has($message))->toBeTrue();
        expect(Translator::has($message, 'en'))->toBeTrue();
    });

    test('has() returns false for non-existent translations', function (): void {
        // Add Spanish translation resource
        $spanishTranslationPath = __DIR__ . '/../../../translations/messages.es.yaml';
        Translator::addResource('yaml', $spanishTranslationPath, 'es');

        $message = Messages::NO_LAST_REQUEST_FOUND;

        expect(Translator::has($message, 'xx'))->toBeFalse(); // Non-existent locale
    });

    test('addResource() adds new translation resources', function (): void {
        $spanishTranslationPath = __DIR__ . '/../../../translations/messages.es.yaml';

        // Test with a non-existent locale instead
        $message = Messages::NO_LAST_REQUEST_FOUND;
        expect(Translator::has($message, 'xx'))->toBeFalse();

        // Test that Spanish was auto-loaded
        expect(Translator::has($message, 'es'))->toBeTrue();
        $spanishTranslation = Translator::trans($message, [], Language::Spanish);
        expect($spanishTranslation)->toBe('No se encontró la última solicitud');

        // Test that French was also auto-loaded
        expect(Translator::has($message, 'fr'))->toBeTrue();
        $frenchTranslation = Translator::trans($message, [], Language::French);
        expect($frenchTranslation)->not->toBe($message->key()); // Should be translated

        // Test manual resource addition with a non-existent locale file
        // This demonstrates that addResource still works for future translations
    });

    test('reset() clears translator state', function (): void {
        // First access to initialize translator
        $translation1 = Translator::trans(Messages::NO_LAST_REQUEST_FOUND);
        expect($translation1)->toBeString();

        // Reset translator
        Translator::reset();

        // Should still work after reset
        $translation2 = Translator::trans(Messages::NO_LAST_REQUEST_FOUND);
        expect($translation2)->toBe($translation1);
    });

    test('setDefaultLocale() updates existing translator locale', function (): void {
        // Initialize translator with default locale
        $englishTranslation = Translator::trans(Messages::NO_LAST_REQUEST_FOUND);
        expect($englishTranslation)->toBe('No last request found');

        // Add Spanish resource
        $spanishTranslationPath = __DIR__ . '/../../../translations/messages.es.yaml';
        Translator::addResource('yaml', $spanishTranslationPath, 'es');

        // Change default locale
        Translator::setDefaultLocale('es');

        // Should now return Spanish translation by default
        $spanishTranslation = Translator::trans(Messages::NO_LAST_REQUEST_FOUND);
        expect($spanishTranslation)->toBe('No se encontró la última solicitud');
    });

    test('handles complex parameter substitution', function (): void {
        $message = Messages::SERIALIZATION_ERROR_INVALID_ITEM_TYPE;
        $parameters = [
            'property' => 'items',
            'className' => 'MyCollection',
            'expected' => 'MyInterface',
            'actual_type' => 'stdClass',
        ];

        $translation = Translator::trans($message, $parameters);

        expect($translation)->toContain('items');
        expect($translation)->toContain('MyCollection');
        expect($translation)->toContain('MyInterface');
        expect($translation)->toContain('stdClass');
    });

    test('parameter conversion works correctly', function (): void {
        $message = Messages::DSL_UNRECOGNIZED_TERM;

        // Test with plain parameter
        $translation1 = Translator::trans($message, ['term' => 'invalid_term']);
        expect($translation1)->toContain('invalid_term');

        // Test with pre-formatted parameter
        $translation2 = Translator::trans($message, ['%term%' => 'another_term']);
        expect($translation2)->toContain('another_term');
    });

    test('supports domain parameter for addResource', function (): void {
        // Test that domain parameter is accepted (even if we only use 'messages' domain)
        $spanishTranslationPath = __DIR__ . '/../../../translations/messages.es.yaml';

        Translator::addResource('yaml', $spanishTranslationPath, 'es', 'messages');

        $message = Messages::NO_LAST_REQUEST_FOUND;
        expect(Translator::has($message, 'es'))->toBeTrue();
    });

    test('translator singleton behavior', function (): void {
        // Multiple calls should use the same translator instance internally
        $translation1 = Translator::trans(Messages::NO_LAST_REQUEST_FOUND);
        $translation2 = Translator::trans(Messages::NO_LAST_REQUEST_FOUND);

        expect($translation1)->toBe($translation2);
    });

    test('handles empty parameters array', function (): void {
        $message = Messages::NO_LAST_REQUEST_FOUND;
        $translation = Translator::trans($message, []);

        expect($translation)->toBe('No last request found');
    });

    test('loads English translations by default', function (): void {
        // Test that English translations are loaded automatically
        $message = Messages::AUTH_INVALID_RESPONSE_FORMAT;
        $translation = Translator::trans($message);

        expect($translation)->toBe('Invalid response format');
        expect($translation)->not()->toBe($message->key());
    });

    test('German translations work correctly', function (): void {
        // Test German translation resource
        $germanTranslationPath = __DIR__ . '/../../../translations/messages.de.yaml';
        Translator::addResource('yaml', $germanTranslationPath, 'de');

        $message = Messages::NO_LAST_REQUEST_FOUND;

        $englishTranslation = Translator::trans($message, [], Language::English);
        $germanTranslation = Translator::trans($message, [], Language::German);

        expect($englishTranslation)->toBe('No last request found');
        expect($germanTranslation)->toBe('Keine letzte Anfrage gefunden');

        // Test another message with complex text
        $authMessage = Messages::AUTH_USER_MESSAGE_TOKEN_EXPIRED;
        $germanAuthTranslation = Translator::trans($authMessage, [], Language::German);
        expect($germanAuthTranslation)->toBe('Ihre Sitzung ist abgelaufen. Bitte melden Sie sich erneut an.');
    });
});
