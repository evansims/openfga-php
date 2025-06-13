<?php

declare(strict_types=1);

namespace OpenFGA\Translation;

use InvalidArgumentException;
use OpenFGA\{Language, Messages};

/**
 * Translation service interface for OpenFGA SDK messages.
 *
 * This interface defines the contract for a centralized message translation system
 * that supports parameterized messages, multiple locales, and integration with the
 * OpenFGA SDK's error handling and user-facing messaging systems.
 *
 * The translator is designed to work with the Messages enum for type-safe message
 * references and supports dynamic parameter substitution for contextual error
 * messages and user notifications.
 *
 * @see Messages For available message constants and keys
 */
interface TranslatorInterface
{
    /**
     * Add a translation resource to the translator.
     *
     * Registers a translation file with the translator for a specific locale and domain.
     * This allows the translator to load and use translations from various file formats
     * and organize them by locale and domain for better maintainability.
     *
     * @param string $format   The file format (for example, 'yaml', 'json', 'php')
     * @param string $resource The path to the translation file
     * @param string $locale   The locale code (for example, 'en', 'es', 'fr')
     * @param string $domain   The translation domain (defaults to 'messages')
     *
     * @throws InvalidArgumentException If the format is not supported or resource file doesn't exist
     */
    public static function addResource(string $format, string $resource, string $locale, string $domain = 'messages'): void;

    /**
     * Get the current default language.
     *
     * Returns the Language enum representing the current default locale.
     *
     * @return Language The current default language
     */
    public static function getDefaultLanguage(): Language;

    /**
     * Get the current default locale.
     *
     * @return string The current default locale code
     */
    public static function getDefaultLocale(): string;

    /**
     * Check if a translation exists for the given message.
     *
     * Determines whether a specific message has been translated in the given locale.
     * This is useful for conditional messaging or fallback handling when translations
     * may not be available for all locales.
     *
     * @param  Messages    $message The message enum case to check
     * @param  string|null $locale  Locale to check (defaults to configured locale)
     * @return bool        True if translation exists, false otherwise
     */
    public static function has(Messages $message, ?string $locale = null): bool;

    /**
     * Reset the translator instance.
     *
     * Clears the current translator instance and forces reinitialization on next use.
     * This is particularly useful for testing scenarios where you need to start with
     * a clean slate or when dynamically switching translation configurations.
     */
    public static function reset(): void;

    /**
     * Set the default language for translations.
     *
     * Configures the default language that is used for all translation operations
     * when no specific language is provided. This method provides type-safe language
     * configuration using the Language enum.
     *
     * @param Language $language The language to set as default
     */
    public static function setDefaultLanguage(Language $language): void;

    /**
     * Set the default locale for translations.
     *
     * Configures the default locale that is used for all translation operations
     * when no specific locale is provided. This setting affects the behavior of all
     * translation methods and determines fallback behavior.
     *
     * @param string $locale The locale code (for example, 'en', 'es', 'fr')
     *
     * @throws InvalidArgumentException If the locale format is invalid
     */
    public static function setDefaultLocale(string $locale): void;

    /**
     * Translate a message using a Messages enum case with Language enum.
     *
     * Provides type-safe message translation using both the Messages and Language enums
     * for maximum compile-time safety and better developer experience. This is the
     * preferred method for translating SDK messages.
     *
     * @param  Messages             $message    The message enum case to translate
     * @param  array<string, mixed> $parameters Parameters to substitute in the message (key-value pairs)
     * @param  Language|null        $language   Language to use (defaults to configured language)
     * @return string               The translated and parameterized message
     */
    public static function trans(Messages $message, array $parameters = [], ?Language $language = null): string;

    /**
     * Translate a message using a translation key string.
     *
     * Provides direct translation access using string keys instead of the Messages enum.
     * This method is useful for dynamic translations or when integrating with external
     * translation keys that are not defined in the Messages enum.
     *
     * @param string               $key        The translation key to look up
     * @param array<string, mixed> $parameters Parameters to substitute in the message (key-value pairs)
     * @param string|null          $locale     Locale to use (defaults to configured locale)
     *
     * @throws InvalidArgumentException If the locale format is invalid
     *
     * @return string The translated and parameterized message, or the key itself if no translation found
     */
    public static function transKey(string $key, array $parameters = [], ?string $locale = null): string;
}
