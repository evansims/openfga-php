<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Helpers;

use OpenFGA\{Language, Messages};
use OpenFGA\Translation\Translator;

use function function_exists;
use function is_callable;
use function OpenFGA\trans;

describe('trans() helper function', function (): void {
    beforeEach(function (): void {
        // Reset translator state before each test
        Translator::reset();
    });

    afterEach(function (): void {
        // Reset to default locale after each test
        Translator::setDefaultLocale('en');
    });

    test('trans() function exists and is callable', function (): void {
        expect(function_exists('OpenFGA\\trans'))->toBeTrue();
        expect(is_callable('OpenFGA\\trans'))->toBeTrue();
    });

    test('trans() translates messages without parameters', function (): void {
        $message = Messages::NO_LAST_REQUEST_FOUND;
        $translation = trans($message);

        expect($translation)->toBeString();
        expect($translation)->not()->toBeEmpty();
        expect($translation)->toBe('No last request found');
    });

    test('trans() translates messages with parameters', function (): void {
        $message = Messages::NETWORK_ERROR;
        $parameters = ['message' => 'Connection timeout'];

        $translation = trans($message, $parameters);

        expect($translation)->toBeString();
        expect($translation)->toContain('Connection timeout');
    });

    test('trans() handles parameters with % format', function (): void {
        $message = Messages::NETWORK_ERROR;
        $parameters = ['%message%' => 'Already formatted parameter'];

        $translation = trans($message, $parameters);

        expect($translation)->toBeString();
        expect($translation)->toContain('Already formatted parameter');
    });

    test('trans() with specific language', function (): void {
        $message = Messages::NO_LAST_REQUEST_FOUND;

        $englishTranslation = trans($message, [], Language::English);
        $spanishTranslation = trans($message, [], Language::Spanish);

        expect($englishTranslation)->toBe('No last request found');
        expect($spanishTranslation)->toBe('No se encontró la última solicitud');
    });

    test('trans() returns same result as Translator::trans()', function (): void {
        $message = Messages::AUTH_INVALID_RESPONSE_FORMAT;
        $parameters = ['field' => 'access_token'];
        $language = Language::German;

        $helperResult = trans($message, $parameters, $language);
        $translatorResult = Translator::trans($message, $parameters, $language);

        expect($helperResult)->toBe($translatorResult);
    });

    test('trans() handles complex parameter substitution', function (): void {
        $message = Messages::SERIALIZATION_ERROR_INVALID_ITEM_TYPE;
        $parameters = [
            'property' => 'items',
            'className' => 'MyCollection',
            'expected' => 'MyInterface',
            'actual_type' => 'stdClass',
        ];

        $translation = trans($message, $parameters);

        expect($translation)->toContain('items');
        expect($translation)->toContain('MyCollection');
        expect($translation)->toContain('MyInterface');
        expect($translation)->toContain('stdClass');
    });

    test('trans() works with default parameters', function (): void {
        $message = Messages::AUTH_USER_MESSAGE_TOKEN_EXPIRED;
        $translation = trans($message);

        expect($translation)->toBeString();
        expect($translation)->not()->toBeEmpty();
        expect($translation)->toBe('Your session has expired. Please sign in again.');
    });

    test('trans() handles empty parameters array', function (): void {
        $message = Messages::NO_LAST_REQUEST_FOUND;
        $translation = trans($message, []);

        expect($translation)->toBe('No last request found');
    });

    test('trans() with German language', function (): void {
        $message = Messages::AUTH_USER_MESSAGE_TOKEN_EXPIRED;
        $translation = trans($message, [], Language::German);

        expect($translation)->toBe('Ihre Sitzung ist abgelaufen. Bitte melden Sie sich erneut an.');
    });

    test('trans() parameter conversion works correctly', function (): void {
        $message = Messages::DSL_UNRECOGNIZED_TERM;

        // Test with plain parameter
        $translation1 = trans($message, ['term' => 'invalid_term']);
        expect($translation1)->toContain('invalid_term');

        // Test with pre-formatted parameter
        $translation2 = trans($message, ['%term%' => 'another_term']);
        expect($translation2)->toContain('another_term');
    });

    test('trans() function works in global namespace when imported', function (): void {
        // This test verifies that the function can be used with a use statement
        $message = Messages::NO_LAST_REQUEST_FOUND;
        $result = \OpenFGA\trans($message);

        expect($result)->toBe('No last request found');
    });
});
