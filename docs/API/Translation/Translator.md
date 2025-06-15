# Translator

Translation service for OpenFGA SDK messages. This implementation provides centralized message translation with parameter substitution without external dependencies. It supports multiple locales, domain-based organization, and automatic fallback to default locale when translations are missing. The service uses a singleton pattern to maintain translation state across the application lifecycle and supports parameter substitution using %parameter% placeholder format for compatibility with existing message definitions.

<details>
<summary><strong>Table of Contents</strong></summary>

- [Namespace](#namespace)
- [Source](#source)
- [Implements](#implements)
- [Related Classes](#related-classes)
- [Methods](#methods)

- [`addResource()`](#addresource)
  - [`getDefaultLanguage()`](#getdefaultlanguage)
  - [`getDefaultLocale()`](#getdefaultlocale)
  - [`has()`](#has)
  - [`reset()`](#reset)
  - [`setDefaultLanguage()`](#setdefaultlanguage)
  - [`setDefaultLocale()`](#setdefaultlocale)
  - [`trans()`](#trans)
  - [`transKey()`](#transkey)

</details>

## Namespace

`OpenFGA\Translation`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Translation/Translator.php)

## Implements

- [`TranslatorInterface`](TranslatorInterface.md)

## Related Classes

- [TranslatorInterface](Translation/TranslatorInterface.md) (interface)

## Methods

### addResource

*<small>Implements Translation\TranslatorInterface</small>*

```php
public function addResource(
    string $format,
    string $resource,
    string $locale,
    string $domain = 'messages',
): void

```

Add a translation resource to the translator. Registers a translation file with the translator for a specific locale and domain. This allows the translator to load and use translations from various file formats and organize them by locale and domain for better maintainability.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Translation/TranslatorInterface.php#L39)

#### Parameters

| Name        | Type     | Description                                                                        |
| ----------- | -------- | ---------------------------------------------------------------------------------- |
| `$format`   | `string` | The file format (for example, &#039;yaml&#039;, &#039;json&#039;, &#039;php&#039;) |
| `$resource` | `string` | The path to the translation file                                                   |
| `$locale`   | `string` | The locale code (for example, &#039;en&#039;, &#039;es&#039;, &#039;fr&#039;)      |
| `$domain`   | `string` | The translation domain (defaults to &#039;messages&#039;)                          |

#### Returns

`void`

### getDefaultLanguage

*<small>Implements Translation\TranslatorInterface</small>*

```php
public function getDefaultLanguage(): Language

```

Get the current default language. Returns the Language enum representing the current default locale.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Translation/TranslatorInterface.php#L48)

#### Returns

[`Language`](Language.md) — The current default language

### getDefaultLocale

*<small>Implements Translation\TranslatorInterface</small>*

```php
public function getDefaultLocale(): string

```

Get the current default locale.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Translation/TranslatorInterface.php#L55)

#### Returns

`string` — The current default locale code

### has

*<small>Implements Translation\TranslatorInterface</small>*

```php
public function has(Messages $message, string|null $locale = NULL): bool

```

Check if a translation exists for the given message. Determines whether a specific message has been translated in the given locale. This is useful for conditional messaging or fallback handling when translations may not be available for all locales.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Translation/TranslatorInterface.php#L68)

#### Parameters

| Name       | Type                      | Description                                     |
| ---------- | ------------------------- | ----------------------------------------------- |
| `$message` | [`Messages`](Messages.md) | The message enum case to check                  |
| `$locale`  | `string` &#124; `null`    | Locale to check (defaults to configured locale) |

#### Returns

`bool` — True if translation exists, false otherwise

### reset

*<small>Implements Translation\TranslatorInterface</small>*

```php
public function reset(): void

```

Reset the translator instance. Clears the current translator instance and forces reinitialization on next use. This is particularly useful for testing scenarios where you need to start with a clean slate or when dynamically switching translation configurations.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Translation/TranslatorInterface.php#L77)

#### Returns

`void`

### setDefaultLanguage

*<small>Implements Translation\TranslatorInterface</small>*

```php
public function setDefaultLanguage(Language $language): void

```

Set the default language for translations. Configures the default language that is used for all translation operations when no specific language is provided. This method provides type-safe language configuration using the Language enum.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Translation/TranslatorInterface.php#L88)

#### Parameters

| Name        | Type                      | Description                    |
| ----------- | ------------------------- | ------------------------------ |
| `$language` | [`Language`](Language.md) | The language to set as default |

#### Returns

`void`

### setDefaultLocale

*<small>Implements Translation\TranslatorInterface</small>*

```php
public function setDefaultLocale(string $locale): void

```

Set the default locale for translations. Configures the default locale that is used for all translation operations when no specific locale is provided. This setting affects the behavior of all translation methods and determines fallback behavior.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Translation/TranslatorInterface.php#L101)

#### Parameters

| Name      | Type     | Description                                                                   |
| --------- | -------- | ----------------------------------------------------------------------------- |
| `$locale` | `string` | The locale code (for example, &#039;en&#039;, &#039;es&#039;, &#039;fr&#039;) |

#### Returns

`void`

### trans

*<small>Implements Translation\TranslatorInterface</small>*

```php
public function trans(Messages $message, array<string, mixed> $parameters = [], Language|null $language = NULL): string

```

Translate a message using a Messages enum case with Language enum. Provides type-safe message translation using both the Messages and Language enums for maximum compile-time safety and better developer experience. This is the preferred method for translating SDK messages.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Translation/TranslatorInterface.php#L115)

#### Parameters

| Name          | Type                                    | Description                                       |
| ------------- | --------------------------------------- | ------------------------------------------------- |
| `$message`    | [`Messages`](Messages.md)               | The message enum case to translate                |
| `$parameters` | `array&lt;`string`, `mixed`&gt;`        |                                                   |
| `$language`   | [`Language`](Language.md) &#124; `null` | Language to use (defaults to configured language) |

#### Returns

`string` — The translated and parameterized message

### transKey

*<small>Implements Translation\TranslatorInterface</small>*

```php
public function transKey(string $key, array<string, mixed> $parameters = [], string|null $locale = NULL): string

```

Translate a message using a translation key string. Provides direct translation access using string keys instead of the Messages enum. This method is useful for dynamic translations or when integrating with external translation keys that are not defined in the Messages enum.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Translation/TranslatorInterface.php#L132)

#### Parameters

| Name          | Type                             | Description                                   |
| ------------- | -------------------------------- | --------------------------------------------- |
| `$key`        | `string`                         | The translation key to look up                |
| `$parameters` | `array&lt;`string`, `mixed`&gt;` |                                               |
| `$locale`     | `string` &#124; `null`           | Locale to use (defaults to configured locale) |

#### Returns

`string` — The translated and parameterized message, or the key itself if no translation found
