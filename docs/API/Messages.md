# Messages

Centralized message keys for all exception messages in the OpenFGA PHP SDK. This enum provides type-safe access to all translatable message keys used throughout the library for exceptions, error messages, and user-facing text. Messages are organized by category and support parameter substitution for dynamic content through the translation system. All message keys map to translations in the translation files located in the translations/ directory, supporting multiple locales for internationalization.

## Namespace

`OpenFGA`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Messages.php)

## Implements

* `UnitEnum`
* `BackedEnum`

## Constants

| Name                                         | Value                                                            | Description |
| -------------------------------------------- | ---------------------------------------------------------------- | ----------- |
| `AUTH_ACCESS_TOKEN_MUST_BE_STRING`           | `auth.access_token_must_be_string`                               |             |
| `AUTH_ERROR_TOKEN_EXPIRED`                   | `exception.auth.token_expired`                                   |             |
| `AUTH_ERROR_TOKEN_INVALID`                   | `exception.auth.token_invalid`                                   |             |
| `AUTH_EXPIRES_IN_MUST_BE_INTEGER`            | `auth.expires_in_must_be_integer`                                |             |
| `AUTH_INVALID_RESPONSE_FORMAT`               | `auth.invalid_response_format`                                   |             |
| `AUTH_MISSING_REQUIRED_FIELDS`               | `auth.missing_required_fields`                                   |             |
| `AUTH_USER_MESSAGE_TOKEN_EXPIRED`            | `auth.user_message.token_expired`                                |             |
| `AUTH_USER_MESSAGE_TOKEN_INVALID`            | `auth.user_message.token_invalid`                                |             |
| `CLIENT_ERROR_AUTHENTICATION`                | `exception.client.authentication`                                |             |
| `CLIENT_ERROR_CONFIGURATION`                 | `exception.client.configuration`                                 |             |
| `CLIENT_ERROR_NETWORK`                       | `exception.client.network`                                       |             |
| `CLIENT_ERROR_SERIALIZATION`                 | `exception.client.serialization`                                 |             |
| `CLIENT_ERROR_VALIDATION`                    | `exception.client.validation`                                    |             |
| `COLLECTION_INVALID_ITEM_INSTANCE`           | `collection.invalid_item_instance`                               |             |
| `COLLECTION_INVALID_ITEM_TYPE_INTERFACE`     | `collection.invalid_item_type_interface`                         |             |
| `COLLECTION_INVALID_KEY_TYPE`                | `collection.invalid_key_type`                                    |             |
| `COLLECTION_INVALID_POSITION`                | `collection.invalid_position`                                    |             |
| `COLLECTION_INVALID_VALUE_TYPE`              | `collection.invalid_value_type`                                  |             |
| `COLLECTION_KEY_MUST_BE_STRING`              | `collection.key_must_be_string`                                  |             |
| `COLLECTION_UNDEFINED_ITEM_TYPE`             | `collection.undefined_item_type`                                 |             |
| `CONFIG_ERROR_HTTP_CLIENT_MISSING`           | `exception.config.http_client_missing`                           |             |
| `CONFIG_ERROR_HTTP_REQUEST_FACTORY_MISSING`  | `exception.config.http_request_factory_missing`                  |             |
| `CONFIG_ERROR_HTTP_RESPONSE_FACTORY_MISSING` | `exception.config.http_response_factory_missing`                 |             |
| `CONFIG_ERROR_HTTP_STREAM_FACTORY_MISSING`   | `exception.config.http_stream_factory_missing`                   |             |
| `CONSISTENCY_HIGHER_CONSISTENCY_DESCRIPTION` | `consistency.higher_consistency.description`                     |             |
| `CONSISTENCY_MINIMIZE_LATENCY_DESCRIPTION`   | `consistency.minimize_latency.description`                       |             |
| `CONSISTENCY_UNSPECIFIED_DESCRIPTION`        | `consistency.unspecified.description`                            |             |
| `DSL_INPUT_EMPTY`                            | `dsl.input_empty`                                                |             |
| `DSL_INVALID_COMPUTED_USERSET`               | `dsl.invalid_computed_userset`                                   |             |
| `DSL_PARSE_FAILED`                           | `dsl.parse_failed`                                               |             |
| `DSL_PATTERN_EMPTY`                          | `dsl.pattern_empty`                                              |             |
| `DSL_UNBALANCED_PARENTHESES_CLOSING`         | `dsl.unbalanced_parentheses_closing`                             |             |
| `DSL_UNBALANCED_PARENTHESES_OPENING`         | `dsl.unbalanced_parentheses_opening`                             |             |
| `DSL_UNRECOGNIZED_TERM`                      | `dsl.unrecognized_term`                                          |             |
| `INVALID_BATCH_CHECK_EMPTY`                  | `validation.batch_check_empty`                                   |             |
| `INVALID_CORRELATION_ID`                     | `validation.invalid_correlation_id`                              |             |
| `JWT_INVALID_AUDIENCE`                       | `auth.jwt.invalid_audience`                                      |             |
| `JWT_INVALID_FORMAT`                         | `auth.jwt.invalid_format`                                        |             |
| `JWT_INVALID_HEADER`                         | `auth.jwt.invalid_header`                                        |             |
| `JWT_INVALID_ISSUER`                         | `auth.jwt.invalid_issuer`                                        |             |
| `JWT_INVALID_PAYLOAD`                        | `auth.jwt.invalid_payload`                                       |             |
| `JWT_MISSING_REQUIRED_CLAIMS`                | `auth.jwt.missing_required_claims`                               |             |
| `JWT_TOKEN_EXPIRED`                          | `auth.jwt.token_expired`                                         |             |
| `JWT_TOKEN_NOT_YET_VALID`                    | `auth.jwt.token_not_yet_valid`                                   |             |
| `MODEL_INVALID_TUPLE_KEY`                    | `model.invalid_tuple_key`                                        |             |
| `MODEL_LEAF_MISSING_CONTENT`                 | `model.leaf_missing_content`                                     |             |
| `MODEL_SOURCE_INFO_FILE_EMPTY`               | `model.source_info_file_empty`                                   |             |
| `MODEL_TYPED_WILDCARD_TYPE_EMPTY`            | `model.typed_wildcard_type_empty`                                |             |
| `NETWORK_ERROR`                              | `network.error`                                                  |             |
| `NETWORK_ERROR_CONFLICT`                     | `exception.network.conflict`                                     |             |
| `NETWORK_ERROR_FORBIDDEN`                    | `exception.network.forbidden`                                    |             |
| `NETWORK_ERROR_INVALID`                      | `exception.network.invalid`                                      |             |
| `NETWORK_ERROR_REQUEST`                      | `exception.network.request`                                      |             |
| `NETWORK_ERROR_SERVER`                       | `exception.network.server`                                       |             |
| `NETWORK_ERROR_TIMEOUT`                      | `exception.network.timeout`                                      |             |
| `NETWORK_ERROR_UNAUTHENTICATED`              | `exception.network.unauthenticated`                              |             |
| `NETWORK_ERROR_UNDEFINED_ENDPOINT`           | `exception.network.undefined_endpoint`                           |             |
| `NETWORK_ERROR_UNEXPECTED`                   | `exception.network.unexpected`                                   |             |
| `NETWORK_UNEXPECTED_STATUS`                  | `network.unexpected_status`                                      |             |
| `NO_LAST_REQUEST_FOUND`                      | `client.no_last_request_found`                                   |             |
| `REQUEST_CONTINUATION_TOKEN_EMPTY`           | `request.continuation_token_empty`                               |             |
| `REQUEST_MODEL_ID_EMPTY`                     | `request.model_id_empty`                                         |             |
| `REQUEST_OBJECT_EMPTY`                       | `request.object_empty`                                           |             |
| `REQUEST_OBJECT_TYPE_EMPTY`                  | `request.object_type_empty`                                      |             |
| `REQUEST_PAGE_SIZE_INVALID`                  | `request.page_size_invalid`                                      |             |
| `REQUEST_RELATION_EMPTY`                     | `request.relation_empty`                                         |             |
| `REQUEST_STORE_ID_EMPTY`                     | `request.store_id_empty`                                         |             |
| `REQUEST_STORE_NAME_EMPTY`                   | `request.store_name_empty`                                       |             |
| `REQUEST_TYPE_EMPTY`                         | `request.type_empty`                                             |             |
| `REQUEST_USER_EMPTY`                         | `request.user_empty`                                             |             |
| `RESULT_FAILURE_NO_VALUE`                    | `result.failure_no_value`                                        |             |
| `RESULT_SUCCESS_NO_ERROR`                    | `result.success_no_error`                                        |             |
| `SCHEMA_CLASS_NOT_FOUND`                     | `schema.class_not_found`                                         |             |
| `SCHEMA_ITEM_TYPE_NOT_FOUND`                 | `schema.item_type_not_found`                                     |             |
| `SERIALIZATION_ERROR_COULD_NOT_ADD_ITEMS`    | `exception.serialization.could_not_add_items_to_collection`      |             |
| `SERIALIZATION_ERROR_EMPTY_COLLECTION`       | `exception.serialization.empty_collection`                       |             |
| `SERIALIZATION_ERROR_INVALID_ITEM_TYPE`      | `exception.serialization.invalid_item_type`                      |             |
| `SERIALIZATION_ERROR_MISSING_REQUIRED_PARAM` | `exception.serialization.missing_required_constructor_parameter` |             |
| `SERIALIZATION_ERROR_RESPONSE`               | `exception.serialization.response`                               |             |
| `SERIALIZATION_ERROR_UNDEFINED_ITEM_TYPE`    | `exception.serialization.undefined_item_type`                    |             |
| `TUPLE_OPERATION_DELETE_DESCRIPTION`         | `tuple_operation.delete.description`                             |             |
| `TUPLE_OPERATION_WRITE_DESCRIPTION`          | `tuple_operation.write.description`                              |             |

## Cases

| Name                                         | Value                                                            | Description |
| -------------------------------------------- | ---------------------------------------------------------------- | ----------- |
| `AUTH_ACCESS_TOKEN_MUST_BE_STRING`           | `auth.access_token_must_be_string`                               |             |
| `AUTH_ERROR_TOKEN_EXPIRED`                   | `exception.auth.token_expired`                                   |             |
| `AUTH_ERROR_TOKEN_INVALID`                   | `exception.auth.token_invalid`                                   |             |
| `AUTH_EXPIRES_IN_MUST_BE_INTEGER`            | `auth.expires_in_must_be_integer`                                |             |
| `AUTH_INVALID_RESPONSE_FORMAT`               | `auth.invalid_response_format`                                   |             |
| `AUTH_MISSING_REQUIRED_FIELDS`               | `auth.missing_required_fields`                                   |             |
| `AUTH_USER_MESSAGE_TOKEN_EXPIRED`            | `auth.user_message.token_expired`                                |             |
| `AUTH_USER_MESSAGE_TOKEN_INVALID`            | `auth.user_message.token_invalid`                                |             |
| `CLIENT_ERROR_AUTHENTICATION`                | `exception.client.authentication`                                |             |
| `CLIENT_ERROR_CONFIGURATION`                 | `exception.client.configuration`                                 |             |
| `CLIENT_ERROR_NETWORK`                       | `exception.client.network`                                       |             |
| `CLIENT_ERROR_SERIALIZATION`                 | `exception.client.serialization`                                 |             |
| `CLIENT_ERROR_VALIDATION`                    | `exception.client.validation`                                    |             |
| `COLLECTION_INVALID_ITEM_INSTANCE`           | `collection.invalid_item_instance`                               |             |
| `COLLECTION_INVALID_ITEM_TYPE_INTERFACE`     | `collection.invalid_item_type_interface`                         |             |
| `COLLECTION_INVALID_KEY_TYPE`                | `collection.invalid_key_type`                                    |             |
| `COLLECTION_INVALID_POSITION`                | `collection.invalid_position`                                    |             |
| `COLLECTION_INVALID_VALUE_TYPE`              | `collection.invalid_value_type`                                  |             |
| `COLLECTION_KEY_MUST_BE_STRING`              | `collection.key_must_be_string`                                  |             |
| `COLLECTION_UNDEFINED_ITEM_TYPE`             | `collection.undefined_item_type`                                 |             |
| `CONFIG_ERROR_HTTP_CLIENT_MISSING`           | `exception.config.http_client_missing`                           |             |
| `CONFIG_ERROR_HTTP_REQUEST_FACTORY_MISSING`  | `exception.config.http_request_factory_missing`                  |             |
| `CONFIG_ERROR_HTTP_RESPONSE_FACTORY_MISSING` | `exception.config.http_response_factory_missing`                 |             |
| `CONFIG_ERROR_HTTP_STREAM_FACTORY_MISSING`   | `exception.config.http_stream_factory_missing`                   |             |
| `CONSISTENCY_HIGHER_CONSISTENCY_DESCRIPTION` | `consistency.higher_consistency.description`                     |             |
| `CONSISTENCY_MINIMIZE_LATENCY_DESCRIPTION`   | `consistency.minimize_latency.description`                       |             |
| `CONSISTENCY_UNSPECIFIED_DESCRIPTION`        | `consistency.unspecified.description`                            |             |
| `DSL_INPUT_EMPTY`                            | `dsl.input_empty`                                                |             |
| `DSL_INVALID_COMPUTED_USERSET`               | `dsl.invalid_computed_userset`                                   |             |
| `DSL_PARSE_FAILED`                           | `dsl.parse_failed`                                               |             |
| `DSL_PATTERN_EMPTY`                          | `dsl.pattern_empty`                                              |             |
| `DSL_UNBALANCED_PARENTHESES_CLOSING`         | `dsl.unbalanced_parentheses_closing`                             |             |
| `DSL_UNBALANCED_PARENTHESES_OPENING`         | `dsl.unbalanced_parentheses_opening`                             |             |
| `DSL_UNRECOGNIZED_TERM`                      | `dsl.unrecognized_term`                                          |             |
| `INVALID_BATCH_CHECK_EMPTY`                  | `validation.batch_check_empty`                                   |             |
| `INVALID_CORRELATION_ID`                     | `validation.invalid_correlation_id`                              |             |
| `JWT_INVALID_AUDIENCE`                       | `auth.jwt.invalid_audience`                                      |             |
| `JWT_INVALID_FORMAT`                         | `auth.jwt.invalid_format`                                        |             |
| `JWT_INVALID_HEADER`                         | `auth.jwt.invalid_header`                                        |             |
| `JWT_INVALID_ISSUER`                         | `auth.jwt.invalid_issuer`                                        |             |
| `JWT_INVALID_PAYLOAD`                        | `auth.jwt.invalid_payload`                                       |             |
| `JWT_MISSING_REQUIRED_CLAIMS`                | `auth.jwt.missing_required_claims`                               |             |
| `JWT_TOKEN_EXPIRED`                          | `auth.jwt.token_expired`                                         |             |
| `JWT_TOKEN_NOT_YET_VALID`                    | `auth.jwt.token_not_yet_valid`                                   |             |
| `MODEL_INVALID_TUPLE_KEY`                    | `model.invalid_tuple_key`                                        |             |
| `MODEL_LEAF_MISSING_CONTENT`                 | `model.leaf_missing_content`                                     |             |
| `MODEL_SOURCE_INFO_FILE_EMPTY`               | `model.source_info_file_empty`                                   |             |
| `MODEL_TYPED_WILDCARD_TYPE_EMPTY`            | `model.typed_wildcard_type_empty`                                |             |
| `NETWORK_ERROR`                              | `network.error`                                                  |             |
| `NETWORK_ERROR_CONFLICT`                     | `exception.network.conflict`                                     |             |
| `NETWORK_ERROR_FORBIDDEN`                    | `exception.network.forbidden`                                    |             |
| `NETWORK_ERROR_INVALID`                      | `exception.network.invalid`                                      |             |
| `NETWORK_ERROR_REQUEST`                      | `exception.network.request`                                      |             |
| `NETWORK_ERROR_SERVER`                       | `exception.network.server`                                       |             |
| `NETWORK_ERROR_TIMEOUT`                      | `exception.network.timeout`                                      |             |
| `NETWORK_ERROR_UNAUTHENTICATED`              | `exception.network.unauthenticated`                              |             |
| `NETWORK_ERROR_UNDEFINED_ENDPOINT`           | `exception.network.undefined_endpoint`                           |             |
| `NETWORK_ERROR_UNEXPECTED`                   | `exception.network.unexpected`                                   |             |
| `NETWORK_UNEXPECTED_STATUS`                  | `network.unexpected_status`                                      |             |
| `NO_LAST_REQUEST_FOUND`                      | `client.no_last_request_found`                                   |             |
| `REQUEST_CONTINUATION_TOKEN_EMPTY`           | `request.continuation_token_empty`                               |             |
| `REQUEST_MODEL_ID_EMPTY`                     | `request.model_id_empty`                                         |             |
| `REQUEST_OBJECT_EMPTY`                       | `request.object_empty`                                           |             |
| `REQUEST_OBJECT_TYPE_EMPTY`                  | `request.object_type_empty`                                      |             |
| `REQUEST_PAGE_SIZE_INVALID`                  | `request.page_size_invalid`                                      |             |
| `REQUEST_RELATION_EMPTY`                     | `request.relation_empty`                                         |             |
| `REQUEST_STORE_ID_EMPTY`                     | `request.store_id_empty`                                         |             |
| `REQUEST_STORE_NAME_EMPTY`                   | `request.store_name_empty`                                       |             |
| `REQUEST_TYPE_EMPTY`                         | `request.type_empty`                                             |             |
| `REQUEST_USER_EMPTY`                         | `request.user_empty`                                             |             |
| `RESULT_FAILURE_NO_VALUE`                    | `result.failure_no_value`                                        |             |
| `RESULT_SUCCESS_NO_ERROR`                    | `result.success_no_error`                                        |             |
| `SCHEMA_CLASS_NOT_FOUND`                     | `schema.class_not_found`                                         |             |
| `SCHEMA_ITEM_TYPE_NOT_FOUND`                 | `schema.item_type_not_found`                                     |             |
| `SERIALIZATION_ERROR_COULD_NOT_ADD_ITEMS`    | `exception.serialization.could_not_add_items_to_collection`      |             |
| `SERIALIZATION_ERROR_EMPTY_COLLECTION`       | `exception.serialization.empty_collection`                       |             |
| `SERIALIZATION_ERROR_INVALID_ITEM_TYPE`      | `exception.serialization.invalid_item_type`                      |             |
| `SERIALIZATION_ERROR_MISSING_REQUIRED_PARAM` | `exception.serialization.missing_required_constructor_parameter` |             |
| `SERIALIZATION_ERROR_RESPONSE`               | `exception.serialization.response`                               |             |
| `SERIALIZATION_ERROR_UNDEFINED_ITEM_TYPE`    | `exception.serialization.undefined_item_type`                    |             |
| `TUPLE_OPERATION_DELETE_DESCRIPTION`         | `tuple_operation.delete.description`                             |             |
| `TUPLE_OPERATION_WRITE_DESCRIPTION`          | `tuple_operation.write.description`                              |             |

## Translation Tables

The following tables show all available translations for each message key used throughout the OpenFGA PHP SDK.

### `auth.access_token_must_be_string`

| Locale | Translation                      |
| ------ | -------------------------------- |
| `en`   | access_token must be a string    |
| `es`   | access_token debe ser una cadena |

### `exception.auth.token_expired`

| Locale | Translation                           |
| ------ | ------------------------------------- |
| `en`   | Authentication token has expired      |
| `es`   | El token de autenticación ha expirado |

### `exception.auth.token_invalid`

| Locale | Translation                           |
| ------ | ------------------------------------- |
| `en`   | Authentication token is invalid       |
| `es`   | El token de autenticación es inválido |

### `auth.expires_in_must_be_integer`

| Locale | Translation                   |
| ------ | ----------------------------- |
| `en`   | expires_in must be an integer |
| `es`   | expires_in debe ser un entero |

### `auth.invalid_response_format`

| Locale | Translation                   |
| ------ | ----------------------------- |
| `en`   | Invalid response format       |
| `es`   | Formato de respuesta inválido |

### `auth.missing_required_fields`

| Locale | Translation                              |
| ------ | ---------------------------------------- |
| `en`   | Missing required fields in response      |
| `es`   | Faltan campos requeridos en la respuesta |

### `auth.user_message.token_expired`

| Locale | Translation                                                 |
| ------ | ----------------------------------------------------------- |
| `en`   | Your session has expired. Please sign in again.             |
| `es`   | Su sesión ha expirado. Por favor, inicie sesión nuevamente. |

### `auth.user_message.token_invalid`

| Locale | Translation                                             |
| ------ | ------------------------------------------------------- |
| `en`   | Invalid authentication credentials provided.            |
| `es`   | Credenciales de autenticación inválidas proporcionadas. |

### `exception.client.authentication`

| Locale | Translation                   |
| ------ | ----------------------------- |
| `en`   | Authentication error occurred |
| `es`   | Error de autenticación        |

### `exception.client.configuration`

| Locale | Translation                      |
| ------ | -------------------------------- |
| `en`   | Configuration error detected     |
| `es`   | Error de configuración detectado |

### `exception.client.network`

| Locale | Translation                  |
| ------ | ---------------------------- |
| `en`   | Network communication error  |
| `es`   | Error de comunicación de red |

### `exception.client.serialization`

| Locale | Translation                     |
| ------ | ------------------------------- |
| `en`   | Data serialization error        |
| `es`   | Error de serialización de datos |

### `exception.client.validation`

| Locale | Translation                         |
| ------ | ----------------------------------- |
| `en`   | Request validation failed           |
| `es`   | La validación de la solicitud falló |

### `collection.invalid_item_instance`

| Locale | Translation                                                     |
| ------ | --------------------------------------------------------------- |
| `en`   | Expected instance of %expected%, %given% given                  |
| `es`   | Se esperaba una instancia de %expected%, se proporcionó %given% |

### `collection.invalid_item_type_interface`

| Locale | Translation                                                                        |
| ------ | ---------------------------------------------------------------------------------- |
| `en`   | Expected item type to implement %interface%, %given% given                         |
| `es`   | Se esperaba que el tipo de elemento implemente %interface%, se proporcionó %given% |

### `collection.invalid_key_type`

| Locale | Translation                                                         |
| ------ | ------------------------------------------------------------------- |
| `en`   | Invalid key type; expected string, %given% given.                   |
| `es`   | Tipo de clave inválido; se esperaba cadena, se proporcionó %given%. |

### `collection.invalid_position`

| Locale | Translation       |
| ------ | ----------------- |
| `en`   | Invalid position  |
| `es`   | Posición inválida |

### `collection.invalid_value_type`

| Locale | Translation                                                      |
| ------ | ---------------------------------------------------------------- |
| `en`   | Expected instance of %expected%, %given% given.                  |
| `es`   | Se esperaba una instancia de %expected%, se proporcionó %given%. |

### `collection.key_must_be_string`

| Locale | Translation                   |
| ------ | ----------------------------- |
| `en`   | Key must be a string.         |
| `es`   | La clave debe ser una cadena. |

### `collection.undefined_item_type`

| Locale | Translation                                                                                           |
| ------ | ----------------------------------------------------------------------------------------------------- |
| `en`   | Undefined item type for %class%. Define the $itemType property or override the constructor.           |
| `es`   | Tipo de elemento indefinido para %class%. Define la propiedad $itemType o sobrescribe el constructor. |

### `exception.config.http_client_missing`

| Locale | Translation                         |
| ------ | ----------------------------------- |
| `en`   | HTTP client is not configured       |
| `es`   | El cliente HTTP no está configurado |

### `exception.config.http_request_factory_missing`

| Locale | Translation                                        |
| ------ | -------------------------------------------------- |
| `en`   | HTTP request factory is not configured             |
| `es`   | La fábrica de solicitudes HTTP no está configurada |

### `exception.config.http_response_factory_missing`

| Locale | Translation                                       |
| ------ | ------------------------------------------------- |
| `en`   | HTTP response factory is not configured           |
| `es`   | La fábrica de respuestas HTTP no está configurada |

### `exception.config.http_stream_factory_missing`

| Locale | Translation                                    |
| ------ | ---------------------------------------------- |
| `en`   | HTTP stream factory is not configured          |
| `es`   | La fábrica de streams HTTP no está configurada |

### `consistency.higher_consistency.description`

| Locale | Translation                                                                                                     |
| ------ | --------------------------------------------------------------------------------------------------------------- |
| `en`   | Prioritizes data consistency over query performance, ensuring the most up-to-date results                       |
| `es`   | Prioriza la consistencia de datos sobre el rendimiento de consultas, asegurando los resultados más actualizados |

### `consistency.minimize_latency.description`

| Locale | Translation                                                                                                            |
| ------ | ---------------------------------------------------------------------------------------------------------------------- |
| `en`   | Prioritizes query performance over data consistency, potentially using slightly stale data                             |
| `es`   | Prioriza el rendimiento de consultas sobre la consistencia de datos, potencialmente usando datos ligeramente obsoletos |

### `consistency.unspecified.description`

| Locale | Translation                                                                                       |
| ------ | ------------------------------------------------------------------------------------------------- |
| `en`   | Uses the default consistency level determined by the OpenFGA server configuration                 |
| `es`   | Usa el nivel de consistencia predeterminado determinado por la configuración del servidor OpenFGA |

### `dsl.input_empty`

| Locale | Translation                               |
| ------ | ----------------------------------------- |
| `en`   | Input string cannot be empty              |
| `es`   | La cadena de entrada no puede estar vacía |

### `dsl.invalid_computed_userset`

| Locale | Translation                             |
| ------ | --------------------------------------- |
| `en`   | Invalid computed userset                |
| `es`   | Conjunto de usuarios calculado inválido |

### `dsl.parse_failed`

| Locale | Translation                        |
| ------ | ---------------------------------- |
| `en`   | Failed to parse DSL input          |
| `es`   | No se pudo analizar la entrada DSL |

### `dsl.pattern_empty`

| Locale | Translation                    |
| ------ | ------------------------------ |
| `en`   | Pattern cannot be empty        |
| `es`   | El patrón no puede estar vacío |

### `dsl.unbalanced_parentheses_closing`

| Locale | Translation                                                                           |
| ------ | ------------------------------------------------------------------------------------- |
| `en`   | Unbalanced parentheses: too many closing parentheses at position %position%           |
| `es`   | Paréntesis desequilibrados: demasiados paréntesis de cierre en la posición %position% |

### `dsl.unbalanced_parentheses_opening`

| Locale | Translation                                                              |
| ------ | ------------------------------------------------------------------------ |
| `en`   | Unbalanced parentheses: %count% unclosed opening %parentheses%           |
| `es`   | Paréntesis desequilibrados: %count% %parentheses% de apertura sin cerrar |

### `dsl.unrecognized_term`

| Locale | Translation                       |
| ------ | --------------------------------- |
| `en`   | Unrecognized DSL term: %term%     |
| `es`   | Término DSL no reconocido: %term% |

### `validation.batch_check_empty`

| Locale | Translation                                                 |
| ------ | ----------------------------------------------------------- |
| `en`   | Batch check request cannot be empty                         |
| `es`   | La solicitud de verificación por lotes no puede estar vacía |

### `validation.invalid_correlation_id`

| Locale | Translation                                                                                        |
| ------ | -------------------------------------------------------------------------------------------------- |
| `en`   | Correlation ID &quot;%correlationId%&quot; is invalid. Must match pattern: %pattern%               |
| `es`   | ID de correlación &quot;%correlationId%&quot; es inválido. Debe coincidir con el patrón: %pattern% |

### `auth.jwt.invalid_audience`

| Locale | Translation                                                      |
| ------ | ---------------------------------------------------------------- |
| `en`   | JWT token audience does not match expected audience              |
| `es`   | La audiencia del token JWT no coincide con la audiencia esperada |

### `auth.jwt.invalid_format`

| Locale | Translation                   |
| ------ | ----------------------------- |
| `en`   | Invalid JWT token format      |
| `es`   | Formato de token JWT inválido |

### `auth.jwt.invalid_header`

| Locale | Translation             |
| ------ | ----------------------- |
| `en`   | Invalid JWT header      |
| `es`   | Encabezado JWT inválido |

### `auth.jwt.invalid_issuer`

| Locale | Translation                                                |
| ------ | ---------------------------------------------------------- |
| `en`   | JWT token issuer does not match expected issuer            |
| `es`   | El emisor del token JWT no coincide con el emisor esperado |

### `auth.jwt.invalid_payload`

| Locale | Translation             |
| ------ | ----------------------- |
| `en`   | Invalid JWT payload     |
| `es`   | Carga útil JWT inválida |

### `auth.jwt.missing_required_claims`

| Locale | Translation                        |
| ------ | ---------------------------------- |
| `en`   | Missing required JWT claims        |
| `es`   | Faltan claims requeridos en el JWT |

### `auth.jwt.token_expired`

| Locale | Translation              |
| ------ | ------------------------ |
| `en`   | JWT token has expired    |
| `es`   | El token JWT ha expirado |

### `auth.jwt.token_not_yet_valid`

| Locale | Translation                   |
| ------ | ----------------------------- |
| `en`   | JWT token is not yet valid    |
| `es`   | El token JWT aún no es válido |

### `model.invalid_tuple_key`

| Locale | Translation                                             |
| ------ | ------------------------------------------------------- |
| `en`   | Invalid tuple_key provided to Assertion::fromArray      |
| `es`   | tuple_key inválido proporcionado a Assertion::fromArray |

### `model.leaf_missing_content`

| Locale | Translation                                                          |
| ------ | -------------------------------------------------------------------- |
| `en`   | Leaf must contain at least one of users, computed or tupleToUserset  |
| `es`   | Leaf debe contener al menos uno de: users, computed o tupleToUserset |

### `model.source_info_file_empty`

| Locale | Translation                             |
| ------ | --------------------------------------- |
| `en`   | SourceInfo::$file cannot be empty.      |
| `es`   | SourceInfo::$file no puede estar vacío. |

### `model.typed_wildcard_type_empty`

| Locale | Translation                                |
| ------ | ------------------------------------------ |
| `en`   | TypedWildcard::$type cannot be empty.      |
| `es`   | TypedWildcard::$type no puede estar vacío. |

### `network.error`

| Locale | Translation              |
| ------ | ------------------------ |
| `en`   | Network error: %message% |
| `es`   | Error de red: %message%  |

### `exception.network.conflict`

| Locale | Translation                                                           |
| ------ | --------------------------------------------------------------------- |
| `en`   | Conflict (409): The request conflicts with the current state          |
| `es`   | Conflicto (409): La solicitud entra en conflicto con el estado actual |

### `exception.network.forbidden`

| Locale | Translation                                              |
| ------ | -------------------------------------------------------- |
| `en`   | Forbidden (403): Access denied to the requested resource |
| `es`   | Prohibido (403): Acceso denegado al recurso solicitado   |

### `exception.network.invalid`

| Locale | Translation                                           |
| ------ | ----------------------------------------------------- |
| `en`   | Bad Request (400): The request is invalid             |
| `es`   | Solicitud incorrecta (400): La solicitud no es válida |

### `exception.network.request`

| Locale | Translation                                               |
| ------ | --------------------------------------------------------- |
| `en`   | Request failed: Unable to complete the HTTP request       |
| `es`   | Solicitud fallida: No se pudo completar la solicitud HTTP |

### `exception.network.server`

| Locale | Translation                                                     |
| ------ | --------------------------------------------------------------- |
| `en`   | Internal Server Error (500): The server encountered an error    |
| `es`   | Error interno del servidor (500): El servidor encontró un error |

### `exception.network.timeout`

| Locale | Translation                                                    |
| ------ | -------------------------------------------------------------- |
| `en`   | Unprocessable Entity (422): The request could not be processed |
| `es`   | Entidad no procesable (422): No se pudo procesar la solicitud  |

### `exception.network.unauthenticated`

| Locale | Translation                                    |
| ------ | ---------------------------------------------- |
| `en`   | Unauthorized (401): Authentication required    |
| `es`   | No autorizado (401): Se requiere autenticación |

### `exception.network.undefined_endpoint`

| Locale | Translation                                            |
| ------ | ------------------------------------------------------ |
| `en`   | Not Found (404): The requested endpoint does not exist |
| `es`   | No encontrado (404): El endpoint solicitado no existe  |

### `exception.network.unexpected`

| Locale | Translation                         |
| ------ | ----------------------------------- |
| `en`   | Unexpected response from the server |
| `es`   | Respuesta inesperada del servidor   |

### `network.unexpected_status`

| Locale | Translation                                                        |
| ------ | ------------------------------------------------------------------ |
| `en`   | API responded with an unexpected status code: %status_code%        |
| `es`   | La API respondió con un código de estado inesperado: %status_code% |

### `client.no_last_request_found`

| Locale | Translation                        |
| ------ | ---------------------------------- |
| `en`   | No last request found              |
| `es`   | No se encontró la última solicitud |

### `request.continuation_token_empty`

| Locale | Translation                                   |
| ------ | --------------------------------------------- |
| `en`   | Continuation token cannot be empty            |
| `es`   | El token de continuación no puede estar vacío |

### `request.model_id_empty`

| Locale | Translation                                           |
| ------ | ----------------------------------------------------- |
| `en`   | Authorization Model ID cannot be empty                |
| `es`   | El ID del modelo de autorización no puede estar vacío |

### `request.object_empty`

| Locale | Translation                    |
| ------ | ------------------------------ |
| `en`   | Object cannot be empty         |
| `es`   | El objeto no puede estar vacío |

### `request.object_type_empty`

| Locale | Translation                            |
| ------ | -------------------------------------- |
| `en`   | Object type cannot be empty            |
| `es`   | El tipo de objeto no puede estar vacío |

### `request.page_size_invalid`

| Locale | Translation                                   |
| ------ | --------------------------------------------- |
| `en`   | Invalid pageSize provided to %className%      |
| `es`   | pageSize inválido proporcionado a %className% |

### `request.relation_empty`

| Locale | Translation                      |
| ------ | -------------------------------- |
| `en`   | Relation cannot be empty         |
| `es`   | La relación no puede estar vacía |

### `request.store_id_empty`

| Locale | Translation                            |
| ------ | -------------------------------------- |
| `en`   | Store ID cannot be empty               |
| `es`   | El ID del almacén no puede estar vacío |

### `request.store_name_empty`

| Locale | Translation                                |
| ------ | ------------------------------------------ |
| `en`   | Store name cannot be empty                 |
| `es`   | El nombre del almacén no puede estar vacío |

### `request.type_empty`

| Locale | Translation                  |
| ------ | ---------------------------- |
| `en`   | Type cannot be empty         |
| `es`   | El tipo no puede estar vacío |

### `request.user_empty`

| Locale | Translation                     |
| ------ | ------------------------------- |
| `en`   | User cannot be empty            |
| `es`   | El usuario no puede estar vacío |

### `result.failure_no_value`

| Locale | Translation                         |
| ------ | ----------------------------------- |
| `en`   | Failure has no value                |
| `es`   | El resultado fallido no tiene valor |

### `result.success_no_error`

| Locale | Translation                         |
| ------ | ----------------------------------- |
| `en`   | Success has no error                |
| `es`   | El resultado exitoso no tiene error |

### `schema.class_not_found`

| Locale | Translation                                                                     |
| ------ | ------------------------------------------------------------------------------- |
| `en`   | Class &quot;%className%&quot; does not exist or cannot be autoloaded            |
| `es`   | La clase &quot;%className%&quot; no existe o no se puede cargar automáticamente |

### `schema.item_type_not_found`

| Locale | Translation                                                                               |
| ------ | ----------------------------------------------------------------------------------------- |
| `en`   | Item type &quot;%itemType%&quot; does not exist or cannot be autoloaded                   |
| `es`   | El tipo de elemento &quot;%itemType%&quot; no existe o no se puede cargar automáticamente |

### `exception.serialization.could_not_add_items_to_collection`

| Locale | Translation                                                 |
| ------ | ----------------------------------------------------------- |
| `en`   | Could not add items to collection %className%               |
| `es`   | No se pudieron agregar elementos a la colección %className% |

### `exception.serialization.empty_collection`

| Locale | Translation                       |
| ------ | --------------------------------- |
| `en`   | Collection cannot be empty        |
| `es`   | La colección no puede estar vacía |

### `exception.serialization.invalid_item_type`

| Locale | Translation                                                                                               |
| ------ | --------------------------------------------------------------------------------------------------------- |
| `en`   | Invalid item type for %property% in %className%: expected %expected%, got %actual_type%                   |
| `es`   | Tipo de elemento inválido para %property% en %className%: se esperaba %expected%, se obtuvo %actual_type% |

### `exception.serialization.missing_required_constructor_parameter`

| Locale | Translation                                                                                    |
| ------ | ---------------------------------------------------------------------------------------------- |
| `en`   | Missing required constructor parameter &quot;%paramName%&quot; for class %className%           |
| `es`   | Falta el parámetro requerido del constructor &quot;%paramName%&quot; para la clase %className% |

### `exception.serialization.response`

| Locale | Translation                                                   |
| ------ | ------------------------------------------------------------- |
| `en`   | Failed to serialize/deserialize response data                 |
| `es`   | No se pudieron serializar/deserializar los datos de respuesta |

### `exception.serialization.undefined_item_type`

| Locale | Translation                                           |
| ------ | ----------------------------------------------------- |
| `en`   | Item type is not defined for %className%              |
| `es`   | El tipo de elemento no está definido para %className% |

### `tuple_operation.delete.description`

| Locale | Translation                                                                            |
| ------ | -------------------------------------------------------------------------------------- |
| `en`   | Removes an existing relationship tuple, revoking permissions or removing relationships |
| `es`   | Elimina una tupla de relación existente, revocando permisos o eliminando relaciones    |

### `tuple_operation.write.description`

| Locale | Translation                                                                       |
| ------ | --------------------------------------------------------------------------------- |
| `en`   | Adds a new relationship tuple, granting permissions or establishing relationships |
| `es`   | Agrega una nueva tupla de relación, otorgando permisos o estableciendo relaciones |

## Methods

#### key

```php
public function key(): string

```

Get the translation key for this message.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Messages.php#L275)

#### Returns

`string`
