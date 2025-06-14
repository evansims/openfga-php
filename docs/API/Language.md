# Language

Supported languages for OpenFGA SDK internationalization. This enum represents all available languages for SDK messages and error translations, providing type-safe language selection with rich metadata about each supported locale including native names, ISO codes, and text directionality.

## Table of Contents

- [Namespace](#namespace)
- [Source](#source)
- [Implements](#implements)
- [Constants](#constants)
- [Cases](#cases)
- [Methods](#methods)

- [Utility](#utility)
  - [`displayName()`](#displayname)
  - [`isActive()`](#isactive)
  - [`isRightToLeft()`](#isrighttoleft)
  - [`isoCode()`](#isocode)
- [Other](#other)
  - [`apply()`](#apply)
  - [`locale()`](#locale)
  - [`nativeName()`](#nativename)
  - [`regionCode()`](#regioncode)
  - [`withLocale()`](#withlocale)

## Namespace

`OpenFGA`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Language.php)

## Implements

- `UnitEnum`
- `BackedEnum`

## Constants

| Name                  | Value   | Description                            |
| --------------------- | ------- | -------------------------------------- |
| `ChineseSimplified`   | `zh_CN` | Chinese Simplified language support.   |
| `Dutch`               | `nl`    | Dutch language support.                |
| `English`             | `en`    | English language support (default).    |
| `French`              | `fr`    | French language support.               |
| `German`              | `de`    | German language support.               |
| `Italian`             | `it`    | Italian language support.              |
| `Japanese`            | `ja`    | Japanese language support.             |
| `Korean`              | `ko`    | Korean language support.               |
| `PortugueseBrazilian` | `pt_BR` | Brazilian Portuguese language support. |
| `Russian`             | `ru`    | Russian language support.              |
| `Spanish`             | `es`    | Spanish language support.              |
| `Swedish`             | `sv`    | Swedish language support.              |
| `Turkish`             | `tr`    | Turkish language support.              |
| `Ukrainian`           | `uk`    | Ukrainian language support.            |

## Cases

| Name                  | Value   | Description                            |
| --------------------- | ------- | -------------------------------------- |
| `ChineseSimplified`   | `zh_CN` | Chinese Simplified language support.   |
| `Dutch`               | `nl`    | Dutch language support.                |
| `English`             | `en`    | English language support (default).    |
| `French`              | `fr`    | French language support.               |
| `German`              | `de`    | German language support.               |
| `Italian`             | `it`    | Italian language support.              |
| `Japanese`            | `ja`    | Japanese language support.             |
| `Korean`              | `ko`    | Korean language support.               |
| `PortugueseBrazilian` | `pt_BR` | Brazilian Portuguese language support. |
| `Russian`             | `ru`    | Russian language support.              |
| `Spanish`             | `es`    | Spanish language support.              |
| `Swedish`             | `sv`    | Swedish language support.              |
| `Turkish`             | `tr`    | Turkish language support.              |
| `Ukrainian`           | `uk`    | Ukrainian language support.            |

## Methods

### Utility

#### displayName

```php
public function displayName(): string

```

Get the display name of the language in English. Returns the English name of the language for UI display and documentation purposes.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Language.php#L150)

#### Returns

`string` — The English name of the language

#### isActive

```php
public function isActive(): bool

```

Check if this language is the currently active locale. Determines whether this language is currently set as the default locale for translation operations.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Language.php#L178)

#### Returns

`bool` — True if this is the active language

#### isRightToLeft

```php
public function isRightToLeft(): bool

```

Check if this language uses right-to-left text direction. Useful for UI implementations that need to adjust layout direction based on the selected language.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Language.php#L209)

#### Returns

`bool` — True if the language is RTL

#### isoCode

```php
public function isoCode(): string

```

Get the ISO 639-1 two-letter language code. Returns the base language code without region specifier. For regional variants, this returns the primary language code.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Language.php#L191)

#### Returns

`string` — The ISO 639-1 language code

### Other

#### apply

```php
public function apply(): void

```

Apply this language as the active translation locale. Sets this language as the default locale for all subsequent translation operations until changed or reset.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Language.php#L137)

#### Returns

`void`

#### locale

```php
public function locale(): string

```

Get the locale code for this language. Returns the full locale identifier including any region specifier (e.g., &quot;pt_BR&quot; for Brazilian Portuguese).

[View source](https://github.com/evansims/openfga-php/blob/main/src/Language.php#L224)

#### Returns

`string` — The locale code

#### nativeName

```php
public function nativeName(): string

```

Get the native name of the language. Returns the language name as written in that language, useful for language selection interfaces.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Language.php#L237)

#### Returns

`string` — The native language name

#### regionCode

```php
public function regionCode(): string|null

```

Get the region code if this is a regional language variant. Returns the ISO 3166-1 alpha-2 country code for regional language variants, or null for generic language codes.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Language.php#L265)

#### Returns

`string` &#124; `null` — The region code or null

#### withLocale

```php
public function withLocale(callable $callback): T

```

Execute a callback with this language as the active locale. Temporarily sets this language as the active locale, executes the provided callback, then restores the previous locale. This ensures proper cleanup even if the callback throws.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Language.php#L290)

#### Parameters

| Name        | Type       | Description |
| ----------- | ---------- | ----------- |
| `$callback` | `callable` |             |

#### Returns

`T` — The result of the callback
