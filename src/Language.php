<?php

declare(strict_types=1);

namespace OpenFGA;

use InvalidArgumentException;
use OpenFGA\Translation\Translator;

/**
 * Supported languages for OpenFGA SDK internationalization.
 *
 * This enum represents all available languages for SDK messages and error
 * translations, providing type-safe language selection with rich metadata
 * about each supported locale including native names, ISO codes, and
 * text directionality.
 *
 * @see Translator For the translation system implementation
 * @see https://openfga.dev/docs/getting-started/setup-sdk-client#language SDK language configuration
 */
enum Language: string
{
    /**
     * Chinese Simplified language support.
     */
    case ChineseSimplified = 'zh_CN';

    /**
     * Dutch language support.
     */
    case Dutch = 'nl';

    /**
     * English language support (default).
     */
    case English = 'en';

    /**
     * French language support.
     */
    case French = 'fr';

    /**
     * German language support.
     */
    case German = 'de';

    /**
     * Italian language support.
     */
    case Italian = 'it';

    /**
     * Japanese language support.
     */
    case Japanese = 'ja';

    /**
     * Korean language support.
     */
    case Korean = 'ko';

    /**
     * Brazilian Portuguese language support.
     */
    case PortugueseBrazilian = 'pt_BR';

    /**
     * Russian language support.
     */
    case Russian = 'ru';

    /**
     * Spanish language support.
     */
    case Spanish = 'es';

    /**
     * Swedish language support.
     */
    case Swedish = 'sv';

    /**
     * Turkish language support.
     */
    case Turkish = 'tr';

    /**
     * Ukrainian language support.
     */
    case Ukrainian = 'uk';

    /**
     * Get the default language for the SDK.
     *
     * Returns English as the default language when no specific
     * language preference has been configured.
     *
     * @return self The default language (English)
     */
    public static function default(): self
    {
        return self::English;
    }

    /**
     * Create a Language enum from a locale code string.
     *
     * Supports both underscore and hyphen separators for locale codes
     * (for example, "pt_BR" or "pt-BR" both map to PortugueseBrazilian).
     *
     * @param  string    $locale The locale code to parse
     * @return self|null The matching Language enum or null if not found
     */
    public static function fromLocale(string $locale): ?self
    {
        // Normalize locale separators to underscore
        $normalizedLocale = str_replace('-', '_', $locale);

        foreach (self::cases() as $case) {
            if ($case->value === $normalizedLocale) {
                return $case;
            }
        }

        return null;
    }

    /**
     * Apply this language as the active translation locale.
     *
     * Sets this language as the default locale for all subsequent
     * translation operations until changed or reset.
     *
     * @throws InvalidArgumentException If the locale format is invalid
     */
    public function apply(): void
    {
        Translator::setDefaultLocale($this->value);
    }

    /**
     * Get the display name of the language in English.
     *
     * Returns the English name of the language for UI display
     * and documentation purposes.
     *
     * @return string The English name of the language
     */
    public function displayName(): string
    {
        return match ($this) {
            self::German => 'German',
            self::English => 'English',
            self::Spanish => 'Spanish',
            self::French => 'French',
            self::Italian => 'Italian',
            self::Japanese => 'Japanese',
            self::Korean => 'Korean',
            self::Dutch => 'Dutch',
            self::PortugueseBrazilian => 'Portuguese (Brazilian)',
            self::Russian => 'Russian',
            self::Swedish => 'Swedish',
            self::Turkish => 'Turkish',
            self::Ukrainian => 'Ukrainian',
            self::ChineseSimplified => 'Chinese (Simplified)',
        };
    }

    /**
     * Check if this language is the currently active locale.
     *
     * Determines whether this language is currently set as the
     * default locale for translation operations.
     *
     * @return bool True if this is the active language
     */
    public function isActive(): bool
    {
        return $this->value === Translator::getDefaultLocale();
    }

    /**
     * Get the ISO 639-1 two-letter language code.
     *
     * Returns the base language code without region specifier.
     * For regional variants, this returns the primary language code.
     *
     * @return string The ISO 639-1 language code
     */
    public function isoCode(): string
    {
        $locale = $this->value;
        $underscorePos = strpos($locale, '_');

        return false !== $underscorePos
            ? substr($locale, 0, $underscorePos)
            : $locale;
    }

    /**
     * Check if this language uses right-to-left text direction.
     *
     * Useful for UI implementations that need to adjust layout
     * direction based on the selected language.
     *
     * @return bool True if the language is RTL
     */
    public function isRightToLeft(): bool
    {
        // Currently none of our supported languages are RTL
        // This is here for future extensibility
        return false;
    }

    /**
     * Get the locale code for this language.
     *
     * Returns the full locale identifier including any region
     * specifier (for example, "pt_BR" for Brazilian Portuguese).
     *
     * @return string The locale code
     */
    public function locale(): string
    {
        return $this->value;
    }

    /**
     * Get the native name of the language.
     *
     * Returns the language name as written in that language,
     * useful for language selection interfaces.
     *
     * @return string The native language name
     */
    public function nativeName(): string
    {
        return match ($this) {
            self::German => 'Deutsch',
            self::English => 'English',
            self::Spanish => 'Español',
            self::French => 'Français',
            self::Italian => 'Italiano',
            self::Japanese => '日本語',
            self::Korean => '한국어',
            self::Dutch => 'Nederlands',
            self::PortugueseBrazilian => 'Português (Brasil)',
            self::Russian => 'Русский',
            self::Swedish => 'Svenska',
            self::Turkish => 'Türkçe',
            self::Ukrainian => 'Українська',
            self::ChineseSimplified => '简体中文',
        };
    }

    /**
     * Get the region code if this is a regional language variant.
     *
     * Returns the ISO 3166-1 alpha-2 country code for regional
     * language variants, or null for generic language codes.
     *
     * @return string|null The region code or null
     */
    public function regionCode(): ?string
    {
        $locale = $this->value;
        $underscorePos = strpos($locale, '_');

        return false !== $underscorePos
            ? substr($locale, $underscorePos + 1)
            : null;
    }

    /**
     * Execute a callback with this language as the active locale.
     *
     * Temporarily sets this language as the active locale, executes
     * the provided callback, then restores the previous locale.
     * This ensures proper cleanup even if the callback throws.
     *
     * @template T
     *
     * @param callable(): T $callback The callback to execute
     *
     * @throws InvalidArgumentException If the locale format is invalid
     *
     * @return T The result of the callback
     */
    public function withLocale(callable $callback): mixed
    {
        $previousLocale = Translator::getDefaultLocale();

        try {
            $this->apply();

            return $callback();
        } finally {
            Translator::setDefaultLocale($previousLocale);
        }
    }
}
