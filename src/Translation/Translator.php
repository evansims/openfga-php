<?php

declare(strict_types=1);

namespace OpenFGA\Translation;

use InvalidArgumentException;
use OpenFGA\Exceptions\{ClientError, ClientThrowable};
use OpenFGA\Messages;
use Override;
use ReflectionException;

use function array_key_exists;
use function array_merge;
use function dirname;
use function file_exists;
use function is_array;
use function is_object;
use function is_scalar;
use function is_string;
use function method_exists;
use function preg_replace_callback;
use function sprintf;

/**
 * Translation service for OpenFGA SDK messages.
 *
 * This implementation provides centralized message translation with parameter
 * substitution without external dependencies. It supports multiple locales,
 * domain-based organization, and automatic fallback to default locale when
 * translations are missing.
 *
 * The service uses a singleton pattern to maintain translation state across the
 * application lifecycle and supports parameter substitution using %parameter%
 * placeholder format for compatibility with existing message definitions.
 *
 * @see Messages For available message constants and keys
 */
final class Translator implements TranslatorInterface
{
    private static string $defaultLocale = 'en';

    private static bool $initialized = false;

    /**
     * @var array<string, array<string, array<string, mixed>>>
     */
    private static array $translations = [];

    /**
     * @inheritDoc
     *
     * @throws ClientThrowable          If format is unsupported or translation file is not found
     * @throws InvalidArgumentException If message translation parameters are invalid
     * @throws ReflectionException      If exception location capture fails
     */
    #[Override]
    public static function addResource(string $format, string $resource, string $locale, string $domain = 'messages'): void
    {
        if ('yaml' !== $format) {
            throw ClientError::Configuration->exception(context: ['message' => self::trans(Messages::TRANSLATION_UNSUPPORTED_FORMAT, ['format' => $format])]);
        }

        if (! file_exists($resource)) {
            throw ClientError::Configuration->exception(context: ['message' => self::trans(Messages::TRANSLATION_FILE_NOT_FOUND, ['resource' => $resource])]);
        }

        $data = YamlParser::parseFile($resource);
        self::addTranslations($data, $locale, $domain);
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public static function getDefaultLocale(): string
    {
        return self::$defaultLocale;
    }

    /**
     * @inheritDoc
     *
     * @throws InvalidArgumentException If the locale format is invalid
     */
    #[Override]
    public static function has(Messages $message, ?string $locale = null): bool
    {
        self::ensureInitialized();
        $locale ??= self::$defaultLocale;

        return null !== self::getTranslation($message->key(), $locale, 'messages');
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public static function reset(): void
    {
        self::$translations = [];
        self::$initialized = false;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public static function setDefaultLocale(string $locale): void
    {
        self::$defaultLocale = $locale;
    }

    /**
     * @inheritDoc
     *
     * @param array<string, mixed> $parameters
     */
    #[Override]
    public static function trans(Messages $message, array $parameters = [], ?string $locale = null): string
    {
        // Convert parameter keys to %key% format
        /** @var array<string, mixed> $formattedParameters */
        $formattedParameters = [];

        /** @var mixed $value */
        foreach ($parameters as $key => $value) {
            $formattedKey = str_starts_with($key, '%') && str_ends_with($key, '%') ? $key : sprintf('%%%s%%', $key);
            // Assign mixed parameter value to translation parameters array
            self::assignMixed($formattedParameters, $formattedKey, $value);
        }

        return self::translate($message->key(), $formattedParameters, 'messages', $locale);
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public static function transKey(string $key, array $parameters = [], ?string $locale = null): string
    {
        return self::translate($key, $parameters, 'messages', $locale);
    }

    /**
     * Add translations from an array.
     *
     * @param array<string, mixed> $translations The translation data
     * @param string               $locale       The locale for these translations
     * @param string               $domain       The translation domain
     */
    private static function addTranslations(array $translations, string $locale, string $domain = 'messages'): void
    {
        if (! array_key_exists($locale, self::$translations)) {
            self::$translations[$locale] = [];
        }

        if (! array_key_exists($domain, self::$translations[$locale])) {
            self::$translations[$locale][$domain] = [];
        }

        self::$translations[$locale][$domain] = self::flattenArray($translations);
    }

    /**
     * Safely assign a mixed value to an array to satisfy Psalm.
     *
     * @param array<string, mixed> $array The target array
     * @param string               $key   The array key
     * @param mixed                $value The value to assign
     *
     * @psalm-suppress MixedAssignment
     */
    private static function assignMixed(array &$array, string $key, mixed $value): void
    {
        $array[$key] = $value;
    }

    /**
     * Ensure the translation system is initialized with available translation files.
     *
     * Lazy-loads translation files from the translations directory and maintains
     * the singleton pattern to ensure consistent translation state across all
     * method calls.
     *
     * @throws InvalidArgumentException If locale configuration is invalid
     */
    private static function ensureInitialized(): void
    {
        if (self::$initialized) {
            return;
        }

        // Load available translations
        $translationsDir = dirname(__DIR__, 2) . '/translations';
        $locales = ['en', 'es']; // Add more locales as they become available

        foreach ($locales as $locale) {
            $translationPath = $translationsDir . '/messages.' . $locale . '.yaml';

            if (file_exists($translationPath)) {
                self::addResource('yaml', $translationPath, $locale);
            }
        }

        self::$initialized = true;
    }

    /**
     * Flatten a nested array into dot notation keys.
     *
     * @param  array<string, mixed> $array  The array to flatten
     * @param  string               $prefix The key prefix
     * @return array<string, mixed> The flattened array
     *
     * @psalm-suppress MixedAssignment
     * @psalm-suppress DocblockTypeContradiction
     */
    private static function flattenArray(array $array, string $prefix = ''): array
    {
        /** @var array<string, mixed> $result */
        $result = [];

        foreach ($array as $key => $value) {
            if (! is_string($key)) {
                continue; // Skip non-string keys
            }

            $newKey = '' === $prefix ? $key : sprintf('%s.%s', $prefix, $key);

            if (is_array($value)) {
                /** @var array<string, mixed> $value */
                $result = array_merge($result, self::flattenArray($value, $newKey));
            } else {
                $result[$newKey] = $value;
            }
        }

        return $result;
    }

    /**
     * Get a translation for a specific key, locale, and domain.
     *
     * @param  string      $key    The translation key
     * @param  string      $locale The locale
     * @param  string      $domain The domain
     * @return string|null The translation or null if not found
     */
    private static function getTranslation(string $key, string $locale, string $domain): ?string
    {
        if (! array_key_exists($locale, self::$translations)) {
            return null;
        }

        if (! array_key_exists($domain, self::$translations[$locale])) {
            return null;
        }

        if (! array_key_exists($key, self::$translations[$locale][$domain])) {
            return null;
        }

        /** @var mixed $translation */
        $translation = self::$translations[$locale][$domain][$key];

        return is_string($translation) ? $translation : null;
    }

    /**
     * Substitute parameters in a translation string.
     *
     * @param  string               $translation The translation string
     * @param  array<string, mixed> $parameters  The parameters to substitute
     * @return string               The translation with substituted parameters
     *
     * @psalm-suppress PossiblyUndefinedIntArrayOffset
     */
    private static function substituteParameters(string $translation, array $parameters): string
    {
        if ([] === $parameters) {
            return $translation;
        }

        $result = preg_replace_callback('/%%?([^%]+)%%?/', static function ($matches) use ($parameters) {
            $paramName = $matches[1];

            // Try with % wrapper first
            $wrappedKey = sprintf('%%%s%%', $paramName);

            if (array_key_exists($wrappedKey, $parameters)) {
                /** @var mixed $value */
                $value = $parameters[$wrappedKey];

                return is_scalar($value) || (is_object($value) && method_exists($value, '__toString'))
                    ? (string) $value
                    : $matches[0];
            }

            // Try without % wrapper
            if (array_key_exists($paramName, $parameters)) {
                /** @var mixed $value */
                $value = $parameters[$paramName];

                return is_scalar($value) || (is_object($value) && method_exists($value, '__toString'))
                    ? (string) $value
                    : $matches[0];
            }

            // Return original if parameter not found
            return $matches[0];
        }, $translation);

        return $result ?? $translation;
    }

    /**
     * Translate a message key with optional parameters.
     *
     * @param string               $key        The translation key (dot notation)
     * @param array<string, mixed> $parameters Parameters for substitution
     * @param string               $domain     The translation domain
     * @param string|null          $locale     The locale to use (null for default)
     *
     * @throws InvalidArgumentException If locale configuration is invalid
     *
     * @return string The translated message or the key if translation not found
     */
    private static function translate(string $key, array $parameters = [], string $domain = 'messages', ?string $locale = null): string
    {
        self::ensureInitialized();

        $locale ??= self::$defaultLocale;

        // Try to get translation for requested locale
        $translation = self::getTranslation($key, $locale, $domain);

        // Fallback to default locale if not found and different from requested
        if (null === $translation && $locale !== self::$defaultLocale) {
            $translation = self::getTranslation($key, self::$defaultLocale, $domain);
        }

        // If still not found, return the key
        if (null === $translation) {
            return $key;
        }

        // Substitute parameters
        return self::substituteParameters($translation, $parameters);
    }
}
