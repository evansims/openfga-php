# SerializationError

Serialization error types for the OpenFGA SDK. Defines specific serialization and data processing failures that can occur when converting between different data formats (JSON, objects, etc.) or when validating data structures. Each case provides a factory method to create the corresponding SerializationException. Serialization errors typically occur during data transformation between JSON and PHP objects, schema validation, or when processing API responses. These errors often indicate data format mismatches, missing required fields, or type conversion failures that prevent proper object construction.

## Namespace

`OpenFGA\Exceptions`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Exceptions/SerializationError.php)

## Implements

* `UnitEnum`
* `BackedEnum`

## Constants

| Name                                  | Value                                    | Description |
| ------------------------------------- | ---------------------------------------- | ----------- |
| `CouldNotAddItemsToCollection`        | `could_not_add_items_to_collection`      |             |
| `EmptyCollection`                     | `empty_collection`                       |             |
| `InvalidItemType`                     | `invalid_item_type`                      |             |
| `MissingRequiredConstructorParameter` | `missing_required_constructor_parameter` |             |
| `Response`                            | `response`                               |             |
| `UndefinedItemType`                   | `undefined_item_type`                    |             |

## Cases

| Name                                  | Value                                    | Description |
| ------------------------------------- | ---------------------------------------- | ----------- |
| `CouldNotAddItemsToCollection`        | `could_not_add_items_to_collection`      |             |
| `EmptyCollection`                     | `empty_collection`                       |             |
| `InvalidItemType`                     | `invalid_item_type`                      |             |
| `MissingRequiredConstructorParameter` | `missing_required_constructor_parameter` |             |
| `Response`                            | `response`                               |             |
| `UndefinedItemType`                   | `undefined_item_type`                    |             |

## Methods

### Utility

#### isCollectionError

```php
public function isCollectionError(): bool

```

Check if this serialization error is related to collection operations. Useful for identifying errors that occur during collection manipulation and providing appropriate error handling strategies.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Exceptions/SerializationError.php#L86)

#### Returns

`bool` — True if the error is collection-related, false otherwise

#### isTypeValidationError

```php
public function isTypeValidationError(): bool

```

Check if this serialization error indicates a data type validation failure. Useful for distinguishing between validation errors and structural errors during serialization processes.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Exceptions/SerializationError.php#L106)

#### Returns

`bool` — True if the error is type-related, false otherwise

### Other

#### exception

```php
public function exception(
    RequestInterface|null $request = NULL,
    ResponseInterface|null $response = NULL,
    array<string, mixed> $context = [],
    Throwable|null $prev = NULL,
): SerializationException

```

Create a new SerializationException for this error type. Factory method that creates a SerializationException instance with the current error type and provided context information. This provides a convenient way to generate typed exceptions with proper error categorization and rich debugging context for OpenFGA serialization failures. The exception will automatically capture the correct file and line location where this method was called (typically where `throw` occurs), ensuring debuggers show the actual throw location rather than this factory method.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Exceptions/SerializationError.php#L66)

#### Parameters

| Name        | Type                                                                | Description                                                                     |
| ----------- | ------------------------------------------------------------------- | ------------------------------------------------------------------------------- |
| `$request`  | [`RequestInterface`](Requests/RequestInterface.md) &#124; `null`    | The PSR-7 HTTP request being processed when serialization failed, if applicable |
| `$response` | [`ResponseInterface`](Responses/ResponseInterface.md) &#124; `null` | The PSR-7 HTTP response containing invalid data, if applicable                  |
| `$context`  | `array&lt;`string`, `mixed`&gt;`                                    |                                                                                 |
| `$prev`     | `Throwable` &#124; `null`                                           | The previous throwable used for exception chaining, if any                      |

#### Returns

[`SerializationException`](SerializationException.md) — The newly created SerializationException instance with comprehensive error context
