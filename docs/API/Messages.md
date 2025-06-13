# Messages

Centralized message keys for all exception messages in the OpenFGA PHP SDK. This enum provides type-safe access to all translatable message keys used throughout the library for exceptions, error messages, and user-facing text. Messages are organized by category and support parameter substitution for dynamic content through the translation system. All message keys map to translations in the translation files located in the translations/ directory, supporting multiple locales for internationalization.

## Table of Contents

* [Namespace](#namespace)
* [Source](#source)
* [Implements](#implements)
* [Constants](#constants)
* [Cases](#cases)
* [Translation Tables](#translation-tables)
* [Methods](#methods)

* [Other](#other)
    * [`key()`](#key)

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
| `ASSERTIONS_EMPTY_COLLECTION`                | `assertions.empty_collection`                                    |             |
| `ASSERTIONS_INVALID_TUPLE_KEY`               | `assertions.invalid_tuple_key`                                   |             |
| `AUTH_ACCESS_TOKEN_MUST_BE_STRING`           | `auth.access_token_must_be_string`                               |             |
| `AUTH_ERROR_TOKEN_EXPIRED`                   | `exception.auth.token_expired`                                   |             |
| `AUTH_ERROR_TOKEN_INVALID`                   | `exception.auth.token_invalid`                                   |             |
| `AUTH_EXPIRES_IN_MUST_BE_INTEGER`            | `auth.expires_in_must_be_integer`                                |             |
| `AUTH_INVALID_RESPONSE_FORMAT`               | `auth.invalid_response_format`                                   |             |
| `AUTH_MISSING_REQUIRED_FIELDS`               | `auth.missing_required_fields`                                   |             |
| `AUTH_USER_MESSAGE_TOKEN_EXPIRED`            | `auth.user_message.token_expired`                                |             |
| `AUTH_USER_MESSAGE_TOKEN_INVALID`            | `auth.user_message.token_invalid`                                |             |
| `BATCH_TUPLE_CHUNK_SIZE_EXCEEDED`            | `validation.batch_tuple_chunk_size_exceeded`                     |             |
| `BATCH_TUPLE_CHUNK_SIZE_POSITIVE`            | `validation.batch_tuple_chunk_size_positive`                     |             |
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
| `CONFIG_ERROR_INVALID_LANGUAGE`              | `exception.config.invalid_language`                              |             |
| `CONFIG_ERROR_INVALID_RETRY_COUNT`           | `exception.config.invalid_retry_count`                           |             |
| `CONFIG_ERROR_INVALID_URL`                   | `exception.config.invalid_url`                                   |             |
| `CONSISTENCY_HIGHER_CONSISTENCY_DESCRIPTION` | `consistency.higher_consistency.description`                     |             |
| `CONSISTENCY_MINIMIZE_LATENCY_DESCRIPTION`   | `consistency.minimize_latency.description`                       |             |
| `CONSISTENCY_UNSPECIFIED_DESCRIPTION`        | `consistency.unspecified.description`                            |             |
| `DSL_INPUT_EMPTY`                            | `dsl.input_empty`                                                |             |
| `DSL_INVALID_COMPUTED_USERSET`               | `dsl.invalid_computed_userset`                                   |             |
| `DSL_INVALID_COMPUTED_USERSET_RELATION`      | `dsl.invalid_computed_userset_relation`                          |             |
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
| `MODEL_DUPLICATE_TYPE`                       | `model.duplicate_type`                                           |             |
| `MODEL_INVALID_IDENTIFIER_FORMAT`            | `model.invalid_identifier_format`                                |             |
| `MODEL_INVALID_TUPLE_KEY`                    | `model.invalid_tuple_key`                                        |             |
| `MODEL_LEAF_MISSING_CONTENT`                 | `model.leaf_missing_content`                                     |             |
| `MODEL_NO_MODELS_IN_STORE`                   | `model.no_models_in_store`                                       |             |
| `MODEL_SOURCE_INFO_FILE_EMPTY`               | `model.source_info_file_empty`                                   |             |
| `MODEL_TYPE_DEFINITIONS_EMPTY`               | `model.type_definitions_empty`                                   |             |
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
| `REQUEST_TRANSACTIONAL_LIMIT_EXCEEDED`       | `request.transactional_limit_exceeded`                           |             |
| `REQUEST_TYPE_EMPTY`                         | `request.type_empty`                                             |             |
| `REQUEST_USER_EMPTY`                         | `request.user_empty`                                             |             |
| `RESPONSE_UNEXPECTED_TYPE`                   | `response.unexpected_type`                                       |             |
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
| `SERVICE_HTTP_NOT_AVAILABLE`                 | `service.http_not_available`                                     |             |
| `SERVICE_SCHEMA_VALIDATOR_NOT_AVAILABLE`     | `service.schema_validator_not_available`                         |             |
| `SERVICE_STORE_REPOSITORY_NOT_AVAILABLE`     | `service.store_repository_not_available`                         |             |
| `SERVICE_TUPLE_FILTER_NOT_AVAILABLE`         | `service.tuple_filter_not_available`                             |             |
| `SERVICE_TUPLE_REPOSITORY_NOT_AVAILABLE`     | `service.tuple_repository_not_available`                         |             |
| `STORE_NAME_REQUIRED`                        | `store.name_required`                                            |             |
| `STORE_NAME_TOO_LONG`                        | `store.name_too_long`                                            |             |
| `STORE_NOT_FOUND`                            | `store.not_found`                                                |             |
| `TRANSLATION_FILE_NOT_FOUND`                 | `translation.file_not_found`                                     |             |
| `TRANSLATION_UNSUPPORTED_FORMAT`             | `translation.unsupported_format`                                 |             |
| `TUPLE_OPERATION_DELETE_DESCRIPTION`         | `tuple_operation.delete.description`                             |             |
| `TUPLE_OPERATION_WRITE_DESCRIPTION`          | `tuple_operation.write.description`                              |             |
| `YAML_CANNOT_READ_FILE`                      | `yaml.cannot_read_file`                                          |             |
| `YAML_FILE_DOES_NOT_EXIST`                   | `yaml.file_does_not_exist`                                       |             |
| `YAML_INVALID_STRUCTURE`                     | `yaml.invalid_structure`                                         |             |
| `YAML_INVALID_SYNTAX_EMPTY_KEY`              | `yaml.invalid_syntax_empty_key`                                  |             |
| `YAML_INVALID_SYNTAX_MISSING_COLON`          | `yaml.invalid_syntax_missing_colon`                              |             |
| `YAML_INVALID_SYNTAX_MISSING_VALUE`          | `yaml.invalid_syntax_missing_value`                              |             |

## Cases

| Name                                         | Value                                                            | Description |
| -------------------------------------------- | ---------------------------------------------------------------- | ----------- |
| `ASSERTIONS_EMPTY_COLLECTION`                | `assertions.empty_collection`                                    |             |
| `ASSERTIONS_INVALID_TUPLE_KEY`               | `assertions.invalid_tuple_key`                                   |             |
| `AUTH_ACCESS_TOKEN_MUST_BE_STRING`           | `auth.access_token_must_be_string`                               |             |
| `AUTH_ERROR_TOKEN_EXPIRED`                   | `exception.auth.token_expired`                                   |             |
| `AUTH_ERROR_TOKEN_INVALID`                   | `exception.auth.token_invalid`                                   |             |
| `AUTH_EXPIRES_IN_MUST_BE_INTEGER`            | `auth.expires_in_must_be_integer`                                |             |
| `AUTH_INVALID_RESPONSE_FORMAT`               | `auth.invalid_response_format`                                   |             |
| `AUTH_MISSING_REQUIRED_FIELDS`               | `auth.missing_required_fields`                                   |             |
| `AUTH_USER_MESSAGE_TOKEN_EXPIRED`            | `auth.user_message.token_expired`                                |             |
| `AUTH_USER_MESSAGE_TOKEN_INVALID`            | `auth.user_message.token_invalid`                                |             |
| `BATCH_TUPLE_CHUNK_SIZE_EXCEEDED`            | `validation.batch_tuple_chunk_size_exceeded`                     |             |
| `BATCH_TUPLE_CHUNK_SIZE_POSITIVE`            | `validation.batch_tuple_chunk_size_positive`                     |             |
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
| `CONFIG_ERROR_INVALID_LANGUAGE`              | `exception.config.invalid_language`                              |             |
| `CONFIG_ERROR_INVALID_RETRY_COUNT`           | `exception.config.invalid_retry_count`                           |             |
| `CONFIG_ERROR_INVALID_URL`                   | `exception.config.invalid_url`                                   |             |
| `CONSISTENCY_HIGHER_CONSISTENCY_DESCRIPTION` | `consistency.higher_consistency.description`                     |             |
| `CONSISTENCY_MINIMIZE_LATENCY_DESCRIPTION`   | `consistency.minimize_latency.description`                       |             |
| `CONSISTENCY_UNSPECIFIED_DESCRIPTION`        | `consistency.unspecified.description`                            |             |
| `DSL_INPUT_EMPTY`                            | `dsl.input_empty`                                                |             |
| `DSL_INVALID_COMPUTED_USERSET`               | `dsl.invalid_computed_userset`                                   |             |
| `DSL_INVALID_COMPUTED_USERSET_RELATION`      | `dsl.invalid_computed_userset_relation`                          |             |
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
| `MODEL_DUPLICATE_TYPE`                       | `model.duplicate_type`                                           |             |
| `MODEL_INVALID_IDENTIFIER_FORMAT`            | `model.invalid_identifier_format`                                |             |
| `MODEL_INVALID_TUPLE_KEY`                    | `model.invalid_tuple_key`                                        |             |
| `MODEL_LEAF_MISSING_CONTENT`                 | `model.leaf_missing_content`                                     |             |
| `MODEL_NO_MODELS_IN_STORE`                   | `model.no_models_in_store`                                       |             |
| `MODEL_SOURCE_INFO_FILE_EMPTY`               | `model.source_info_file_empty`                                   |             |
| `MODEL_TYPE_DEFINITIONS_EMPTY`               | `model.type_definitions_empty`                                   |             |
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
| `REQUEST_TRANSACTIONAL_LIMIT_EXCEEDED`       | `request.transactional_limit_exceeded`                           |             |
| `REQUEST_TYPE_EMPTY`                         | `request.type_empty`                                             |             |
| `REQUEST_USER_EMPTY`                         | `request.user_empty`                                             |             |
| `RESPONSE_UNEXPECTED_TYPE`                   | `response.unexpected_type`                                       |             |
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
| `SERVICE_HTTP_NOT_AVAILABLE`                 | `service.http_not_available`                                     |             |
| `SERVICE_SCHEMA_VALIDATOR_NOT_AVAILABLE`     | `service.schema_validator_not_available`                         |             |
| `SERVICE_STORE_REPOSITORY_NOT_AVAILABLE`     | `service.store_repository_not_available`                         |             |
| `SERVICE_TUPLE_FILTER_NOT_AVAILABLE`         | `service.tuple_filter_not_available`                             |             |
| `SERVICE_TUPLE_REPOSITORY_NOT_AVAILABLE`     | `service.tuple_repository_not_available`                         |             |
| `STORE_NAME_REQUIRED`                        | `store.name_required`                                            |             |
| `STORE_NAME_TOO_LONG`                        | `store.name_too_long`                                            |             |
| `STORE_NOT_FOUND`                            | `store.not_found`                                                |             |
| `TRANSLATION_FILE_NOT_FOUND`                 | `translation.file_not_found`                                     |             |
| `TRANSLATION_UNSUPPORTED_FORMAT`             | `translation.unsupported_format`                                 |             |
| `TUPLE_OPERATION_DELETE_DESCRIPTION`         | `tuple_operation.delete.description`                             |             |
| `TUPLE_OPERATION_WRITE_DESCRIPTION`          | `tuple_operation.write.description`                              |             |
| `YAML_CANNOT_READ_FILE`                      | `yaml.cannot_read_file`                                          |             |
| `YAML_FILE_DOES_NOT_EXIST`                   | `yaml.file_does_not_exist`                                       |             |
| `YAML_INVALID_STRUCTURE`                     | `yaml.invalid_structure`                                         |             |
| `YAML_INVALID_SYNTAX_EMPTY_KEY`              | `yaml.invalid_syntax_empty_key`                                  |             |
| `YAML_INVALID_SYNTAX_MISSING_COLON`          | `yaml.invalid_syntax_missing_colon`                              |             |
| `YAML_INVALID_SYNTAX_MISSING_VALUE`          | `yaml.invalid_syntax_missing_value`                              |             |

## Translation Tables

The following tables show all available translations for each message key used throughout the OpenFGA PHP SDK.

### `assertions.empty_collection`

| Locale                 | Translation                                           |
| ---------------------- | ----------------------------------------------------- |
| German                 | Assertion-Sammlung darf nicht leer sein               |
| English                | Assertions collection cannot be empty                 |
| Spanish                | La colección de aserciones no puede estar vacía       |
| French                 | La collection d&#039;assertions ne peut pas être vide |
| Italian                | La collezione di asserzioni non può essere vuota      |
| Japanese               | アサーションコレクションを空にすることはできません    |
| Korean                 | 어설션 컬렉션은 비어있을 수 없습니다                  |
| Dutch                  | Assertieverzameling kan niet leeg zijn                |
| Portuguese (Brazilian) | Coleção de assertions não pode estar vazia            |
| Russian                | Коллекция утверждений не может быть пустой            |
| Swedish                | Påståendesamling kan inte vara tom                    |
| Turkish                | Onaylama koleksiyonu boş olamaz                       |
| Ukrainian              | Колекція тверджень не може бути порожньою             |
| Chinese (Simplified)   | 断言集合不能为空                                      |

### `assertions.invalid_tuple_key`

| Locale                 | Translation                                                                                                |
| ---------------------- | ---------------------------------------------------------------------------------------------------------- |
| German                 | Assertion enthält ungültigen Tupel-Schlüssel: Benutzer, Relation und Objekt sind erforderlich              |
| English                | Assertion contains invalid tuple key: user, relation, and object are required                              |
| Spanish                | La aserción contiene una clave de tupla inválida: se requieren usuario, relación y objeto                  |
| French                 | L&#039;assertion contient une clé de tuple invalide : utilisateur, relation et objet sont requis           |
| Italian                | L&#039;&#039;asserzione contiene una chiave tupla non valida: utente, relazione e oggetto sono obbligatori |
| Japanese               | アサーションに無効なタプルキーが含まれています: ユーザー、関係、オブジェクトが必須です                     |
| Korean                 | 어설션에 잘못된 튜플 키가 포함되어 있습니다: 사용자, 관계, 객체가 필요합니다                               |
| Dutch                  | Assertie bevat ongeldige tuple-sleutel: gebruiker, relatie en object zijn vereist                          |
| Portuguese (Brazilian) | Assertion contém chave de tupla inválida: usuário, relação e objeto são obrigatórios                       |
| Russian                | Утверждение содержит недопустимый ключ кортежа: требуются пользователь, отношение и объект                 |
| Swedish                | Påstående innehåller ogiltig tupel-nyckel: användare, relation och objekt krävs                            |
| Turkish                | Onaylama geçersiz tuple anahtarı içeriyor: kullanıcı, ilişki ve nesne gerekli                              |
| Ukrainian              | Твердження містить недійсний ключ кортежу: потрібні користувач, відношення та об&#039;єкт                  |
| Chinese (Simplified)   | 断言包含无效的元组键：用户、关系和对象是必需的                                                             |

### `auth.access_token_must_be_string`

| Locale                 | Translation                              |
| ---------------------- | ---------------------------------------- |
| German                 | access_token muss eine Zeichenkette sein |
| English                | access_token must be a string            |
| Spanish                | access_token debe ser una cadena         |
| French                 | access_token doit être une chaîne        |
| Italian                | access_token deve essere una stringa     |
| Japanese               | access_tokenは文字列である必要があります |
| Korean                 | access_token은 문자열이어야 합니다       |
| Dutch                  | access_token moet een string zijn        |
| Portuguese (Brazilian) | access_token deve ser uma string         |
| Russian                | access_token должен быть строкой         |
| Swedish                | access_token måste vara en sträng        |
| Turkish                | access_token bir dize olmalıdır          |
| Ukrainian              | access_token має бути рядком             |
| Chinese (Simplified)   | access_token必须是字符串                 |

### `exception.auth.token_expired`

| Locale                 | Translation                               |
| ---------------------- | ----------------------------------------- |
| German                 | Authentifizierungstoken ist abgelaufen    |
| English                | Authentication token has expired          |
| Spanish                | El token de autenticación ha expirado     |
| French                 | Le jeton d&#039;authentification a expiré |
| Italian                | Il token di autenticazione è scaduto      |
| Japanese               | 認証トークンの有効期限が切れました        |
| Korean                 | 인증 토큰이 만료되었습니다                |
| Dutch                  | Authenticatietoken is verlopen            |
| Portuguese (Brazilian) | Token de autenticação expirou             |
| Russian                | Токен аутентификации истек                |
| Swedish                | Autentiseringstoken har gått ut           |
| Turkish                | Kimlik doğrulama tokeni süresi doldu      |
| Ukrainian              | Токен автентифікації закінчився           |
| Chinese (Simplified)   | 身份验证令牌已过期                        |

### `exception.auth.token_invalid`

| Locale                 | Translation                                   |
| ---------------------- | --------------------------------------------- |
| German                 | Authentifizierungstoken ist ungültig          |
| English                | Authentication token is invalid               |
| Spanish                | El token de autenticación es inválido         |
| French                 | Le jeton d&#039;authentification est invalide |
| Italian                | Il token di autenticazione non è valido       |
| Japanese               | 認証トークンが無効です                        |
| Korean                 | 인증 토큰이 잘못되었습니다                    |
| Dutch                  | Authenticatietoken is ongeldig                |
| Portuguese (Brazilian) | Token de autenticação é inválido              |
| Russian                | Токен аутентификации недействителен           |
| Swedish                | Autentiseringstoken är ogiltig                |
| Turkish                | Kimlik doğrulama tokeni geçersiz              |
| Ukrainian              | Токен автентифікації недійсний                |
| Chinese (Simplified)   | 身份验证令牌无效                              |

### `auth.expires_in_must_be_integer`

| Locale                 | Translation                             |
| ---------------------- | --------------------------------------- |
| German                 | expires_in muss eine Ganzzahl sein      |
| English                | expires_in must be an integer           |
| Spanish                | expires_in debe ser un entero           |
| French                 | expires_in doit être un entier          |
| Italian                | expires_in deve essere un numero intero |
| Japanese               | expires_inは整数である必要があります    |
| Korean                 | expires_in은 정수여야 합니다            |
| Dutch                  | expires_in moet een geheel getal zijn   |
| Portuguese (Brazilian) | expires_in deve ser um inteiro          |
| Russian                | expires_in должен быть целым числом     |
| Swedish                | expires_in måste vara ett heltal        |
| Turkish                | expires_in bir tamsayı olmalıdır        |
| Ukrainian              | expires_in має бути цілим числом        |
| Chinese (Simplified)   | expires_in必须是整数                    |

### `auth.invalid_response_format`

| Locale                 | Translation                    |
| ---------------------- | ------------------------------ |
| German                 | Ungültiges Antwortformat       |
| English                | Invalid response format        |
| Spanish                | Formato de respuesta inválido  |
| French                 | Format de réponse invalide     |
| Italian                | Formato di risposta non valido |
| Japanese               | 無効なレスポンス形式です       |
| Korean                 | 잘못된 응답 형식               |
| Dutch                  | Ongeldig responsformaat        |
| Portuguese (Brazilian) | Formato de resposta inválido   |
| Russian                | Недопустимый формат ответа     |
| Swedish                | Ogiltigt svarsformat           |
| Turkish                | Geçersiz yanıt formatı         |
| Ukrainian              | Недійсний формат відповіді     |
| Chinese (Simplified)   | 无效的响应格式                 |

### `auth.missing_required_fields`

| Locale                 | Translation                                   |
| ---------------------- | --------------------------------------------- |
| German                 | Erforderliche Felder in der Antwort fehlen    |
| English                | Missing required fields in response           |
| Spanish                | Faltan campos requeridos en la respuesta      |
| French                 | Champs obligatoires manquants dans la réponse |
| Italian                | Campi obbligatori mancanti nella risposta     |
| Japanese               | レスポンスに必須フィールドがありません        |
| Korean                 | 응답에 필수 필드가 누락되었습니다             |
| Dutch                  | Ontbrekende vereiste velden in respons        |
| Portuguese (Brazilian) | Campos obrigatórios faltando na resposta      |
| Russian                | Отсутствуют обязательные поля в ответе        |
| Swedish                | Saknade obligatoriska fält i svar             |
| Turkish                | Yanıtta gerekli alanlar eksik                 |
| Ukrainian              | Відсутні обов&#039;язкові поля у відповіді    |
| Chinese (Simplified)   | 响应中缺少必需字段                            |

### `auth.user_message.token_expired`

| Locale                 | Translation                                                    |
| ---------------------- | -------------------------------------------------------------- |
| German                 | Ihre Sitzung ist abgelaufen. Bitte melden Sie sich erneut an.  |
| English                | Your session has expired. Please sign in again.                |
| Spanish                | Su sesión ha expirado. Por favor, inicie sesión nuevamente.    |
| French                 | Votre session a expiré. Veuillez vous reconnecter.             |
| Italian                | La tua sessione è scaduta. Per favore, accedi di nuovo.        |
| Japanese               | セッションの有効期限が切れました。再度サインインしてください。 |
| Korean                 | 세션이 만료되었습니다. 다시 로그인해 주세요.                   |
| Dutch                  | Uw sessie is verlopen. Log opnieuw in.                         |
| Portuguese (Brazilian) | Sua sessão expirou. Por favor, faça login novamente.           |
| Russian                | Ваша сессия истекла. Пожалуйста, войдите снова.                |
| Swedish                | Din session har gått ut. Vänligen logga in igen.               |
| Turkish                | Oturumunuzun süresi doldu. Lütfen tekrar giriş yapın.          |
| Ukrainian              | Ваша сесія закінчилася. Будь ласка, увійдіть знову.            |
| Chinese (Simplified)   | 您的会话已过期，请重新登录。                                   |

### `auth.user_message.token_invalid`

| Locale                 | Translation                                               |
| ---------------------- | --------------------------------------------------------- |
| German                 | Ungültige Authentifizierungsdaten bereitgestellt.         |
| English                | Invalid authentication credentials provided.              |
| Spanish                | Credenciales de autenticación inválidas proporcionadas.   |
| French                 | Identifiants d&#039;authentification invalides fournis.   |
| Italian                | Credenziali di autenticazione non valide fornite.         |
| Japanese               | 無効な認証資格情報が提供されました。                      |
| Korean                 | 잘못된 인증 자격 증명이 제공되었습니다.                   |
| Dutch                  | Ongeldige authenticatiegegevens verstrekt.                |
| Portuguese (Brazilian) | Credenciais de autenticação inválidas fornecidas.         |
| Russian                | Предоставлены недопустимые учетные данные аутентификации. |
| Swedish                | Ogiltiga autentiseringsuppgifter tillhandahållna.         |
| Turkish                | Geçersiz kimlik doğrulama bilgileri sağlandı.             |
| Ukrainian              | Надано недійсні облікові дані автентифікації.             |
| Chinese (Simplified)   | 提供的身份验证凭据无效。                                  |

### `validation.batch_tuple_chunk_size_exceeded`

| Locale                 | Translation                                         |
| ---------------------- | --------------------------------------------------- |
| German                 | Chunk-Größe darf %max_size% nicht überschreiten     |
| English                | Chunk size cannot exceed %max_size%                 |
| Spanish                | El tamaño del fragmento no puede exceder %max_size% |
| French                 | La taille du bloc ne peut pas dépasser %max_size%   |
| Italian                | La dimensione del chunk non può superare %max_size% |
| Japanese               | チャンクサイズは%max_size%を超えることはできません  |
| Korean                 | 청크 크기는 %max_size%를 초과할 수 없습니다         |
| Dutch                  | Chunkgrootte kan %max_size% niet overschrijden      |
| Portuguese (Brazilian) | Tamanho do chunk não pode exceder %max_size%        |
| Russian                | Размер блока не может превышать %max_size%          |
| Swedish                | Chunkstorlek kan inte överstiga %max_size%          |
| Turkish                | Parça boyutu %max_size% değerini aşamaz             |
| Ukrainian              | Розмір блоку не може перевищувати %max_size%        |
| Chinese (Simplified)   | 块大小不能超过%max_size%                            |

### `validation.batch_tuple_chunk_size_positive`

| Locale                 | Translation                                                   |
| ---------------------- | ------------------------------------------------------------- |
| German                 | Chunk-Größe muss eine positive Ganzzahl sein                  |
| English                | Chunk size must be a positive integer                         |
| Spanish                | El tamaño del fragmento debe ser un entero positivo           |
| French                 | La taille du bloc doit être un entier positif                 |
| Italian                | La dimensione del chunk deve essere un numero intero positivo |
| Japanese               | チャンクサイズは正の整数である必要があります                  |
| Korean                 | 청크 크기는 양의 정수여야 합니다                              |
| Dutch                  | Chunkgrootte moet een positief geheel getal zijn              |
| Portuguese (Brazilian) | Tamanho do chunk deve ser um inteiro positivo                 |
| Russian                | Размер блока должен быть положительным целым числом           |
| Swedish                | Chunkstorlek måste vara ett positivt heltal                   |
| Turkish                | Parça boyutu pozitif bir tamsayı olmalıdır                    |
| Ukrainian              | Розмір блоку має бути додатним цілим числом                   |
| Chinese (Simplified)   | 块大小必须是正整数                                            |

### `exception.client.authentication`

| Locale                 | Translation                                 |
| ---------------------- | ------------------------------------------- |
| German                 | Authentifizierungsfehler aufgetreten        |
| English                | Authentication error occurred               |
| Spanish                | Error de autenticación                      |
| French                 | Erreur d&#039;authentification survenue     |
| Italian                | Si è verificato un errore di autenticazione |
| Japanese               | 認証エラーが発生しました                    |
| Korean                 | 인증 오류가 발생했습니다                    |
| Dutch                  | Authenticatiefout opgetreden                |
| Portuguese (Brazilian) | Erro de autenticação ocorreu                |
| Russian                | Произошла ошибка аутентификации             |
| Swedish                | Autentiseringsfel inträffade                |
| Turkish                | Kimlik doğrulama hatası oluştu              |
| Ukrainian              | Сталася помилка автентифікації              |
| Chinese (Simplified)   | 发生身份验证错误                            |

### `exception.client.configuration`

| Locale                 | Translation                       |
| ---------------------- | --------------------------------- |
| German                 | Konfigurationsfehler erkannt      |
| English                | Configuration error detected      |
| Spanish                | Error de configuración detectado  |
| French                 | Erreur de configuration détectée  |
| Italian                | Rilevato errore di configurazione |
| Japanese               | 設定エラーが検出されました        |
| Korean                 | 구성 오류가 감지되었습니다        |
| Dutch                  | Configuratiefout gedetecteerd     |
| Portuguese (Brazilian) | Erro de configuração detectado    |
| Russian                | Обнаружена ошибка конфигурации    |
| Swedish                | Konfigurationsfel upptäckt        |
| Turkish                | Yapılandırma hatası tespit edildi |
| Ukrainian              | Виявлено помилку конфігурації     |
| Chinese (Simplified)   | 检测到配置错误                    |

### `exception.client.network`

| Locale                 | Translation                       |
| ---------------------- | --------------------------------- |
| German                 | Netzwerkkommunikationsfehler      |
| English                | Network communication error       |
| Spanish                | Error de comunicación de red      |
| French                 | Erreur de communication réseau    |
| Italian                | Errore di comunicazione di rete   |
| Japanese               | ネットワーク通信エラー            |
| Korean                 | 네트워크 통신 오류                |
| Dutch                  | Netwerkcommunicatiefout           |
| Portuguese (Brazilian) | Erro de comunicação de rede       |
| Russian                | Ошибка сетевого соединения        |
| Swedish                | Nätverkskommunikationsfel         |
| Turkish                | Ağ iletişim hatası                |
| Ukrainian              | Помилка мережевого з&#039;єднання |
| Chinese (Simplified)   | 网络通信错误                      |

### `exception.client.serialization`

| Locale                 | Translation                         |
| ---------------------- | ----------------------------------- |
| German                 | Datenserialisierungsfehler          |
| English                | Data serialization error            |
| Spanish                | Error de serialización de datos     |
| French                 | Erreur de sérialisation des données |
| Italian                | Errore di serializzazione dati      |
| Japanese               | データシリアライゼーションエラー    |
| Korean                 | 데이터 직렬화 오류                  |
| Dutch                  | Data serialisatiefout               |
| Portuguese (Brazilian) | Erro de serialização de dados       |
| Russian                | Ошибка сериализации данных          |
| Swedish                | Dataserialiseringsfel               |
| Turkish                | Veri serileştirme hatası            |
| Ukrainian              | Помилка серіалізації даних          |
| Chinese (Simplified)   | 数据序列化错误                      |

### `exception.client.validation`

| Locale                 | Translation                          |
| ---------------------- | ------------------------------------ |
| German                 | Anfragvalidierung fehlgeschlagen     |
| English                | Request validation failed            |
| Spanish                | La validación de la solicitud falló  |
| French                 | Échec de la validation de la requête |
| Italian                | Validazione della richiesta fallita  |
| Japanese               | リクエストの検証に失敗しました       |
| Korean                 | 요청 검증에 실패했습니다             |
| Dutch                  | Verzoekvalidatie mislukt             |
| Portuguese (Brazilian) | Validação de requisição falhou       |
| Russian                | Проверка запроса не удалась          |
| Swedish                | Begäranvalidering misslyckades       |
| Turkish                | İstek doğrulaması başarısız          |
| Ukrainian              | Перевірка запиту не вдалася          |
| Chinese (Simplified)   | 请求验证失败                         |

### `collection.invalid_item_instance`

| Locale                 | Translation                                                     |
| ---------------------- | --------------------------------------------------------------- |
| German                 | Erwartete Instanz von %expected%, %given% gegeben               |
| English                | Expected instance of %expected%, %given% given                  |
| Spanish                | Se esperaba una instancia de %expected%, se proporcionó %given% |
| French                 | Instance attendue de %expected%, %given% donné                  |
| Italian                | Attesa istanza di %expected%, fornito %given%                   |
| Japanese               | %expected%のインスタンスが期待されます。%given%が提供されました |
| Korean                 | %expected%의 인스턴스가 예상됩니다. %given%이 제공되었습니다    |
| Dutch                  | Verwacht instantie van %expected%, %given% gegeven              |
| Portuguese (Brazilian) | Esperada instância de %expected%, %given% fornecido             |
| Russian                | Ожидается экземпляр %expected%, предоставлено %given%           |
| Swedish                | Förväntad instans av %expected%, %given% given                  |
| Turkish                | %expected% örneği bekleniyor, %given% verildi                   |
| Ukrainian              | Очікується екземпляр %expected%, надано %given%                 |
| Chinese (Simplified)   | 期望%expected%的实例，提供了%given%                             |

### `collection.invalid_item_type_interface`

| Locale                 | Translation                                                                        |
| ---------------------- | ---------------------------------------------------------------------------------- |
| German                 | Erwarteter Elementtyp sollte %interface% implementieren, %given% gegeben           |
| English                | Expected item type to implement %interface%, %given% given                         |
| Spanish                | Se esperaba que el tipo de elemento implemente %interface%, se proporcionó %given% |
| French                 | Type d&#039;élément attendu pour implémenter %interface%, %given% donné            |
| Italian                | Il tipo di elemento dovrebbe implementare %interface%, fornito %given%             |
| Japanese               | アイテムタイプは%interface%を実装する必要があります。%given%が提供されました       |
| Korean                 | 항목 타입이 %interface%를 구현해야 합니다. %given%이 제공되었습니다                |
| Dutch                  | Verwacht itemtype om %interface% te implementeren, %given% gegeven                 |
| Portuguese (Brazilian) | Esperado tipo de item para implementar %interface%, %given% fornecido              |
| Russian                | Ожидается, что тип элемента реализует %interface%, предоставлено %given%           |
| Swedish                | Förväntad objekttyp att implementera %interface%, %given% given                    |
| Turkish                | Öğe türünün %interface% uygulaması bekleniyor, %given% verildi                     |
| Ukrainian              | Очікується, що тип елемента реалізує %interface%, надано %given%                   |
| Chinese (Simplified)   | 期望项目类型实现%interface%，提供了%given%                                         |

### `collection.invalid_key_type`

| Locale                 | Translation                                                           |
| ---------------------- | --------------------------------------------------------------------- |
| German                 | Ungültiger Schlüsseltyp; Zeichenkette erwartet, %given% gegeben.      |
| English                | Invalid key type; expected string, %given% given.                     |
| Spanish                | Tipo de clave inválido; se esperaba cadena, se proporcionó %given%.   |
| French                 | Type de clé invalide ; chaîne attendue, %given% donné.                |
| Italian                | Tipo di chiave non valido; attesa stringa, fornito %given%.           |
| Japanese               | 無効なキータイプです。文字列が期待されます。%given%が提供されました。 |
| Korean                 | 잘못된 키 타입; 문자열이 예상됩니다. %given%이 제공되었습니다.        |
| Dutch                  | Ongeldig sleuteltype; verwacht string, %given% gegeven.               |
| Portuguese (Brazilian) | Tipo de chave inválido; esperada string, %given% fornecido.           |
| Russian                | Недопустимый тип ключа; ожидается строка, предоставлено %given%.      |
| Swedish                | Ogiltig nyckeltyp; förväntad sträng, %given% given.                   |
| Turkish                | Geçersiz anahtar türü; dize bekleniyor, %given% verildi.              |
| Ukrainian              | Недійсний тип ключа; очікується рядок, надано %given%.                |
| Chinese (Simplified)   | 无效的键类型；期望字符串，提供了%given%。                             |

### `collection.invalid_position`

| Locale                 | Translation          |
| ---------------------- | -------------------- |
| German                 | Ungültige Position   |
| English                | Invalid position     |
| Spanish                | Posición inválida    |
| French                 | Position invalide    |
| Italian                | Posizione non valida |
| Japanese               | 無効な位置です       |
| Korean                 | 잘못된 위치          |
| Dutch                  | Ongeldige positie    |
| Portuguese (Brazilian) | Posição inválida     |
| Russian                | Недопустимая позиция |
| Swedish                | Ogiltig position     |
| Turkish                | Geçersiz konum       |
| Ukrainian              | Недійсна позиція     |
| Chinese (Simplified)   | 无效的位置           |

### `collection.invalid_value_type`

| Locale                 | Translation                                                       |
| ---------------------- | ----------------------------------------------------------------- |
| German                 | Erwartete Instanz von %expected%, %given% gegeben.                |
| English                | Expected instance of %expected%, %given% given.                   |
| Spanish                | Se esperaba una instancia de %expected%, se proporcionó %given%.  |
| French                 | Instance attendue de %expected%, %given% donné.                   |
| Italian                | Attesa istanza di %expected%, fornito %given%.                    |
| Japanese               | %expected%のインスタンスが期待されます。%given%が提供されました。 |
| Korean                 | %expected%의 인스턴스가 예상됩니다. %given%이 제공되었습니다.     |
| Dutch                  | Verwacht instantie van %expected%, %given% gegeven.               |
| Portuguese (Brazilian) | Esperada instância de %expected%, %given% fornecido.              |
| Russian                | Ожидается экземпляр %expected%, предоставлено %given%.            |
| Swedish                | Förväntad instans av %expected%, %given% given.                   |
| Turkish                | %expected% örneği bekleniyor, %given% verildi.                    |
| Ukrainian              | Очікується екземпляр %expected%, надано %given%.                  |
| Chinese (Simplified)   | 期望%expected%的实例，提供了%given%。                             |

### `collection.key_must_be_string`

| Locale                 | Translation                            |
| ---------------------- | -------------------------------------- |
| German                 | Schlüssel muss eine Zeichenkette sein. |
| English                | Key must be a string.                  |
| Spanish                | La clave debe ser una cadena.          |
| French                 | La clé doit être une chaîne.           |
| Italian                | La chiave deve essere una stringa.     |
| Japanese               | キーは文字列である必要があります。     |
| Korean                 | 키는 문자열이어야 합니다.              |
| Dutch                  | Sleutel moet een string zijn.          |
| Portuguese (Brazilian) | Chave deve ser uma string.             |
| Russian                | Ключ должен быть строкой.              |
| Swedish                | Nyckel måste vara en sträng.           |
| Turkish                | Anahtar bir dize olmalıdır.            |
| Ukrainian              | Ключ має бути рядком.                  |
| Chinese (Simplified)   | 键必须是字符串。                       |

### `collection.undefined_item_type`

| Locale                 | Translation                                                                                                            |
| ---------------------- | ---------------------------------------------------------------------------------------------------------------------- |
| German                 | Undefinierter Elementtyp für %class%. Definieren Sie die $itemType-Eigenschaft oder überschreiben Sie den Konstruktor. |
| English                | Undefined item type for %class%. Define the $itemType property or override the constructor.                            |
| Spanish                | Tipo de elemento indefinido para %class%. Define la propiedad $itemType o sobrescribe el constructor.                  |
| French                 | Type d&#039;élément non défini pour %class%. Définissez la propriété $itemType ou surchargez le constructeur.          |
| Italian                | Tipo di elemento non definito per %class%. Definire la proprietà $itemType o sovrascrivere il costruttore.             |
| Japanese               | %class%のアイテムタイプが未定義です。$itemTypeプロパティを定義するかコンストラクターをオーバーライドしてください。     |
| Korean                 | %class%의 항목 타입이 정의되지 않았습니다. $itemType 속성을 정의하거나 생성자를 재정의하세요.                          |
| Dutch                  | Ongedefinieerd itemtype voor %class%. Definieer de $itemType eigenschap of overschrijf de constructor.                 |
| Portuguese (Brazilian) | Tipo de item indefinido para %class%. Defina a propriedade $itemType ou sobrescreva o construtor.                      |
| Russian                | Неопределенный тип элемента для %class%. Определите свойство $itemType или переопределите конструктор.                 |
| Swedish                | Odefinierad objekttyp för %class%. Definiera $itemType-egenskapen eller åsidosätt konstruktorn.                        |
| Turkish                | %class% için tanımlanmamış öğe türü. $itemType özelliğini tanımlayın veya yapıcıyı geçersiz kılın.                     |
| Ukrainian              | Невизначений тип елемента для %class%. Визначте властивість $itemType або перевизначте конструктор.                    |
| Chinese (Simplified)   | %class%的项目类型未定义。请定义$itemType属性或覆盖构造函数。                                                           |

### `exception.config.http_client_missing`

| Locale                 | Translation                             |
| ---------------------- | --------------------------------------- |
| German                 | HTTP-Client ist nicht konfiguriert      |
| English                | HTTP client is not configured           |
| Spanish                | El cliente HTTP no está configurado     |
| French                 | Le client HTTP n&#039;est pas configuré |
| Italian                | Client HTTP non configurato             |
| Japanese               | HTTPクライアントが設定されていません    |
| Korean                 | HTTP 클라이언트가 구성되지 않았습니다   |
| Dutch                  | HTTP-client is niet geconfigureerd      |
| Portuguese (Brazilian) | Cliente HTTP não está configurado       |
| Russian                | HTTP клиент не настроен                 |
| Swedish                | HTTP-klient är inte konfigurerad        |
| Turkish                | HTTP istemci yapılandırılmamış          |
| Ukrainian              | HTTP клієнт не налаштований             |
| Chinese (Simplified)   | HTTP客户端未配置                        |

### `exception.config.http_request_factory_missing`

| Locale                 | Translation                                            |
| ---------------------- | ------------------------------------------------------ |
| German                 | HTTP-Request-Factory ist nicht konfiguriert            |
| English                | HTTP request factory is not configured                 |
| Spanish                | La fábrica de solicitudes HTTP no está configurada     |
| French                 | La fabrique de requêtes HTTP n&#039;est pas configurée |
| Italian                | Factory delle richieste HTTP non configurata           |
| Japanese               | HTTPリクエストファクトリが設定されていません           |
| Korean                 | HTTP 요청 팩토리가 구성되지 않았습니다                 |
| Dutch                  | HTTP-verzoek factory is niet geconfigureerd            |
| Portuguese (Brazilian) | Factory de requisição HTTP não está configurada        |
| Russian                | Фабрика HTTP запросов не настроена                     |
| Swedish                | HTTP-begäranfabrik är inte konfigurerad                |
| Turkish                | HTTP istek fabrikası yapılandırılmamış                 |
| Ukrainian              | Фабрика HTTP запитів не налаштована                    |
| Chinese (Simplified)   | HTTP请求工厂未配置                                     |

### `exception.config.http_response_factory_missing`

| Locale                 | Translation                                            |
| ---------------------- | ------------------------------------------------------ |
| German                 | HTTP-Response-Factory ist nicht konfiguriert           |
| English                | HTTP response factory is not configured                |
| Spanish                | La fábrica de respuestas HTTP no está configurada      |
| French                 | La fabrique de réponses HTTP n&#039;est pas configurée |
| Italian                | Factory delle risposte HTTP non configurata            |
| Japanese               | HTTPレスポンスファクトリが設定されていません           |
| Korean                 | HTTP 응답 팩토리가 구성되지 않았습니다                 |
| Dutch                  | HTTP-respons factory is niet geconfigureerd            |
| Portuguese (Brazilian) | Factory de resposta HTTP não está configurada          |
| Russian                | Фабрика HTTP ответов не настроена                      |
| Swedish                | HTTP-svarsfabrik är inte konfigurerad                  |
| Turkish                | HTTP yanıt fabrikası yapılandırılmamış                 |
| Ukrainian              | Фабрика HTTP відповідей не налаштована                 |
| Chinese (Simplified)   | HTTP响应工厂未配置                                     |

### `exception.config.http_stream_factory_missing`

| Locale                 | Translation                                        |
| ---------------------- | -------------------------------------------------- |
| German                 | HTTP-Stream-Factory ist nicht konfiguriert         |
| English                | HTTP stream factory is not configured              |
| Spanish                | La fábrica de streams HTTP no está configurada     |
| French                 | La fabrique de flux HTTP n&#039;est pas configurée |
| Italian                | Factory degli stream HTTP non configurata          |
| Japanese               | HTTPストリームファクトリが設定されていません       |
| Korean                 | HTTP 스트림 팩토리가 구성되지 않았습니다           |
| Dutch                  | HTTP-stream factory is niet geconfigureerd         |
| Portuguese (Brazilian) | Factory de stream HTTP não está configurada        |
| Russian                | Фабрика HTTP потоков не настроена                  |
| Swedish                | HTTP-strömfabrik är inte konfigurerad              |
| Turkish                | HTTP akış fabrikası yapılandırılmamış              |
| Ukrainian              | Фабрика HTTP потоків не налаштована                |
| Chinese (Simplified)   | HTTP流工厂未配置                                   |

### `exception.config.invalid_language`

| Locale                 | Translation                                         |
| ---------------------- | --------------------------------------------------- |
| German                 | Ungültiger Sprachcode bereitgestellt: %language%    |
| English                | Invalid language code provided: %language%          |
| Spanish                | Código de idioma inválido proporcionado: %language% |
| French                 | Code de langue invalide fourni : %language%         |
| Italian                | Codice lingua non valido fornito: %language%        |
| Japanese               | 無効な言語コードが提供されました: %language%        |
| Korean                 | 잘못된 언어 코드가 제공되었습니다: %language%       |
| Dutch                  | Ongeldige taalcode verstrekt: %language%            |
| Portuguese (Brazilian) | Código de idioma inválido fornecido: %language%     |
| Russian                | Предоставлен недопустимый код языка: %language%     |
| Swedish                | Ogiltig språkkod tillhandahållen: %language%        |
| Turkish                | Geçersiz dil kodu sağlandı: %language%              |
| Ukrainian              | Надано недійсний код мови: %language%               |
| Chinese (Simplified)   | 提供的语言代码无效：%language%                      |

### `exception.config.invalid_retry_count`

| Locale                 | Translation                                               |
| ---------------------- | --------------------------------------------------------- |
| German                 | Ungültige Wiederholungsanzahl bereitgestellt: %retries%   |
| English                | Invalid retry count provided: %retries%                   |
| Spanish                | Número de reintentos inválido proporcionado: %retries%    |
| French                 | Nombre de tentatives invalide fourni : %retries%          |
| Italian                | Numero di tentativi non valido fornito: %retries%         |
| Japanese               | 無効な再試行回数が提供されました: %retries%               |
| Korean                 | 잘못된 재시도 횟수가 제공되었습니다: %retries%            |
| Dutch                  | Ongeldig aantal herhalingen verstrekt: %retries%          |
| Portuguese (Brazilian) | Contagem de tentativas inválida fornecida: %retries%      |
| Russian                | Предоставлено недопустимое количество повторов: %retries% |
| Swedish                | Ogiltigt antal återförsök tillhandahållet: %retries%      |
| Turkish                | Geçersiz yeniden deneme sayısı sağlandı: %retries%        |
| Ukrainian              | Надано недійсну кількість повторів: %retries%             |
| Chinese (Simplified)   | 提供的重试次数无效：%retries%                             |

### `exception.config.invalid_url`

| Locale                 | Translation                          |
| ---------------------- | ------------------------------------ |
| German                 | Ungültige URL bereitgestellt: %url%  |
| English                | Invalid URL provided: %url%          |
| Spanish                | URL inválida proporcionada: %url%    |
| French                 | URL invalide fournie : %url%         |
| Italian                | URL non valido fornito: %url%        |
| Japanese               | 無効なURLが提供されました: %url%     |
| Korean                 | 잘못된 URL이 제공되었습니다: %url%   |
| Dutch                  | Ongeldige URL verstrekt: %url%       |
| Portuguese (Brazilian) | URL inválida fornecida: %url%        |
| Russian                | Предоставлен недопустимый URL: %url% |
| Swedish                | Ogiltig URL tillhandahållen: %url%   |
| Turkish                | Geçersiz URL sağlandı: %url%         |
| Ukrainian              | Надано недійсний URL: %url%          |
| Chinese (Simplified)   | 提供的URL无效：%url%                 |

### `consistency.higher_consistency.description`

| Locale                 | Translation                                                                                                              |
| ---------------------- | ------------------------------------------------------------------------------------------------------------------------ |
| German                 | Priorisiert Datenkonsistenz über Abfrageleistung und gewährleistet die aktuellsten Ergebnisse                            |
| English                | Prioritizes data consistency over query performance, ensuring the most up-to-date results                                |
| Spanish                | Prioriza la consistencia de datos sobre el rendimiento de consultas, asegurando los resultados más actualizados          |
| French                 | Privilégie la cohérence des données par rapport aux performances de requête, garantissant les résultats les plus récents |
| Italian                | Prioritizza la coerenza dei dati rispetto alle prestazioni delle query, garantendo i risultati più aggiornati            |
| Japanese               | クエリパフォーマンスよりもデータ整合性を優先し、最新の結果を保証します                                                   |
| Korean                 | 쿼리 성능보다 데이터 일관성을 우선시하여 가장 최신 결과를 보장합니다                                                     |
| Dutch                  | Geeft prioriteit aan dataconsistentie boven queryprestaties, zorgt voor de meest actuele resultaten                      |
| Portuguese (Brazilian) | Prioriza consistência de dados sobre performance de consulta, garantindo resultados mais atualizados                     |
| Russian                | Приоритизирует согласованность данных над производительностью запросов, обеспечивая самые актуальные результаты          |
| Swedish                | Prioriterar datakonsistens över frågeprestanda, säkerställer de mest uppdaterade resultaten                              |
| Turkish                | Sorgu performansından ziyade veri tutarlılığını önceleyerek en güncel sonuçları sağlar                                   |
| Ukrainian              | Пріоритизує узгодженість даних над продуктивністю запитів, забезпечуючи найновіші результати                             |
| Chinese (Simplified)   | 优先考虑数据一致性而非查询性能，确保最新的结果                                                                           |

### `consistency.minimize_latency.description`

| Locale                 | Translation                                                                                                                               |
| ---------------------- | ----------------------------------------------------------------------------------------------------------------------------------------- |
| German                 | Priorisiert Abfrageleistung über Datenkonsistenz, verwendet möglicherweise leicht veraltete Daten                                         |
| English                | Prioritizes query performance over data consistency, potentially using slightly stale data                                                |
| Spanish                | Prioriza el rendimiento de consultas sobre la consistencia de datos, potencialmente usando datos ligeramente obsoletos                    |
| French                 | Privilégie les performances de requête par rapport à la cohérence des données, utilisant potentiellement des données légèrement obsolètes |
| Italian                | Prioritizza le prestazioni delle query rispetto alla coerenza dei dati, potenzialmente utilizzando dati leggermente obsoleti              |
| Japanese               | データ整合性よりもクエリパフォーマンスを優先し、わずかに古いデータを使用する可能性があります                                              |
| Korean                 | 데이터 일관성보다 쿼리 성능을 우선시하여 약간 오래된 데이터를 사용할 수 있습니다                                                          |
| Dutch                  | Geeft prioriteit aan queryprestaties boven dataconsistentie, mogelijk met gebruik van enigszins verouderde data                           |
| Portuguese (Brazilian) | Prioriza performance de consulta sobre consistência de dados, potencialmente usando dados ligeiramente desatualizados                     |
| Russian                | Приоритизирует производительность запросов над согласованностью данных, потенциально используя слегка устаревшие данные                   |
| Swedish                | Prioriterar frågeprestanda över datakonsistens, potentiellt använder något föråldrad data                                                 |
| Turkish                | Veri tutarlılığından ziyade sorgu performansını önceleyerek potansiyel olarak biraz eski veri kullanır                                    |
| Ukrainian              | Пріоритизує продуктивність запитів над узгодженістю даних, потенційно використовуючи дещо застарілі дані                                  |
| Chinese (Simplified)   | 优先考虑查询性能而非数据一致性，可能使用稍旧的数据                                                                                        |

### `consistency.unspecified.description`

| Locale                 | Translation                                                                                       |
| ---------------------- | ------------------------------------------------------------------------------------------------- |
| German                 | Verwendet die Standard-Konsistenzebene, die durch die OpenFGA-Serverkonfiguration bestimmt wird   |
| English                | Uses the default consistency level determined by the OpenFGA server configuration                 |
| Spanish                | Usa el nivel de consistencia predeterminado determinado por la configuración del servidor OpenFGA |
| French                 | Utilise le niveau de cohérence par défaut déterminé par la configuration du serveur OpenFGA       |
| Italian                | Utilizza il livello di coerenza predefinito determinato dalla configurazione del server OpenFGA   |
| Japanese               | OpenFGAサーバー設定によって決定されるデフォルトの整合性レベルを使用します                         |
| Korean                 | OpenFGA 서버 구성에 의해 결정되는 기본 일관성 수준을 사용합니다                                   |
| Dutch                  | Gebruikt het standaard consistentieniveau bepaald door de OpenFGA-serverconfiguratie              |
| Portuguese (Brazilian) | Usa o nível de consistência padrão determinado pela configuração do servidor OpenFGA              |
| Russian                | Использует уровень согласованности по умолчанию, определяемый конфигурацией сервера OpenFGA       |
| Swedish                | Använder standardkonsistensnivån som bestäms av OpenFGA-serverkonfigurationen                     |
| Turkish                | OpenFGA sunucu yapılandırması tarafından belirlenen varsayılan tutarlılık seviyesini kullanır     |
| Ukrainian              | Використовує рівень узгодженості за замовчуванням, визначений конфігурацією сервера OpenFGA       |
| Chinese (Simplified)   | 使用由OpenFGA服务器配置确定的默认一致性级别                                                       |

### `dsl.input_empty`

| Locale                 | Translation                                   |
| ---------------------- | --------------------------------------------- |
| German                 | Eingabezeichenkette darf nicht leer sein      |
| English                | Input string cannot be empty                  |
| Spanish                | La cadena de entrada no puede estar vacía     |
| French                 | La chaîne d&#039;entrée ne peut pas être vide |
| Italian                | La stringa di input non può essere vuota      |
| Japanese               | 入力文字列を空にすることはできません          |
| Korean                 | 입력 문자열은 비어있을 수 없습니다            |
| Dutch                  | Invoerstring kan niet leeg zijn               |
| Portuguese (Brazilian) | String de entrada não pode estar vazia        |
| Russian                | Строка ввода не может быть пустой             |
| Swedish                | Inmatningssträngen kan inte vara tom          |
| Turkish                | Giriş dizesi boş olamaz                       |
| Ukrainian              | Рядок введення не може бути порожнім          |
| Chinese (Simplified)   | 输入字符串不能为空                            |

### `dsl.invalid_computed_userset`

| Locale                 | Translation                                   |
| ---------------------- | --------------------------------------------- |
| German                 | Ungültiges berechnetes Benutzerset            |
| English                | Invalid computed userset                      |
| Spanish                | Conjunto de usuarios calculado inválido       |
| French                 | Ensemble d&#039;utilisateurs calculé invalide |
| Italian                | Set di utenti calcolato non valido            |
| Japanese               | 無効な計算済みユーザーセットです              |
| Korean                 | 잘못된 계산된 사용자 집합                     |
| Dutch                  | Ongeldige berekende gebruikersset             |
| Portuguese (Brazilian) | Conjunto de usuários computado inválido       |
| Russian                | Недопустимый вычисленный набор пользователей  |
| Swedish                | Ogiltig beräknad användaruppsättning          |
| Turkish                | Geçersiz hesaplanmış kullanıcı kümesi         |
| Ukrainian              | Недійсний обчислений набір користувачів       |
| Chinese (Simplified)   | 无效的计算用户集                              |

### `dsl.invalid_computed_userset_relation`

| Locale                 | Translation                                                                       |
| ---------------------- | --------------------------------------------------------------------------------- |
| German                 | Berechnete Benutzerset-Relation darf nicht leer sein.                             |
| English                | Computed userset relation cannot be empty.                                        |
| Spanish                | La relación del userset computado no puede estar vacía.                           |
| French                 | La relation de l&#039;ensemble d&#039;utilisateurs calculé ne peut pas être vide. |
| Italian                | La relazione del set di utenti calcolato non può essere vuota.                    |
| Japanese               | 計算済みユーザーセットの関係を空にすることはできません。                          |
| Korean                 | 계산된 사용자 집합 관계는 비어있을 수 없습니다.                                   |
| Dutch                  | Berekende gebruikersset relatie kan niet leeg zijn.                               |
| Portuguese (Brazilian) | Relação do conjunto de usuários computado não pode estar vazia.                   |
| Russian                | Отношение вычисленного набора пользователей не может быть пустым.                 |
| Swedish                | Beräknad användaruppsättnings relation kan inte vara tom.                         |
| Turkish                | Hesaplanmış kullanıcı kümesi ilişkisi boş olamaz.                                 |
| Ukrainian              | Відношення обчисленого набору користувачів не може бути порожнім.                 |
| Chinese (Simplified)   | 计算用户集关系不能为空。                                                          |

### `dsl.parse_failed`

| Locale                 | Translation                                   |
| ---------------------- | --------------------------------------------- |
| German                 | DSL-Eingabe konnte nicht geparst werden       |
| English                | Failed to parse DSL input                     |
| Spanish                | No se pudo analizar la entrada DSL            |
| French                 | Échec de l&#039;analyse de l&#039;entrée DSL  |
| Italian                | Impossibile analizzare l&#039;&#039;input DSL |
| Japanese               | DSL入力の解析に失敗しました                   |
| Korean                 | DSL 입력 구문분석에 실패했습니다              |
| Dutch                  | Verwerken van DSL-invoer is mislukt           |
| Portuguese (Brazilian) | Falha ao analisar entrada DSL                 |
| Russian                | Не удалось разобрать ввод DSL                 |
| Swedish                | Misslyckades med att tolka DSL-inmatning      |
| Turkish                | DSL girişi ayrıştırılamadı                    |
| Ukrainian              | Не вдалося розібрати введення DSL             |
| Chinese (Simplified)   | 解析DSL输入失败                               |

### `dsl.pattern_empty`

| Locale                 | Translation                        |
| ---------------------- | ---------------------------------- |
| German                 | Muster darf nicht leer sein        |
| English                | Pattern cannot be empty            |
| Spanish                | El patrón no puede estar vacío     |
| French                 | Le modèle ne peut pas être vide    |
| Italian                | Il pattern non può essere vuoto    |
| Japanese               | パターンを空にすることはできません |
| Korean                 | 패턴은 비어있을 수 없습니다        |
| Dutch                  | Patroon kan niet leeg zijn         |
| Portuguese (Brazilian) | Padrão não pode estar vazio        |
| Russian                | Шаблон не может быть пустым        |
| Swedish                | Mönstret kan inte vara tomt        |
| Turkish                | Desen boş olamaz                   |
| Ukrainian              | Шаблон не може бути порожнім       |
| Chinese (Simplified)   | 模式不能为空                       |

### `dsl.unbalanced_parentheses_closing`

| Locale                 | Translation                                                                           |
| ---------------------- | ------------------------------------------------------------------------------------- |
| German                 | Unausgeglichene Klammern: zu viele schließende Klammern an Position %position%        |
| English                | Unbalanced parentheses: too many closing parentheses at position %position%           |
| Spanish                | Paréntesis desequilibrados: demasiados paréntesis de cierre en la posición %position% |
| French                 | Parenthèses déséquilibrées : trop de parenthèses fermantes à la position %position%   |
| Italian                | Parentesi non bilanciate: troppe parentesi di chiusura alla posizione %position%      |
| Japanese               | 括弧の対応が取れていません: 位置%position%に閉じ括弧が多すぎます                      |
| Korean                 | 불균형한 괄호: 위치 %position%에 닫는 괄호가 너무 많습니다                            |
| Dutch                  | Ongelijke haakjes: te veel sluithaakjes op positie %position%                         |
| Portuguese (Brazilian) | Parênteses desequilibrados: muitos parênteses de fechamento na posição %position%     |
| Russian                | Несбалансированные скобки: слишком много закрывающих скобок в позиции %position%      |
| Swedish                | Obalanserade parenteser: för många avslutande parenteser vid position %position%      |
| Turkish                | Dengesiz parantezler: %position% konumunda çok fazla kapanış parantezi                |
| Ukrainian              | Незбалансовані дужки: забагато закриваючих дужок у позиції %position%                 |
| Chinese (Simplified)   | 括号不匹配：位置%position%有过多的右括号                                              |

### `dsl.unbalanced_parentheses_opening`

| Locale                 | Translation                                                                  |
| ---------------------- | ---------------------------------------------------------------------------- |
| German                 | Unausgeglichene Klammern: %count% ungeschlossene öffnende %parentheses%      |
| English                | Unbalanced parentheses: %count% unclosed opening %parentheses%               |
| Spanish                | Paréntesis desequilibrados: %count% %parentheses% de apertura sin cerrar     |
| French                 | Parenthèses déséquilibrées : %count% %parentheses% ouvrantes non fermées     |
| Italian                | Parentesi non bilanciate: %count% %parentheses% di apertura non chiuse       |
| Japanese               | 括弧の対応が取れていません: %count%個の開き%parentheses%が閉じられていません |
| Korean                 | 불균형한 괄호: %count%개의 열린 %parentheses%가 닫히지 않았습니다            |
| Dutch                  | Ongelijke haakjes: %count% ongesloten %parentheses%                          |
| Portuguese (Brazilian) | Parênteses desequilibrados: %count% %parentheses% de abertura não fechados   |
| Russian                | Несбалансированные скобки: %count% незакрытых открывающих %parentheses%      |
| Swedish                | Obalanserade parenteser: %count% ostängda öppnande %parentheses%             |
| Turkish                | Dengesiz parantezler: %count% kapatılmamış açılış %parentheses%              |
| Ukrainian              | Незбалансовані дужки: %count% незакритих відкриваючих %parentheses%          |
| Chinese (Simplified)   | 括号不匹配：%count%个未关闭的%parentheses%                                   |

### `dsl.unrecognized_term`

| Locale                 | Translation                          |
| ---------------------- | ------------------------------------ |
| German                 | Unerkannter DSL-Begriff: %term%      |
| English                | Unrecognized DSL term: %term%        |
| Spanish                | Término DSL no reconocido: %term%    |
| French                 | Terme DSL non reconnu : %term%       |
| Italian                | Termine DSL non riconosciuto: %term% |
| Japanese               | 認識されないDSL用語です: %term%      |
| Korean                 | 인식되지 않은 DSL 용어: %term%       |
| Dutch                  | Onbekende DSL-term: %term%           |
| Portuguese (Brazilian) | Termo DSL não reconhecido: %term%    |
| Russian                | Нераспознанный термин DSL: %term%    |
| Swedish                | Okänd DSL-term: %term%               |
| Turkish                | Tanınmayan DSL terimi: %term%        |
| Ukrainian              | Нерозпізнаний термін DSL: %term%     |
| Chinese (Simplified)   | 无法识别的DSL术语：%term%            |

### `validation.batch_check_empty`

| Locale                 | Translation                                                 |
| ---------------------- | ----------------------------------------------------------- |
| German                 | Batch-Check-Anfrage darf nicht leer sein                    |
| English                | Batch check request cannot be empty                         |
| Spanish                | La solicitud de verificación por lotes no puede estar vacía |
| French                 | La requête de vérification par lot ne peut pas être vide    |
| Italian                | La richiesta di controllo batch non può essere vuota        |
| Japanese               | バッチチェックリクエストを空にすることはできません          |
| Korean                 | 배치 확인 요청은 비어있을 수 없습니다                       |
| Dutch                  | Batchcontrole verzoek kan niet leeg zijn                    |
| Portuguese (Brazilian) | Requisição de verificação em lote não pode estar vazia      |
| Russian                | Запрос пакетной проверки не может быть пустым               |
| Swedish                | Batch-kontrollförfrågan kan inte vara tom                   |
| Turkish                | Toplu kontrol isteği boş olamaz                             |
| Ukrainian              | Запит пакетної перевірки не може бути порожнім              |
| Chinese (Simplified)   | 批量检查请求不能为空                                        |

### `validation.invalid_correlation_id`

| Locale                 | Translation                                                                                                        |
| ---------------------- | ------------------------------------------------------------------------------------------------------------------ |
| German                 | Korrelations-ID &#039;%correlationId%&#039; ist ungültig. Muss dem Muster entsprechen: %pattern%                   |
| English                | Correlation ID &quot;%correlationId%&quot; is invalid. Must match pattern: %pattern%                               |
| Spanish                | ID de correlación &quot;%correlationId%&quot; es inválido. Debe coincidir con el patrón: %pattern%                 |
| French                 | L&#039;&#039;ID de corrélation &quot;%correlationId%&quot; est invalide. Doit correspondre au modèle : %pattern%   |
| Italian                | L&#039;&#039;ID di correlazione &quot;%correlationId%&quot; non è valido. Deve corrispondere al pattern: %pattern% |
| Japanese               | 相関ID「%correlationId%」は無効です。パターンと一致する必要があります: %pattern%                                   |
| Korean                 | 상관관계 ID &quot;%correlationId%&quot;가 잘못되었습니다. 패턴과 일치해야 합니다: %pattern%                        |
| Dutch                  | Correlatie-ID &quot;%correlationId%&quot; is ongeldig. Moet overeenkomen met patroon: %pattern%                    |
| Portuguese (Brazilian) | ID de correlação &quot;%correlationId%&quot; é inválido. Deve corresponder ao padrão: %pattern%                    |
| Russian                | ID корреляции &quot;%correlationId%&quot; недопустим. Должен соответствовать шаблону: %pattern%                    |
| Swedish                | Korrelations-ID &quot;%correlationId%&quot; är ogiltigt. Måste matcha mönster: %pattern%                           |
| Turkish                | Korelasyon ID &quot;%correlationId%&quot; geçersiz. Desene uymalı: %pattern%                                       |
| Ukrainian              | ID кореляції &quot;%correlationId%&quot; недійсний. Має відповідати шаблону: %pattern%                             |
| Chinese (Simplified)   | 关联ID&quot;%correlationId%&quot;无效。必须匹配模式：%pattern%                                                     |

### `auth.jwt.invalid_audience`

| Locale                 | Translation                                                                        |
| ---------------------- | ---------------------------------------------------------------------------------- |
| German                 | JWT-Token-Zielgruppe stimmt nicht mit erwarteter Zielgruppe überein                |
| English                | JWT token audience does not match expected audience                                |
| Spanish                | La audiencia del token JWT no coincide con la audiencia esperada                   |
| French                 | L&#039;audience du jeton JWT ne correspond pas à l&#039;audience attendue          |
| Italian                | L&#039;&#039;audience del token JWT non corrisponde all&#039;&#039;audience atteso |
| Japanese               | JWTトークンの対象者が期待される対象者と一致しません                                |
| Korean                 | JWT 토큰 대상이 예상 대상과 일치하지 않습니다                                      |
| Dutch                  | JWT-token doelgroep komt niet overeen met verwachte doelgroep                      |
| Portuguese (Brazilian) | Audiência do token JWT não corresponde à audiência esperada                        |
| Russian                | Аудитория JWT токена не соответствует ожидаемой аудитории                          |
| Swedish                | JWT-tokens målgrupp matchar inte förväntad målgrupp                                |
| Turkish                | JWT token hedef kitlesi beklenen hedef kitle ile eşleşmiyor                        |
| Ukrainian              | Аудиторія JWT токена не відповідає очікуваній аудиторії                            |
| Chinese (Simplified)   | JWT令牌受众与期望受众不匹配                                                        |

### `auth.jwt.invalid_format`

| Locale                 | Translation                    |
| ---------------------- | ------------------------------ |
| German                 | Ungültiges JWT-Token-Format    |
| English                | Invalid JWT token format       |
| Spanish                | Formato de token JWT inválido  |
| French                 | Format de jeton JWT invalide   |
| Italian                | Formato token JWT non valido   |
| Japanese               | 無効なJWTトークン形式です      |
| Korean                 | 잘못된 JWT 토큰 형식           |
| Dutch                  | Ongeldig JWT-tokenformaat      |
| Portuguese (Brazilian) | Formato de token JWT inválido  |
| Russian                | Недопустимый формат JWT токена |
| Swedish                | Ogiltigt JWT-tokenformat       |
| Turkish                | Geçersiz JWT token formatı     |
| Ukrainian              | Недійсний формат JWT токена    |
| Chinese (Simplified)   | 无效的JWT令牌格式              |

### `auth.jwt.invalid_header`

| Locale                 | Translation                |
| ---------------------- | -------------------------- |
| German                 | Ungültiger JWT-Header      |
| English                | Invalid JWT header         |
| Spanish                | Encabezado JWT inválido    |
| French                 | En-tête JWT invalide       |
| Italian                | Header JWT non valido      |
| Japanese               | 無効なJWTヘッダーです      |
| Korean                 | 잘못된 JWT 헤더            |
| Dutch                  | Ongeldige JWT-header       |
| Portuguese (Brazilian) | Cabeçalho JWT inválido     |
| Russian                | Недопустимый заголовок JWT |
| Swedish                | Ogiltig JWT-header         |
| Turkish                | Geçersiz JWT başlığı       |
| Ukrainian              | Недійсний заголовок JWT    |
| Chinese (Simplified)   | 无效的JWT标头              |

### `auth.jwt.invalid_issuer`

| Locale                 | Translation                                                                          |
| ---------------------- | ------------------------------------------------------------------------------------ |
| German                 | JWT-Token-Aussteller stimmt nicht mit erwartetem Aussteller überein                  |
| English                | JWT token issuer does not match expected issuer                                      |
| Spanish                | El emisor del token JWT no coincide con el emisor esperado                           |
| French                 | L&#039;émetteur du jeton JWT ne correspond pas à l&#039;émetteur attendu             |
| Italian                | L&#039;&#039;emittente del token JWT non corrisponde all&#039;&#039;emittente atteso |
| Japanese               | JWTトークンの発行者が期待される発行者と一致しません                                  |
| Korean                 | JWT 토큰 발급자가 예상 발급자와 일치하지 않습니다                                    |
| Dutch                  | JWT-token uitgever komt niet overeen met verwachte uitgever                          |
| Portuguese (Brazilian) | Emissor do token JWT não corresponde ao emissor esperado                             |
| Russian                | Издатель JWT токена не соответствует ожидаемому издателю                             |
| Swedish                | JWT-tokens utgivare matchar inte förväntad utgivare                                  |
| Turkish                | JWT token veren beklenen veren ile eşleşmiyor                                        |
| Ukrainian              | Видавець JWT токена не відповідає очікуваному видавцю                                |
| Chinese (Simplified)   | JWT令牌颁发者与期望颁发者不匹配                                                      |

### `auth.jwt.invalid_payload`

| Locale                 | Translation                        |
| ---------------------- | ---------------------------------- |
| German                 | Ungültige JWT-Nutzlast             |
| English                | Invalid JWT payload                |
| Spanish                | Carga útil JWT inválida            |
| French                 | Charge utile JWT invalide          |
| Italian                | Payload JWT non valido             |
| Japanese               | 無効なJWTペイロードです            |
| Korean                 | 잘못된 JWT 페이로드                |
| Dutch                  | Ongeldige JWT-payload              |
| Portuguese (Brazilian) | Payload JWT inválido               |
| Russian                | Недопустимая полезная нагрузка JWT |
| Swedish                | Ogiltig JWT-payload                |
| Turkish                | Geçersiz JWT yükü                  |
| Ukrainian              | Недійсне корисне навантаження JWT  |
| Chinese (Simplified)   | 无效的JWT有效负载                  |

### `auth.jwt.missing_required_claims`

| Locale                 | Translation                              |
| ---------------------- | ---------------------------------------- |
| German                 | Erforderliche JWT-Claims fehlen          |
| English                | Missing required JWT claims              |
| Spanish                | Faltan claims requeridos en el JWT       |
| French                 | Revendications JWT requises manquantes   |
| Italian                | Claims JWT obbligatori mancanti          |
| Japanese               | 必要なJWTクレームがありません            |
| Korean                 | 필수 JWT 클레임이 누락되었습니다         |
| Dutch                  | Ontbrekende vereiste JWT-claims          |
| Portuguese (Brazilian) | Claims JWT obrigatórios faltando         |
| Russian                | Отсутствуют обязательные утверждения JWT |
| Swedish                | Saknade obligatoriska JWT-anspråk        |
| Turkish                | Gerekli JWT talepleri eksik              |
| Ukrainian              | Відсутні обов&#039;язкові твердження JWT |
| Chinese (Simplified)   | 缺少必需的JWT声明                        |

### `auth.jwt.token_expired`

| Locale                 | Translation                       |
| ---------------------- | --------------------------------- |
| German                 | JWT-Token ist abgelaufen          |
| English                | JWT token has expired             |
| Spanish                | El token JWT ha expirado          |
| French                 | Le jeton JWT a expiré             |
| Italian                | Il token JWT è scaduto            |
| Japanese               | JWTトークンの有効期限が切れました |
| Korean                 | JWT 토큰이 만료되었습니다         |
| Dutch                  | JWT-token is verlopen             |
| Portuguese (Brazilian) | Token JWT expirou                 |
| Russian                | JWT токен истек                   |
| Swedish                | JWT-token har gått ut             |
| Turkish                | JWT token süresi doldu            |
| Ukrainian              | JWT токен закінчився              |
| Chinese (Simplified)   | JWT令牌已过期                     |

### `auth.jwt.token_not_yet_valid`

| Locale                 | Translation                               |
| ---------------------- | ----------------------------------------- |
| German                 | JWT-Token ist noch nicht gültig           |
| English                | JWT token is not yet valid                |
| Spanish                | El token JWT aún no es válido             |
| French                 | Le jeton JWT n&#039;est pas encore valide |
| Italian                | Il token JWT non è ancora valido          |
| Japanese               | JWTトークンはまだ有効ではありません       |
| Korean                 | JWT 토큰이 아직 유효하지 않습니다         |
| Dutch                  | JWT-token is nog niet geldig              |
| Portuguese (Brazilian) | Token JWT ainda não é válido              |
| Russian                | JWT токен еще не действителен             |
| Swedish                | JWT-token är inte giltigt ännu            |
| Turkish                | JWT token henüz geçerli değil             |
| Ukrainian              | JWT токен ще не дійсний                   |
| Chinese (Simplified)   | JWT令牌尚未生效                           |

### `model.duplicate_type`

| Locale                 | Translation                                          |
| ---------------------- | ---------------------------------------------------- |
| German                 | Doppelte Typdefinition gefunden: %type%              |
| English                | Duplicate type definition found: %type%              |
| Spanish                | Se encontró una definición de tipo duplicada: %type% |
| French                 | Définition de type dupliquée trouvée : %type%        |
| Italian                | Definizione di tipo duplicata trovata: %type%        |
| Japanese               | 重複するタイプ定義が見つかりました: %type%           |
| Korean                 | 중복된 타입 정의를 발견했습니다: %type%              |
| Dutch                  | Dubbele typedefinitie gevonden: %type%               |
| Portuguese (Brazilian) | Definição de tipo duplicada encontrada: %type%       |
| Russian                | Найдено дублирующееся определение типа: %type%       |
| Swedish                | Dubblerad typdefinition hittades: %type%             |
| Turkish                | Yinelenen tür tanımı bulundu: %type%                 |
| Ukrainian              | Знайдено дублікат визначення типу: %type%            |
| Chinese (Simplified)   | 发现重复的类型定义：%type%                           |

### `model.invalid_identifier_format`

| Locale                 | Translation                                                                                                              |
| ---------------------- | ------------------------------------------------------------------------------------------------------------------------ |
| German                 | Ungültiges Bezeichnerformat: Bezeichner dürfen keine Leerzeichen enthalten. Gefunden in %identifier%                     |
| English                | Invalid identifier format: identifiers cannot contain whitespace. Found in %identifier%                                  |
| Spanish                | Formato de identificador inválido: los identificadores no pueden contener espacios en blanco. Encontrado en %identifier% |
| French                 | Format d&#039;identifiant invalide : les identifiants ne peuvent pas contenir d&#039;espaces. Trouvé dans %identifier%   |
| Italian                | Formato identificatore non valido: gli identificatori non possono contenere spazi. Trovato in %identifier%               |
| Japanese               | 無効な識別子形式です: 識別子に空白文字を含めることはできません。%identifier%で見つかりました                             |
| Korean                 | 잘못된 식별자 형식: 식별자에는 공백이 포함될 수 없습니다. %identifier%에서 발견되었습니다                                |
| Dutch                  | Ongeldig identificatieformaat: identificaties kunnen geen witruimte bevatten. Gevonden in %identifier%                   |
| Portuguese (Brazilian) | Formato de identificador inválido: identificadores não podem conter espaços em branco. Encontrado em %identifier%        |
| Russian                | Недопустимый формат идентификатора: идентификаторы не могут содержать пробелы. Найдено в %identifier%                    |
| Swedish                | Ogiltigt identifierarformat: identifierare kan inte innehålla mellanslag. Hittades i %identifier%                        |
| Turkish                | Geçersiz tanımlayıcı formatı: tanımlayıcılar boşluk içeremez. %identifier% içinde bulundu                                |
| Ukrainian              | Недійсний формат ідентифікатора: ідентифікатори не можуть містити пробіли. Знайдено в %identifier%                       |
| Chinese (Simplified)   | 无效的标识符格式：标识符不能包含空白字符。在%identifier%中发现                                                           |

### `model.invalid_tuple_key`

| Locale                 | Translation                                                  |
| ---------------------- | ------------------------------------------------------------ |
| German                 | Ungültiger tuple_key für Assertion::fromArray bereitgestellt |
| English                | Invalid tuple_key provided to Assertion::fromArray           |
| Spanish                | tuple_key inválido proporcionado a Assertion::fromArray      |
| French                 | tuple_key invalide fourni à Assertion::fromArray             |
| Italian                | tuple_key non valido fornito ad Assertion::fromArray         |
| Japanese               | Assertion::fromArrayに無効なtuple_keyが提供されました        |
| Korean                 | Assertion::fromArray에 잘못된 tuple_key가 제공되었습니다     |
| Dutch                  | Ongeldige tuple_key verstrekt aan Assertion::fromArray       |
| Portuguese (Brazilian) | tuple_key inválido fornecido para Assertion::fromArray       |
| Russian                | Недопустимый tuple_key предоставлен для Assertion::fromArray |
| Swedish                | Ogiltig tuple_key tillhandahållen till Assertion::fromArray  |
| Turkish                | Assertion::fromArray için geçersiz tuple_key sağlandı        |
| Ukrainian              | Недійсний tuple_key надано для Assertion::fromArray          |
| Chinese (Simplified)   | 提供给Assertion::fromArray的tuple_key无效                    |

### `model.leaf_missing_content`

| Locale                 | Translation                                                                                    |
| ---------------------- | ---------------------------------------------------------------------------------------------- |
| German                 | Blatt muss mindestens eines von users, computed oder tupleToUserset enthalten                  |
| English                | Leaf must contain at least one of users, computed or tupleToUserset                            |
| Spanish                | Leaf debe contener al menos uno de: users, computed o tupleToUserset                           |
| French                 | Leaf doit contenir au moins un des éléments suivants : users, computed ou tupleToUserset       |
| Italian                | Leaf deve contenere almeno uno tra users, computed o tupleToUserset                            |
| Japanese               | LeafにはusersまたはcomputedまたはtupleToUsersetのうち少なくとも1つが含まれている必要があります |
| Korean                 | Leaf는 users, computed 또는 tupleToUserset 중 적어도 하나를 포함해야 합니다                    |
| Dutch                  | Leaf moet ten minste één van users, computed of tupleToUserset bevatten                        |
| Portuguese (Brazilian) | Leaf deve conter pelo menos um de users, computed ou tupleToUserset                            |
| Russian                | Leaf должен содержать хотя бы одно из users, computed или tupleToUserset                       |
| Swedish                | Leaf måste innehålla minst en av users, computed eller tupleToUserset                          |
| Turkish                | Leaf en az bir users, computed veya tupleToUserset içermelidir                                 |
| Ukrainian              | Leaf має містити принаймні одне з users, computed або tupleToUserset                           |
| Chinese (Simplified)   | Leaf必须包含users、computed或tupleToUserset中的至少一个                                        |

### `model.no_models_in_store`

| Locale                 | Translation                                                        |
| ---------------------- | ------------------------------------------------------------------ |
| German                 | Keine Autorisierungsmodelle in Store %store_id% gefunden           |
| English                | No authorization models found in store %store_id%                  |
| Spanish                | No se encontraron modelos de autorización en el almacén %store_id% |
| French                 | Aucun modèle d&#039;autorisation trouvé dans le magasin %store_id% |
| Italian                | Nessun modello di autorizzazione trovato nello store %store_id%    |
| Japanese               | ストア%store_id%に認可モデルが見つかりません                       |
| Korean                 | 스토어 %store_id%에서 인증 모델을 찾을 수 없습니다                 |
| Dutch                  | Geen autorisatiemodellen gevonden in store %store_id%              |
| Portuguese (Brazilian) | Nenhum modelo de autorização encontrado no store %store_id%        |
| Russian                | Модели авторизации не найдены в хранилище %store_id%               |
| Swedish                | Inga auktorisationsmodeller hittades i butik %store_id%            |
| Turkish                | %store_id% mağazasında yetkilendirme modeli bulunamadı             |
| Ukrainian              | Моделі авторизації не знайдені у сховищі %store_id%                |
| Chinese (Simplified)   | 在存储%store_id%中未找到授权模型                                   |

### `model.source_info_file_empty`

| Locale                 | Translation                                   |
| ---------------------- | --------------------------------------------- |
| German                 | SourceInfo::$file darf nicht leer sein.       |
| English                | SourceInfo::$file cannot be empty.            |
| Spanish                | SourceInfo::$file no puede estar vacío.       |
| French                 | SourceInfo::$file ne peut pas être vide.      |
| Italian                | SourceInfo::$file non può essere vuoto.       |
| Japanese               | SourceInfo::$fileを空にすることはできません。 |
| Korean                 | SourceInfo::$file은 비어있을 수 없습니다.     |
| Dutch                  | SourceInfo::$file kan niet leeg zijn.         |
| Portuguese (Brazilian) | SourceInfo::$file não pode estar vazio.       |
| Russian                | SourceInfo::$file не может быть пустым.       |
| Swedish                | SourceInfo::$file kan inte vara tom.          |
| Turkish                | SourceInfo::$file boş olamaz.                 |
| Ukrainian              | SourceInfo::$file не може бути порожнім.      |
| Chinese (Simplified)   | SourceInfo::$file不能为空。                   |

### `model.type_definitions_empty`

| Locale                 | Translation                                       |
| ---------------------- | ------------------------------------------------- |
| German                 | Typdefinitionen dürfen nicht leer sein            |
| English                | Type definitions cannot be empty                  |
| Spanish                | Las definiciones de tipo no pueden estar vacías   |
| French                 | Les définitions de type ne peuvent pas être vides |
| Italian                | Le definizioni di tipo non possono essere vuote   |
| Japanese               | タイプ定義を空にすることはできません              |
| Korean                 | 타입 정의는 비어있을 수 없습니다                  |
| Dutch                  | Typedefinities kunnen niet leeg zijn              |
| Portuguese (Brazilian) | Definições de tipo não podem estar vazias         |
| Russian                | Определения типов не могут быть пустыми           |
| Swedish                | Typdefinitioner kan inte vara tomma               |
| Turkish                | Tür tanımları boş olamaz                          |
| Ukrainian              | Визначення типів не можуть бути порожніми         |
| Chinese (Simplified)   | 类型定义不能为空                                  |

### `model.typed_wildcard_type_empty`

| Locale                 | Translation                                      |
| ---------------------- | ------------------------------------------------ |
| German                 | TypedWildcard::$type darf nicht leer sein.       |
| English                | TypedWildcard::$type cannot be empty.            |
| Spanish                | TypedWildcard::$type no puede estar vacío.       |
| French                 | TypedWildcard::$type ne peut pas être vide.      |
| Italian                | TypedWildcard::$type non può essere vuoto.       |
| Japanese               | TypedWildcard::$typeを空にすることはできません。 |
| Korean                 | TypedWildcard::$type은 비어있을 수 없습니다.     |
| Dutch                  | TypedWildcard::$type kan niet leeg zijn.         |
| Portuguese (Brazilian) | TypedWildcard::$type não pode estar vazio.       |
| Russian                | TypedWildcard::$type не может быть пустым.       |
| Swedish                | TypedWildcard::$type kan inte vara tom.          |
| Turkish                | TypedWildcard::$type boş olamaz.                 |
| Ukrainian              | TypedWildcard::$type не може бути порожнім.      |
| Chinese (Simplified)   | TypedWildcard::$type不能为空。                   |

### `network.error`

| Locale                 | Translation                   |
| ---------------------- | ----------------------------- |
| German                 | Netzwerkfehler: %message%     |
| English                | Network error: %message%      |
| Spanish                | Error de red: %message%       |
| French                 | Erreur réseau : %message%     |
| Italian                | Errore di rete: %message%     |
| Japanese               | ネットワークエラー: %message% |
| Korean                 | 네트워크 오류: %message%      |
| Dutch                  | Netwerkfout: %message%        |
| Portuguese (Brazilian) | Erro de rede: %message%       |
| Russian                | Сетевая ошибка: %message%     |
| Swedish                | Nätverksfel: %message%        |
| Turkish                | Ağ hatası: %message%          |
| Ukrainian              | Мережева помилка: %message%   |
| Chinese (Simplified)   | 网络错误：%message%           |

### `exception.network.conflict`

| Locale                 | Translation                                                             |
| ---------------------- | ----------------------------------------------------------------------- |
| German                 | Konflikt (409): Die Anfrage steht im Konflikt mit dem aktuellen Zustand |
| English                | Conflict (409): The request conflicts with the current state            |
| Spanish                | Conflicto (409): La solicitud entra en conflicto con el estado actual   |
| French                 | Conflit (409) : La requête entre en conflit avec l&#039;état actuel     |
| Italian                | Conflitto (409): La richiesta è in conflitto con lo stato attuale       |
| Japanese               | 競合 (409): リクエストが現在の状態と競合しています                      |
| Korean                 | 충돌 (409): 요청이 현재 상태와 충돌합니다                               |
| Dutch                  | Conflict (409): Het verzoek conflicteert met de huidige staat           |
| Portuguese (Brazilian) | Conflito (409): A requisição conflita com o estado atual                |
| Russian                | Конфликт (409): Запрос конфликтует с текущим состоянием                 |
| Swedish                | Konflikt (409): Begäran står i konflikt med nuvarande tillstånd         |
| Turkish                | Çakışma (409): İstek mevcut durumla çakışıyor                           |
| Ukrainian              | Конфлікт (409): Запит конфліктує з поточним станом                      |
| Chinese (Simplified)   | 冲突(409)：请求与当前状态冲突                                           |

### `exception.network.forbidden`

| Locale                 | Translation                                                       |
| ---------------------- | ----------------------------------------------------------------- |
| German                 | Verboten (403): Zugriff auf die angeforderte Ressource verweigert |
| English                | Forbidden (403): Access denied to the requested resource          |
| Spanish                | Prohibido (403): Acceso denegado al recurso solicitado            |
| French                 | Interdit (403) : Accès refusé à la ressource demandée             |
| Italian                | Vietato (403): Accesso negato alla risorsa richiesta              |
| Japanese               | 禁止 (403): 要求されたリソースへのアクセスが拒否されました        |
| Korean                 | 금지됨 (403): 요청된 리소스에 대한 액세스가 거부되었습니다        |
| Dutch                  | Verboden (403): Toegang geweigerd tot de gevraagde bron           |
| Portuguese (Brazilian) | Proibido (403): Acesso negado ao recurso solicitado               |
| Russian                | Запрещено (403): Доступ к запрашиваемому ресурсу запрещен         |
| Swedish                | Förbjuden (403): Åtkomst nekad till begärd resurs                 |
| Turkish                | Yasak (403): İstenen kaynağa erişim reddedildi                    |
| Ukrainian              | Заборонено (403): Доступ до запитуваного ресурсу заборонений      |
| Chinese (Simplified)   | 禁止(403)：拒绝访问请求的资源                                     |

### `exception.network.invalid`

| Locale                 | Translation                                           |
| ---------------------- | ----------------------------------------------------- |
| German                 | Ungültige Anfrage (400): Die Anfrage ist ungültig     |
| English                | Bad Request (400): The request is invalid             |
| Spanish                | Solicitud incorrecta (400): La solicitud no es válida |
| French                 | Requête incorrecte (400) : La requête est invalide    |
| Italian                | Richiesta non valida (400): La richiesta non è valida |
| Japanese               | 無効なリクエスト (400): リクエストが無効です          |
| Korean                 | 잘못된 요청 (400): 요청이 잘못되었습니다              |
| Dutch                  | Slecht Verzoek (400): Het verzoek is ongeldig         |
| Portuguese (Brazilian) | Requisição Inválida (400): A requisição é inválida    |
| Russian                | Неверный запрос (400): Запрос недействителен          |
| Swedish                | Dålig begäran (400): Begäran är ogiltig               |
| Turkish                | Hatalı İstek (400): İstek geçersiz                    |
| Ukrainian              | Невірний запит (400): Запит недійсний                 |
| Chinese (Simplified)   | 错误请求(400)：请求无效                               |

### `exception.network.request`

| Locale                 | Translation                                                            |
| ---------------------- | ---------------------------------------------------------------------- |
| German                 | Anfrage fehlgeschlagen: HTTP-Anfrage konnte nicht abgeschlossen werden |
| English                | Request failed: Unable to complete the HTTP request                    |
| Spanish                | Solicitud fallida: No se pudo completar la solicitud HTTP              |
| French                 | Échec de la requête : Impossible de terminer la requête HTTP           |
| Italian                | Richiesta fallita: Impossibile completare la richiesta HTTP            |
| Japanese               | リクエスト失敗: HTTPリクエストを完了できませんでした                   |
| Korean                 | 요청 실패: HTTP 요청을 완료할 수 없습니다                              |
| Dutch                  | Verzoek mislukt: Kan het HTTP-verzoek niet voltooien                   |
| Portuguese (Brazilian) | Requisição falhou: Não foi possível completar a requisição HTTP        |
| Russian                | Запрос не удался: Невозможно выполнить HTTP запрос                     |
| Swedish                | Begäran misslyckades: Kunde inte slutföra HTTP-begäran                 |
| Turkish                | İstek başarısız: HTTP isteği tamamlanamadı                             |
| Ukrainian              | Запит не вдався: Неможливо завершити HTTP запит                        |
| Chinese (Simplified)   | 请求失败：无法完成HTTP请求                                             |

### `exception.network.server`

| Locale                 | Translation                                                           |
| ---------------------- | --------------------------------------------------------------------- |
| German                 | Interner Serverfehler (500): Der Server ist auf einen Fehler gestoßen |
| English                | Internal Server Error (500): The server encountered an error          |
| Spanish                | Error interno del servidor (500): El servidor encontró un error       |
| French                 | Erreur interne du serveur (500) : Le serveur a rencontré une erreur   |
| Italian                | Errore interno del server (500): Il server ha incontrato un errore    |
| Japanese               | 内部サーバーエラー (500): サーバーでエラーが発生しました              |
| Korean                 | 내부 서버 오류 (500): 서버에서 오류가 발생했습니다                    |
| Dutch                  | Interne Serverfout (500): De server ondervond een fout                |
| Portuguese (Brazilian) | Erro Interno do Servidor (500): O servidor encontrou um erro          |
| Russian                | Внутренняя ошибка сервера (500): На сервере произошла ошибка          |
| Swedish                | Internt serverfel (500): Servern stötte på ett fel                    |
| Turkish                | İç Sunucu Hatası (500): Sunucuda hata oluştu                          |
| Ukrainian              | Внутрішня помилка сервера (500): На сервері сталася помилка           |
| Chinese (Simplified)   | 内部服务器错误(500)：服务器遇到错误                                   |

### `exception.network.timeout`

| Locale                 | Translation                                                                    |
| ---------------------- | ------------------------------------------------------------------------------ |
| German                 | Nicht verarbeitbare Entität (422): Die Anfrage konnte nicht verarbeitet werden |
| English                | Unprocessable Entity (422): The request could not be processed                 |
| Spanish                | Entidad no procesable (422): No se pudo procesar la solicitud                  |
| French                 | Entité non traitable (422) : La requête n&#039;a pas pu être traitée           |
| Italian                | Entità non processabile (422): La richiesta non può essere processata          |
| Japanese               | 処理不可能エンティティ (422): リクエストを処理できませんでした                 |
| Korean                 | 처리할 수 없는 엔티티 (422): 요청을 처리할 수 없습니다                         |
| Dutch                  | Onverwerkbare Entiteit (422): Het verzoek kon niet verwerkt worden             |
| Portuguese (Brazilian) | Entidade Não Processável (422): A requisição não pôde ser processada           |
| Russian                | Необработанная сущность (422): Запрос не может быть обработан                  |
| Swedish                | Obearbetbar entitet (422): Begäran kunde inte bearbetas                        |
| Turkish                | İşlenemeyen Varlık (422): İstek işlenemedi                                     |
| Ukrainian              | Необроблювана сутність (422): Запит не може бути оброблений                    |
| Chinese (Simplified)   | 无法处理的实体(422)：无法处理请求                                              |

### `exception.network.unauthenticated`

| Locale                 | Translation                                             |
| ---------------------- | ------------------------------------------------------- |
| German                 | Nicht autorisiert (401): Authentifizierung erforderlich |
| English                | Unauthorized (401): Authentication required             |
| Spanish                | No autorizado (401): Se requiere autenticación          |
| French                 | Non autorisé (401) : Authentification requise           |
| Italian                | Non autorizzato (401): Autenticazione richiesta         |
| Japanese               | 未認証 (401): 認証が必要です                            |
| Korean                 | 인증되지 않음 (401): 인증이 필요합니다                  |
| Dutch                  | Ongeautoriseerd (401): Authenticatie vereist            |
| Portuguese (Brazilian) | Não Autorizado (401): Autenticação necessária           |
| Russian                | Неавторизован (401): Требуется аутентификация           |
| Swedish                | Obehörig (401): Autentisering krävs                     |
| Turkish                | Yetkisiz (401): Kimlik doğrulama gerekli                |
| Ukrainian              | Неавторизований (401): Потрібна автентифікація          |
| Chinese (Simplified)   | 未授权(401)：需要身份验证                               |

### `exception.network.undefined_endpoint`

| Locale                 | Translation                                                          |
| ---------------------- | -------------------------------------------------------------------- |
| German                 | Nicht gefunden (404): Der angeforderte Endpunkt existiert nicht      |
| English                | Not Found (404): The requested endpoint does not exist               |
| Spanish                | No encontrado (404): El endpoint solicitado no existe                |
| French                 | Non trouvé (404) : Le point de terminaison demandé n&#039;existe pas |
| Italian                | Non trovato (404): L&#039;&#039;endpoint richiesto non esiste        |
| Japanese               | 見つかりません (404): 要求されたエンドポイントは存在しません         |
| Korean                 | 찾을 수 없음 (404): 요청된 엔드포인트가 존재하지 않습니다            |
| Dutch                  | Niet Gevonden (404): Het gevraagde eindpunt bestaat niet             |
| Portuguese (Brazilian) | Não Encontrado (404): O endpoint solicitado não existe               |
| Russian                | Не найдено (404): Запрашиваемая конечная точка не существует         |
| Swedish                | Inte hittad (404): Den begärda slutpunkten existerar inte            |
| Turkish                | Bulunamadı (404): İstenen uç nokta mevcut değil                      |
| Ukrainian              | Не знайдено (404): Запитувана кінцева точка не існує                 |
| Chinese (Simplified)   | 未找到(404)：请求的端点不存在                                        |

### `exception.network.unexpected`

| Locale                 | Translation                         |
| ---------------------- | ----------------------------------- |
| German                 | Unerwartete Antwort vom Server      |
| English                | Unexpected response from the server |
| Spanish                | Respuesta inesperada del servidor   |
| French                 | Réponse inattendue du serveur       |
| Italian                | Risposta inaspettata dal server     |
| Japanese               | サーバーからの予期しないレスポンス  |
| Korean                 | 서버로부터 예상치 못한 응답         |
| Dutch                  | Onverwachte respons van de server   |
| Portuguese (Brazilian) | Resposta inesperada do servidor     |
| Russian                | Неожиданный ответ от сервера        |
| Swedish                | Oväntat svar från servern           |
| Turkish                | Sunucudan beklenmeyen yanıt         |
| Ukrainian              | Неочікувана відповідь від сервера   |
| Chinese (Simplified)   | 来自服务器的意外响应                |

### `network.unexpected_status`

| Locale                 | Translation                                                                    |
| ---------------------- | ------------------------------------------------------------------------------ |
| German                 | API antwortete mit einem unerwarteten Statuscode: %status_code%                |
| English                | API responded with an unexpected status code: %status_code%                    |
| Spanish                | La API respondió con un código de estado inesperado: %status_code%             |
| French                 | L&#039;API a répondu avec un code de statut inattendu : %status_code%          |
| Italian                | L&#039;&#039;API ha risposto con un codice di stato inaspettato: %status_code% |
| Japanese               | APIが予期しないステータスコードで応答しました: %status_code%                   |
| Korean                 | API가 예상치 못한 상태 코드로 응답했습니다: %status_code%                      |
| Dutch                  | API reageerde met een onverwachte statuscode: %status_code%                    |
| Portuguese (Brazilian) | API respondeu com código de status inesperado: %status_code%                   |
| Russian                | API ответил неожиданным кодом состояния: %status_code%                         |
| Swedish                | API svarade med en oväntad statuskod: %status_code%                            |
| Turkish                | API beklenmeyen durum koduyla yanıt verdi: %status_code%                       |
| Ukrainian              | API відповів неочікуваним кодом стану: %status_code%                           |
| Chinese (Simplified)   | API返回了意外的状态代码：%status_code%                                         |

### `client.no_last_request_found`

| Locale                 | Translation                          |
| ---------------------- | ------------------------------------ |
| German                 | Keine letzte Anfrage gefunden        |
| English                | No last request found                |
| Spanish                | No se encontró la última solicitud   |
| French                 | Aucune dernière requête trouvée      |
| Italian                | Nessuna ultima richiesta trovata     |
| Japanese               | 最後のリクエストが見つかりません     |
| Korean                 | 마지막 요청을 찾을 수 없습니다       |
| Dutch                  | Geen laatste verzoek gevonden        |
| Portuguese (Brazilian) | Nenhuma última requisição encontrada |
| Russian                | Последний запрос не найден           |
| Swedish                | Ingen senaste förfrågan hittades     |
| Turkish                | Son istek bulunamadı                 |
| Ukrainian              | Останній запит не знайдено           |
| Chinese (Simplified)   | 未找到最后的请求                     |

### `request.continuation_token_empty`

| Locale                 | Translation                                    |
| ---------------------- | ---------------------------------------------- |
| German                 | Fortsetzungstoken darf nicht leer sein         |
| English                | Continuation token cannot be empty             |
| Spanish                | El token de continuación no puede estar vacío  |
| French                 | Le jeton de continuation ne peut pas être vide |
| Italian                | Il token di continuazione non può essere vuoto |
| Japanese               | 継続トークンを空にすることはできません         |
| Korean                 | 연속 토큰은 비어있을 수 없습니다               |
| Dutch                  | Vervolgtoken kan niet leeg zijn                |
| Portuguese (Brazilian) | Token de continuação não pode estar vazio      |
| Russian                | Токен продолжения не может быть пустым         |
| Swedish                | Fortsättningstoken kan inte vara tomt          |
| Turkish                | Devam tokeni boş olamaz                        |
| Ukrainian              | Токен продовження не може бути порожнім        |
| Chinese (Simplified)   | 继续令牌不能为空                               |

### `request.model_id_empty`

| Locale                 | Translation                                                        |
| ---------------------- | ------------------------------------------------------------------ |
| German                 | Autorisierungsmodell-ID darf nicht leer sein                       |
| English                | Authorization Model ID cannot be empty                             |
| Spanish                | El ID del modelo de autorización no puede estar vacío              |
| French                 | L&#039;ID du modèle d&#039;autorisation ne peut pas être vide      |
| Italian                | L&#039;&#039;ID del modello di autorizzazione non può essere vuoto |
| Japanese               | 認可モデルIDを空にすることはできません                             |
| Korean                 | 인증 모델 ID는 비어있을 수 없습니다                                |
| Dutch                  | Autorisatiemodel-ID kan niet leeg zijn                             |
| Portuguese (Brazilian) | ID do modelo de autorização não pode estar vazio                   |
| Russian                | ID модели авторизации не может быть пустым                         |
| Swedish                | Auktorisationsmodell-ID kan inte vara tomt                         |
| Turkish                | Yetkilendirme Modeli ID boş olamaz                                 |
| Ukrainian              | ID моделі авторизації не може бути порожнім                        |
| Chinese (Simplified)   | 授权模型ID不能为空                                                 |

### `request.object_empty`

| Locale                 | Translation                               |
| ---------------------- | ----------------------------------------- |
| German                 | Objekt darf nicht leer sein               |
| English                | Object cannot be empty                    |
| Spanish                | El objeto no puede estar vacío            |
| French                 | L&#039;objet ne peut pas être vide        |
| Italian                | L&#039;&#039;oggetto non può essere vuoto |
| Japanese               | オブジェクトを空にすることはできません    |
| Korean                 | 객체는 비어있을 수 없습니다               |
| Dutch                  | Object kan niet leeg zijn                 |
| Portuguese (Brazilian) | Objeto não pode estar vazio               |
| Russian                | Объект не может быть пустым               |
| Swedish                | Objekt kan inte vara tomt                 |
| Turkish                | Nesne boş olamaz                          |
| Ukrainian              | Об&#039;єкт не може бути порожнім         |
| Chinese (Simplified)   | 对象不能为空                              |

### `request.object_type_empty`

| Locale                 | Translation                                  |
| ---------------------- | -------------------------------------------- |
| German                 | Objekttyp darf nicht leer sein               |
| English                | Object type cannot be empty                  |
| Spanish                | El tipo de objeto no puede estar vacío       |
| French                 | Le type d&#039;objet ne peut pas être vide   |
| Italian                | Il tipo di oggetto non può essere vuoto      |
| Japanese               | オブジェクトタイプを空にすることはできません |
| Korean                 | 객체 타입은 비어있을 수 없습니다             |
| Dutch                  | Objecttype kan niet leeg zijn                |
| Portuguese (Brazilian) | Tipo de objeto não pode estar vazio          |
| Russian                | Тип объекта не может быть пустым             |
| Swedish                | Objekttyp kan inte vara tom                  |
| Turkish                | Nesne türü boş olamaz                        |
| Ukrainian              | Тип об&#039;єкта не може бути порожнім       |
| Chinese (Simplified)   | 对象类型不能为空                             |

### `request.page_size_invalid`

| Locale                 | Translation                                        |
| ---------------------- | -------------------------------------------------- |
| German                 | Ungültige pageSize für %className% bereitgestellt  |
| English                | Invalid pageSize provided to %className%           |
| Spanish                | pageSize inválido proporcionado a %className%      |
| French                 | pageSize invalide fourni à %className%             |
| Italian                | pageSize non valido fornito a %className%          |
| Japanese               | %className%に無効なpageSizeが提供されました        |
| Korean                 | %className%에 잘못된 pageSize가 제공되었습니다     |
| Dutch                  | Ongeldige pageSize verstrekt aan %className%       |
| Portuguese (Brazilian) | pageSize inválido fornecido para %className%       |
| Russian                | Недопустимый pageSize предоставлен для %className% |
| Swedish                | Ogiltig pageSize tillhandahållen till %className%  |
| Turkish                | %className% için geçersiz pageSize sağlandı        |
| Ukrainian              | Недійсний pageSize надано для %className%          |
| Chinese (Simplified)   | 提供给%className%的pageSize无效                    |

### `request.relation_empty`

| Locale                 | Translation                       |
| ---------------------- | --------------------------------- |
| German                 | Relation darf nicht leer sein     |
| English                | Relation cannot be empty          |
| Spanish                | La relación no puede estar vacía  |
| French                 | La relation ne peut pas être vide |
| Italian                | La relazione non può essere vuota |
| Japanese               | 関係を空にすることはできません    |
| Korean                 | 관계는 비어있을 수 없습니다       |
| Dutch                  | Relatie kan niet leeg zijn        |
| Portuguese (Brazilian) | Relação não pode estar vazia      |
| Russian                | Отношение не может быть пустым    |
| Swedish                | Relation kan inte vara tom        |
| Turkish                | İlişki boş olamaz                 |
| Ukrainian              | Відношення не може бути порожнім  |
| Chinese (Simplified)   | 关系不能为空                      |

### `request.store_id_empty`

| Locale                 | Translation                                      |
| ---------------------- | ------------------------------------------------ |
| German                 | Store-ID darf nicht leer sein                    |
| English                | Store ID cannot be empty                         |
| Spanish                | El ID del almacén no puede estar vacío           |
| French                 | L&#039;ID du magasin ne peut pas être vide       |
| Italian                | L&#039;&#039;ID dello store non può essere vuoto |
| Japanese               | ストアIDを空にすることはできません               |
| Korean                 | 스토어 ID는 비어있을 수 없습니다                 |
| Dutch                  | Store-ID kan niet leeg zijn                      |
| Portuguese (Brazilian) | ID do store não pode estar vazio                 |
| Russian                | ID хранилища не может быть пустым                |
| Swedish                | Butiks-ID kan inte vara tomt                     |
| Turkish                | Mağaza ID boş olamaz                             |
| Ukrainian              | ID сховища не може бути порожнім                 |
| Chinese (Simplified)   | 存储ID不能为空                                   |

### `request.store_name_empty`

| Locale                 | Translation                                |
| ---------------------- | ------------------------------------------ |
| German                 | Store-Name darf nicht leer sein            |
| English                | Store name cannot be empty                 |
| Spanish                | El nombre del almacén no puede estar vacío |
| French                 | Le nom du magasin ne peut pas être vide    |
| Italian                | Il nome dello store non può essere vuoto   |
| Japanese               | ストア名を空にすることはできません         |
| Korean                 | 스토어 이름은 비어있을 수 없습니다         |
| Dutch                  | Storenaam kan niet leeg zijn               |
| Portuguese (Brazilian) | Nome do store não pode estar vazio         |
| Russian                | Имя хранилища не может быть пустым         |
| Swedish                | Butiksnamn kan inte vara tomt              |
| Turkish                | Mağaza adı boş olamaz                      |
| Ukrainian              | Ім&#039;я сховища не може бути порожнім    |
| Chinese (Simplified)   | 存储名称不能为空                           |

### `request.transactional_limit_exceeded`

| Locale                 | Translation                                                                                                                                                             |
| ---------------------- | ----------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| German                 | Transaktionale writeTuples-Grenze überschritten: %count% Operationen (max. 100). Verwenden Sie den nicht-transaktionalen Modus oder teilen Sie in mehrere Anfragen auf. |
| English                | Transactional writeTuples exceeded limit: %count% operations (max 100). Use non-transactional mode or split into multiple requests.                                     |
| Spanish                | WriteTuples transaccional excedió el límite: %count% operaciones (máx. 100). Use modo no transaccional o divida en múltiples solicitudes.                               |
| French                 | Limite de writeTuples transactionnel dépassée : %count% opérations (max 100). Utilisez le mode non transactionnel ou divisez en plusieurs requêtes.                     |
| Italian                | writeTuples transazionale ha superato il limite: %count% operazioni (massimo 100). Usa la modalità non transazionale o dividi in più richieste.                         |
| Japanese               | トランザクショナルwriteTuplesが制限を超えました: %count%個の操作（最大100個）。非トランザクショナルモードを使用するか、複数のリクエストに分割してください。             |
| Korean                 | 트랜잭션 writeTuples가 제한을 초과했습니다: %count%개 작업 (최대 100개). 비트랜잭션 모드를 사용하거나 여러 요청으로 분할하세요.                                         |
| Dutch                  | Transactionele writeTuples heeft limiet overschreden: %count% operaties (max 100). Gebruik niet-transactionele modus of verdeel over meerdere verzoeken.                |
| Portuguese (Brazilian) | WriteTuples transacional excedeu limite: %count% operações (máx 100). Use modo não-transacional ou divida em múltiplas requisições.                                     |
| Russian                | Транзакционный writeTuples превысил лимит: %count% операций (максимум 100). Используйте нетранзакционный режим или разделите на несколько запросов.                     |
| Swedish                | Transaktionell writeTuples överskred gränsen: %count% operationer (max 100). Använd icke-transaktionellt läge eller dela upp i flera förfrågningar.                     |
| Turkish                | İşlemsel writeTuples sınırı aştı: %count% işlem (maksimum 100). İşlemsel olmayan modu kullanın veya birden fazla isteğe bölün.                                          |
| Ukrainian              | Транзакційний writeTuples перевищив ліміт: %count% операцій (максимум 100). Використовуйте нетранзакційний режим або розділіть на кілька запитів.                       |
| Chinese (Simplified)   | 事务性writeTuples超出限制：%count%个操作（最大100个）。请使用非事务模式或拆分为多个请求。                                                                               |

### `request.type_empty`

| Locale                 | Translation                      |
| ---------------------- | -------------------------------- |
| German                 | Typ darf nicht leer sein         |
| English                | Type cannot be empty             |
| Spanish                | El tipo no puede estar vacío     |
| French                 | Le type ne peut pas être vide    |
| Italian                | Il tipo non può essere vuoto     |
| Japanese               | タイプを空にすることはできません |
| Korean                 | 타입은 비어있을 수 없습니다      |
| Dutch                  | Type kan niet leeg zijn          |
| Portuguese (Brazilian) | Tipo não pode estar vazio        |
| Russian                | Тип не может быть пустым         |
| Swedish                | Typ kan inte vara tom            |
| Turkish                | Tür boş olamaz                   |
| Ukrainian              | Тип не може бути порожнім        |
| Chinese (Simplified)   | 类型不能为空                     |

### `request.user_empty`

| Locale                 | Translation                              |
| ---------------------- | ---------------------------------------- |
| German                 | Benutzer darf nicht leer sein            |
| English                | User cannot be empty                     |
| Spanish                | El usuario no puede estar vacío          |
| French                 | L&#039;utilisateur ne peut pas être vide |
| Italian                | L&#039;&#039;utente non può essere vuoto |
| Japanese               | ユーザーを空にすることはできません       |
| Korean                 | 사용자는 비어있을 수 없습니다            |
| Dutch                  | Gebruiker kan niet leeg zijn             |
| Portuguese (Brazilian) | Usuário não pode estar vazio             |
| Russian                | Пользователь не может быть пустым        |
| Swedish                | Användare kan inte vara tom              |
| Turkish                | Kullanıcı boş olamaz                     |
| Ukrainian              | Користувач не може бути порожнім         |
| Chinese (Simplified)   | 用户不能为空                             |

### `response.unexpected_type`

| Locale                 | Translation                              |
| ---------------------- | ---------------------------------------- |
| German                 | Unerwarteter Antworttyp erhalten         |
| English                | Unexpected response type received        |
| Spanish                | Tipo de respuesta inesperado recibido    |
| French                 | Type de réponse inattendu reçu           |
| Italian                | Tipo di risposta inaspettato ricevuto    |
| Japanese               | 予期しないレスポンスタイプを受信しました |
| Korean                 | 예상치 못한 응답 타입을 받았습니다       |
| Dutch                  | Onverwacht responstype ontvangen         |
| Portuguese (Brazilian) | Tipo de resposta inesperado recebido     |
| Russian                | Получен неожиданный тип ответа           |
| Swedish                | Oväntad svarstyp mottagen                |
| Turkish                | Beklenmeyen yanıt türü alındı            |
| Ukrainian              | Отримано неочікуваний тип відповіді      |
| Chinese (Simplified)   | 收到意外的响应类型                       |

### `result.failure_no_value`

| Locale                 | Translation                         |
| ---------------------- | ----------------------------------- |
| German                 | Fehlschlag hat keinen Wert          |
| English                | Failure has no value                |
| Spanish                | El resultado fallido no tiene valor |
| French                 | L&#039;échec n&#039;a pas de valeur |
| Italian                | Il fallimento non ha valore         |
| Japanese               | 失敗には値がありません              |
| Korean                 | 실패에는 값이 없습니다              |
| Dutch                  | Falen heeft geen waarde             |
| Portuguese (Brazilian) | Falha não tem valor                 |
| Russian                | Неудача не имеет значения           |
| Swedish                | Misslyckande har inget värde        |
| Turkish                | Başarısızlığın değeri yok           |
| Ukrainian              | Невдача не має значення             |
| Chinese (Simplified)   | 失败没有值                          |

### `result.success_no_error`

| Locale                 | Translation                          |
| ---------------------- | ------------------------------------ |
| German                 | Erfolg hat keinen Fehler             |
| English                | Success has no error                 |
| Spanish                | El resultado exitoso no tiene error  |
| French                 | Le succès n&#039;a pas d&#039;erreur |
| Italian                | Il successo non ha errori            |
| Japanese               | 成功にはエラーがありません           |
| Korean                 | 성공에는 오류가 없습니다             |
| Dutch                  | Succes heeft geen fout               |
| Portuguese (Brazilian) | Sucesso não tem erro                 |
| Russian                | Успех не имеет ошибки                |
| Swedish                | Framgång har inget fel               |
| Turkish                | Başarının hatası yok                 |
| Ukrainian              | Успіх не має помилки                 |
| Chinese (Simplified)   | 成功没有错误                         |

### `schema.class_not_found`

| Locale                 | Translation                                                                                           |
| ---------------------- | ----------------------------------------------------------------------------------------------------- |
| German                 | Klasse &#039;%className%&#039; existiert nicht oder kann nicht automatisch geladen werden             |
| English                | Class &quot;%className%&quot; does not exist or cannot be autoloaded                                  |
| Spanish                | La clase &quot;%className%&quot; no existe o no se puede cargar automáticamente                       |
| French                 | La classe &quot;%className%&quot; n&#039;&#039;existe pas ou ne peut pas être chargée automatiquement |
| Italian                | La classe &quot;%className%&quot; non esiste o non può essere auto-caricata                           |
| Japanese               | クラス「%className%」は存在しないか、自動読み込みできません                                           |
| Korean                 | 클래스 &quot;%className%&quot;가 존재하지 않거나 자동로드할 수 없습니다                               |
| Dutch                  | Klasse &quot;%className%&quot; bestaat niet of kan niet automatisch geladen worden                    |
| Portuguese (Brazilian) | Classe &quot;%className%&quot; não existe ou não pode ser carregada automaticamente                   |
| Russian                | Класс &quot;%className%&quot; не существует или не может быть автозагружен                            |
| Swedish                | Klass &quot;%className%&quot; existerar inte eller kan inte autoladdas                                |
| Turkish                | Sınıf &quot;%className%&quot; mevcut değil veya otomatik yüklenemiyor                                 |
| Ukrainian              | Клас &quot;%className%&quot; не існує або не може бути автозавантажений                               |
| Chinese (Simplified)   | 类&quot;%className%&quot;不存在或无法自动加载                                                         |

### `schema.item_type_not_found`

| Locale                 | Translation                                                                                                            |
| ---------------------- | ---------------------------------------------------------------------------------------------------------------------- |
| German                 | Elementtyp &#039;%itemType%&#039; existiert nicht oder kann nicht automatisch geladen werden                           |
| English                | Item type &quot;%itemType%&quot; does not exist or cannot be autoloaded                                                |
| Spanish                | El tipo de elemento &quot;%itemType%&quot; no existe o no se puede cargar automáticamente                              |
| French                 | Le type d&#039;&#039;élément &quot;%itemType%&quot; n&#039;&#039;existe pas ou ne peut pas être chargé automatiquement |
| Italian                | Il tipo di elemento &quot;%itemType%&quot; non esiste o non può essere auto-caricato                                   |
| Japanese               | アイテムタイプ「%itemType%」は存在しないか、自動読み込みできません                                                     |
| Korean                 | 항목 타입 &quot;%itemType%&quot;이 존재하지 않거나 자동로드할 수 없습니다                                              |
| Dutch                  | Itemtype &quot;%itemType%&quot; bestaat niet of kan niet automatisch geladen worden                                    |
| Portuguese (Brazilian) | Tipo de item &quot;%itemType%&quot; não existe ou não pode ser carregado automaticamente                               |
| Russian                | Тип элемента &quot;%itemType%&quot; не существует или не может быть автозагружен                                       |
| Swedish                | Objekttyp &quot;%itemType%&quot; existerar inte eller kan inte autoladdas                                              |
| Turkish                | Öğe türü &quot;%itemType%&quot; mevcut değil veya otomatik yüklenemiyor                                                |
| Ukrainian              | Тип елемента &quot;%itemType%&quot; не існує або не може бути автозавантажений                                         |
| Chinese (Simplified)   | 项目类型&quot;%itemType%&quot;不存在或无法自动加载                                                                     |

### `exception.serialization.could_not_add_items_to_collection`

| Locale                 | Translation                                                        |
| ---------------------- | ------------------------------------------------------------------ |
| German                 | Elemente konnten nicht zu Sammlung %className% hinzugefügt werden  |
| English                | Could not add items to collection %className%                      |
| Spanish                | No se pudieron agregar elementos a la colección %className%        |
| French                 | Impossible d&#039;ajouter des éléments à la collection %className% |
| Italian                | Impossibile aggiungere elementi alla collezione %className%        |
| Japanese               | コレクション%className%にアイテムを追加できませんでした            |
| Korean                 | 컬렉션 %className%에 항목을 추가할 수 없습니다                     |
| Dutch                  | Kon geen items toevoegen aan verzameling %className%               |
| Portuguese (Brazilian) | Não foi possível adicionar itens à coleção %className%             |
| Russian                | Не удалось добавить элементы в коллекцию %className%               |
| Swedish                | Kunde inte lägga till objekt i samling %className%                 |
| Turkish                | %className% koleksiyonuna öğeler eklenemedi                        |
| Ukrainian              | Не вдалося додати елементи до колекції %className%                 |
| Chinese (Simplified)   | 无法向集合%className%添加项目                                      |

### `exception.serialization.empty_collection`

| Locale                 | Translation                            |
| ---------------------- | -------------------------------------- |
| German                 | Sammlung darf nicht leer sein          |
| English                | Collection cannot be empty             |
| Spanish                | La colección no puede estar vacía      |
| French                 | La collection ne peut pas être vide    |
| Italian                | La collezione non può essere vuota     |
| Japanese               | コレクションを空にすることはできません |
| Korean                 | 컬렉션은 비어있을 수 없습니다          |
| Dutch                  | Verzameling kan niet leeg zijn         |
| Portuguese (Brazilian) | Coleção não pode estar vazia           |
| Russian                | Коллекция не может быть пустой         |
| Swedish                | Samling kan inte vara tom              |
| Turkish                | Koleksiyon boş olamaz                  |
| Ukrainian              | Колекція не може бути порожньою        |
| Chinese (Simplified)   | 集合不能为空                           |

### `exception.serialization.invalid_item_type`

| Locale                 | Translation                                                                                                |
| ---------------------- | ---------------------------------------------------------------------------------------------------------- |
| German                 | Ungültiger Elementtyp für %property% in %className%: erwartet %expected%, erhalten %actual_type%           |
| English                | Invalid item type for %property% in %className%: expected %expected%, got %actual_type%                    |
| Spanish                | Tipo de elemento inválido para %property% en %className%: se esperaba %expected%, se obtuvo %actual_type%  |
| French                 | Type d&#039;élément invalide pour %property% dans %className% : %expected% attendu, %actual_type% obtenu   |
| Italian                | Tipo di elemento non valido per %property% in %className%: atteso %expected%, ottenuto %actual_type%       |
| Japanese               | %className%の%property%に無効なアイテムタイプです: %expected%が期待されますが%actual_type%が取得されました |
| Korean                 | %className%의 %property%에 대한 잘못된 항목 타입: %expected%이 예상되지만 %actual_type%을 받았습니다       |
| Dutch                  | Ongeldig itemtype voor %property% in %className%: verwacht %expected%, kreeg %actual_type%                 |
| Portuguese (Brazilian) | Tipo de item inválido para %property% em %className%: esperado %expected%, obtido %actual_type%            |
| Russian                | Недопустимый тип элемента для %property% в %className%: ожидается %expected%, получено %actual_type%       |
| Swedish                | Ogiltig objekttyp för %property% i %className%: förväntad %expected%, fick %actual_type%                   |
| Turkish                | %className% içindeki %property% için geçersiz öğe türü: %expected% bekleniyor, %actual_type% alındı        |
| Ukrainian              | Недійсний тип елемента для %property% в %className%: очікується %expected%, отримано %actual_type%         |
| Chinese (Simplified)   | %className%中%property%的项目类型无效：期望%expected%，得到%actual_type%                                   |

### `exception.serialization.missing_required_constructor_parameter`

| Locale                 | Translation                                                                                       |
| ---------------------- | ------------------------------------------------------------------------------------------------- |
| German                 | Erforderlicher Konstruktorparameter &#039;%paramName%&#039; für Klasse %className% fehlt          |
| English                | Missing required constructor parameter &quot;%paramName%&quot; for class %className%              |
| Spanish                | Falta el parámetro requerido del constructor &quot;%paramName%&quot; para la clase %className%    |
| French                 | Paramètre de constructeur requis manquant &quot;%paramName%&quot; pour la classe %className%      |
| Italian                | Parametro del costruttore obbligatorio &quot;%paramName%&quot; mancante per la classe %className% |
| Japanese               | クラス%className%の必須コンストラクターパラメーター「%paramName%」がありません                    |
| Korean                 | 클래스 %className%의 필수 생성자 매개변수 &quot;%paramName%&quot;이 누락되었습니다                |
| Dutch                  | Ontbrekende vereiste constructor parameter &quot;%paramName%&quot; voor klasse %className%        |
| Portuguese (Brazilian) | Parâmetro obrigatório do construtor &quot;%paramName%&quot; faltando para classe %className%      |
| Russian                | Отсутствует обязательный параметр конструктора &quot;%paramName%&quot; для класса %className%     |
| Swedish                | Saknas obligatorisk konstruktorparameter &quot;%paramName%&quot; för klass %className%            |
| Turkish                | %className% sınıfı için gerekli yapıcı parametresi &quot;%paramName%&quot; eksik                  |
| Ukrainian              | Відсутній обов&#039;язковий параметр конструктора &#039;%paramName%&#039; для класу %className%   |
| Chinese (Simplified)   | 类%className%缺少必需的构造函数参数&quot;%paramName%&quot;                                        |

### `exception.serialization.response`

| Locale                 | Translation                                                       |
| ---------------------- | ----------------------------------------------------------------- |
| German                 | Serialisierung/Deserialisierung der Antwortdaten fehlgeschlagen   |
| English                | Failed to serialize/deserialize response data                     |
| Spanish                | No se pudieron serializar/deserializar los datos de respuesta     |
| French                 | Échec de la sérialisation/désérialisation des données de réponse  |
| Italian                | Fallita la serializzazione/deserializzazione dei dati di risposta |
| Japanese               | レスポンスデータのシリアライズ/デシリアライズに失敗しました       |
| Korean                 | 응답 데이터 직렬화/역직렬화에 실패했습니다                        |
| Dutch                  | Mislukt om responsdata te serialiseren/deserialiseren             |
| Portuguese (Brazilian) | Falha ao serializar/deserializar dados de resposta                |
| Russian                | Не удалось сериализовать/десериализовать данные ответа            |
| Swedish                | Misslyckades med att serialisera/deserialisera svarsdata          |
| Turkish                | Yanıt verilerini serileştirme/deserileştirme başarısız            |
| Ukrainian              | Не вдалося серіалізувати/десеріалізувати дані відповіді           |
| Chinese (Simplified)   | 序列化/反序列化响应数据失败                                       |

### `exception.serialization.undefined_item_type`

| Locale                 | Translation                                                   |
| ---------------------- | ------------------------------------------------------------- |
| German                 | Elementtyp ist für %className% nicht definiert                |
| English                | Item type is not defined for %className%                      |
| Spanish                | El tipo de elemento no está definido para %className%         |
| French                 | Le type d&#039;élément n&#039;est pas défini pour %className% |
| Italian                | Tipo di elemento non definito per %className%                 |
| Japanese               | %className%のアイテムタイプが定義されていません               |
| Korean                 | %className%의 항목 타입이 정의되지 않았습니다                 |
| Dutch                  | Itemtype is niet gedefinieerd voor %className%                |
| Portuguese (Brazilian) | Tipo de item não está definido para %className%               |
| Russian                | Тип элемента не определен для %className%                     |
| Swedish                | Objekttyp är inte definierad för %className%                  |
| Turkish                | %className% için öğe türü tanımlanmamış                       |
| Ukrainian              | Тип елемента не визначений для %className%                    |
| Chinese (Simplified)   | %className%的项目类型未定义                                   |

### `service.http_not_available`

| Locale                 | Translation                      |
| ---------------------- | -------------------------------- |
| German                 | HTTP-Service nicht verfügbar     |
| English                | HTTP service not available       |
| Spanish                | Servicio HTTP no disponible      |
| French                 | Service HTTP non disponible      |
| Italian                | Servizio HTTP non disponibile    |
| Japanese               | HTTPサービスが利用できません     |
| Korean                 | HTTP 서비스를 사용할 수 없습니다 |
| Dutch                  | HTTP-service niet beschikbaar    |
| Portuguese (Brazilian) | Serviço HTTP não disponível      |
| Russian                | HTTP сервис недоступен           |
| Swedish                | HTTP-tjänst inte tillgänglig     |
| Turkish                | HTTP hizmeti kullanılamıyor      |
| Ukrainian              | HTTP сервіс недоступний          |
| Chinese (Simplified)   | HTTP服务不可用                   |

### `service.schema_validator_not_available`

| Locale                 | Translation                          |
| ---------------------- | ------------------------------------ |
| German                 | Schema-Validator nicht verfügbar     |
| English                | Schema validator not available       |
| Spanish                | Validador de esquema no disponible   |
| French                 | Validateur de schéma non disponible  |
| Italian                | Validatore schema non disponibile    |
| Japanese               | スキーマバリデーターが利用できません |
| Korean                 | 스키마 검증기를 사용할 수 없습니다   |
| Dutch                  | Schema-validator niet beschikbaar    |
| Portuguese (Brazilian) | Validador de schema não disponível   |
| Russian                | Валидатор схемы недоступен           |
| Swedish                | Schemavalidator inte tillgänglig     |
| Turkish                | Şema doğrulayıcı kullanılamıyor      |
| Ukrainian              | Валідатор схеми недоступний          |
| Chinese (Simplified)   | 模式验证器不可用                     |

### `service.store_repository_not_available`

| Locale                 | Translation                          |
| ---------------------- | ------------------------------------ |
| German                 | Store-Repository nicht verfügbar     |
| English                | Store repository not available       |
| Spanish                | Repositorio de almacén no disponible |
| French                 | Dépôt de magasin non disponible      |
| Italian                | Repository store non disponibile     |
| Japanese               | ストアリポジトリが利用できません     |
| Korean                 | 스토어 저장소를 사용할 수 없습니다   |
| Dutch                  | Store-repository niet beschikbaar    |
| Portuguese (Brazilian) | Repositório de store não disponível  |
| Russian                | Репозиторий хранилища недоступен     |
| Swedish                | Butiks-repository inte tillgängligt  |
| Turkish                | Mağaza deposu kullanılamıyor         |
| Ukrainian              | Репозиторій сховища недоступний      |
| Chinese (Simplified)   | 存储仓库不可用                       |

### `service.tuple_filter_not_available`

| Locale                 | Translation                                |
| ---------------------- | ------------------------------------------ |
| German                 | Tupel-Filter-Service nicht verfügbar       |
| English                | Tuple filter service not available         |
| Spanish                | Servicio de filtro de tuplas no disponible |
| French                 | Service de filtre de tuple non disponible  |
| Italian                | Servizio filtro tuple non disponibile      |
| Japanese               | タプルフィルターサービスが利用できません   |
| Korean                 | 튜플 필터 서비스를 사용할 수 없습니다      |
| Dutch                  | Tuple-filterservice niet beschikbaar       |
| Portuguese (Brazilian) | Serviço de filtro de tupla não disponível  |
| Russian                | Служба фильтра кортежей недоступна         |
| Swedish                | Tupel-filtertjänst inte tillgänglig        |
| Turkish                | Tuple filtre hizmeti kullanılamıyor        |
| Ukrainian              | Служба фільтра кортежів недоступна         |
| Chinese (Simplified)   | 元组过滤器服务不可用                       |

### `service.tuple_repository_not_available`

| Locale                 | Translation                         |
| ---------------------- | ----------------------------------- |
| German                 | Tupel-Repository nicht verfügbar    |
| English                | Tuple repository not available      |
| Spanish                | Repositorio de tuplas no disponible |
| French                 | Dépôt de tuple non disponible       |
| Italian                | Repository tuple non disponibile    |
| Japanese               | タプルリポジトリが利用できません    |
| Korean                 | 튜플 저장소를 사용할 수 없습니다    |
| Dutch                  | Tuple-repository niet beschikbaar   |
| Portuguese (Brazilian) | Repositório de tupla não disponível |
| Russian                | Репозиторий кортежей недоступен     |
| Swedish                | Tupel-repository inte tillgängligt  |
| Turkish                | Tuple deposu kullanılamıyor         |
| Ukrainian              | Репозиторій кортежів недоступний    |
| Chinese (Simplified)   | 元组仓库不可用                      |

### `store.name_required`

| Locale                 | Translation                                                |
| ---------------------- | ---------------------------------------------------------- |
| German                 | Store-Name ist erforderlich und darf nicht leer sein       |
| English                | Store name is required and cannot be empty                 |
| Spanish                | El nombre del almacén es requerido y no puede estar vacío  |
| French                 | Le nom du magasin est requis et ne peut pas être vide      |
| Italian                | Il nome dello store è obbligatorio e non può essere vuoto  |
| Japanese               | ストア名は必須で、空にすることはできません                 |
| Korean                 | 스토어 이름은 필수이며 비어있을 수 없습니다                |
| Dutch                  | Storenaam is vereist en kan niet leeg zijn                 |
| Portuguese (Brazilian) | Nome do store é obrigatório e não pode estar vazio         |
| Russian                | Имя хранилища обязательно и не может быть пустым           |
| Swedish                | Butiksnamn krävs och kan inte vara tomt                    |
| Turkish                | Mağaza adı gereklidir ve boş olamaz                        |
| Ukrainian              | Ім&#039;я сховища обов&#039;язкове і не може бути порожнім |
| Chinese (Simplified)   | 存储名称是必需的，不能为空                                 |

### `store.name_too_long`

| Locale                 | Translation                                                                          |
| ---------------------- | ------------------------------------------------------------------------------------ |
| German                 | Store-Name überschreitet maximale Länge von %d Zeichen (bereitgestellt: %d)          |
| English                | Store name exceeds maximum length of %d characters (provided: %d)                    |
| Spanish                | El nombre del almacén excede la longitud máxima de %d caracteres (proporcionado: %d) |
| French                 | Le nom du magasin dépasse la longueur maximale de %d caractères (fourni : %d)        |
| Italian                | Il nome dello store supera la lunghezza massima di %d caratteri (forniti: %d)        |
| Japanese               | ストア名が最大長%d文字を超えています（提供された文字数: %d）                         |
| Korean                 | 스토어 이름이 최대 길이 %d자를 초과했습니다 (제공됨: %d)                             |
| Dutch                  | Storenaam overschrijdt maximale lengte van %d karakters (verstrekt: %d)              |
| Portuguese (Brazilian) | Nome do store excede comprimento máximo de %d caracteres (fornecido: %d)             |
| Russian                | Имя хранилища превышает максимальную длину в %d символов (предоставлено: %d)         |
| Swedish                | Butiksnamn överstiger maximal längd på %d tecken (tillhandahållet: %d)               |
| Turkish                | Mağaza adı %d karakter maksimum uzunluğunu aşıyor (sağlanan: %d)                     |
| Ukrainian              | Ім&#039;я сховища перевищує максимальну довжину %d символів (надано: %d)             |
| Chinese (Simplified)   | 存储名称超过最大长度%d个字符（提供：%d）                                             |

### `store.not_found`

| Locale                 | Translation                           |
| ---------------------- | ------------------------------------- |
| German                 | Store %s wurde nicht gefunden         |
| English                | Store %s was not found                |
| Spanish                | No se encontró el almacén %s          |
| French                 | Le magasin %s n&#039;a pas été trouvé |
| Italian                | Store %s non trovato                  |
| Japanese               | ストア%sが見つかりませんでした        |
| Korean                 | 스토어 %s를 찾을 수 없습니다          |
| Dutch                  | Store %s niet gevonden                |
| Portuguese (Brazilian) | Store %s não foi encontrado           |
| Russian                | Хранилище %s не найдено               |
| Swedish                | Butik %s hittades inte                |
| Turkish                | Mağaza %s bulunamadı                  |
| Ukrainian              | Сховище %s не знайдено                |
| Chinese (Simplified)   | 未找到存储%s                          |

### `translation.file_not_found`

| Locale                 | Translation                                     |
| ---------------------- | ----------------------------------------------- |
| German                 | Übersetzungsdatei nicht gefunden: %resource%    |
| English                | Translation file not found: %resource%          |
| Spanish                | Archivo de traducción no encontrado: %resource% |
| French                 | Fichier de traduction non trouvé : %resource%   |
| Italian                | File di traduzione non trovato: %resource%      |
| Japanese               | 翻訳ファイルが見つかりません: %resource%        |
| Korean                 | 번역 파일을 찾을 수 없습니다: %resource%        |
| Dutch                  | Vertaalbestand niet gevonden: %resource%        |
| Portuguese (Brazilian) | Arquivo de tradução não encontrado: %resource%  |
| Russian                | Файл перевода не найден: %resource%             |
| Swedish                | Översättningsfil hittades inte: %resource%      |
| Turkish                | Çeviri dosyası bulunamadı: %resource%           |
| Ukrainian              | Файл перекладу не знайдено: %resource%          |
| Chinese (Simplified)   | 未找到翻译文件：%resource%                      |

### `translation.unsupported_format`

| Locale                 | Translation                                              |
| ---------------------- | -------------------------------------------------------- |
| German                 | Nicht unterstütztes Übersetzungsdateiformat: %format%    |
| English                | Unsupported translation file format: %format%            |
| Spanish                | Formato de archivo de traducción no compatible: %format% |
| French                 | Format de fichier de traduction non supporté : %format%  |
| Italian                | Formato del file di traduzione non supportato: %format%  |
| Japanese               | サポートされていない翻訳ファイル形式です: %format%       |
| Korean                 | 지원되지 않는 번역 파일 형식: %format%                   |
| Dutch                  | Niet ondersteund vertaalbestandformaat: %format%         |
| Portuguese (Brazilian) | Formato de arquivo de tradução não suportado: %format%   |
| Russian                | Неподдерживаемый формат файла перевода: %format%         |
| Swedish                | Ostödd översättningsfilformat: %format%                  |
| Turkish                | Desteklenmeyen çeviri dosya formatı: %format%            |
| Ukrainian              | Непідтримуваний формат файлу перекладу: %format%         |
| Chinese (Simplified)   | 不支持的翻译文件格式：%format%                           |

### `tuple_operation.delete.description`

| Locale                 | Translation                                                                                     |
| ---------------------- | ----------------------------------------------------------------------------------------------- |
| German                 | Entfernt ein vorhandenes Beziehungstupel, widerruft Berechtigungen oder entfernt Beziehungen    |
| English                | Removes an existing relationship tuple, revoking permissions or removing relationships          |
| Spanish                | Elimina una tupla de relación existente, revocando permisos o eliminando relaciones             |
| French                 | Supprime un tuple de relation existant, révoquant des permissions ou supprimant des relations   |
| Italian                | Rimuove una tupla di relazione esistente, revocando permessi o rimuovendo relazioni             |
| Japanese               | 既存の関係タプルを削除し、権限を取り消すか関係を削除します                                      |
| Korean                 | 기존 관계 튜플을 제거하여 권한을 취소하거나 관계를 삭제합니다                                   |
| Dutch                  | Verwijdert een bestaande relatietuple, trekt machtigingen in of verwijdert relaties             |
| Portuguese (Brazilian) | Remove uma tupla de relacionamento existente, revogando permissões ou removendo relacionamentos |
| Russian                | Удаляет существующий кортеж отношений, отзывая разрешения или удаляя отношения                  |
| Swedish                | Tar bort en befintlig relationstupel, återkallar behörigheter eller tar bort relationer         |
| Turkish                | Mevcut bir ilişki tuple kaldırır, izinleri iptal eder veya ilişkileri siler                     |
| Ukrainian              | Видаляє існуючий кортеж відношень, відкликаючи дозволи або видаляючи відношення                 |
| Chinese (Simplified)   | 删除现有的关系元组，撤销权限或移除关系                                                          |

### `tuple_operation.write.description`

| Locale                 | Translation                                                                                       |
| ---------------------- | ------------------------------------------------------------------------------------------------- |
| German                 | Fügt ein neues Beziehungstupel hinzu, gewährt Berechtigungen oder stellt Beziehungen her          |
| English                | Adds a new relationship tuple, granting permissions or establishing relationships                 |
| Spanish                | Agrega una nueva tupla de relación, otorgando permisos o estableciendo relaciones                 |
| French                 | Ajoute un nouveau tuple de relation, accordant des permissions ou établissant des relations       |
| Italian                | Aggiunge una nuova tupla di relazione, concedendo permessi o stabilendo relazioni                 |
| Japanese               | 新しい関係タプルを追加し、権限を付与するか関係を確立します                                        |
| Korean                 | 새로운 관계 튜플을 추가하여 권한을 부여하거나 관계를 설정합니다                                   |
| Dutch                  | Voegt een nieuwe relatietuple toe, verleent machtigingen of vestigt relaties                      |
| Portuguese (Brazilian) | Adiciona uma nova tupla de relacionamento, concedendo permissões ou estabelecendo relacionamentos |
| Russian                | Добавляет новый кортеж отношений, предоставляя разрешения или устанавливая отношения              |
| Swedish                | Lägger till en ny relationstupel, beviljar behörigheter eller etablerar relationer                |
| Turkish                | Yeni bir ilişki tuple ekler, izinler verir veya ilişkiler kurar                                   |
| Ukrainian              | Додає новий кортеж відношень, надаючи дозволи або встановлюючи відношення                         |
| Chinese (Simplified)   | 添加新的关系元组，授予权限或建立关系                                                              |

### `yaml.cannot_read_file`

| Locale                 | Translation                                 |
| ---------------------- | ------------------------------------------- |
| German                 | Datei kann nicht gelesen werden: %filename% |
| English                | Cannot read file: %filename%                |
| Spanish                | No se puede leer el archivo: %filename%     |
| French                 | Impossible de lire le fichier : %filename%  |
| Italian                | Impossibile leggere il file: %filename%     |
| Japanese               | ファイルを読み取れません: %filename%        |
| Korean                 | 파일을 읽을 수 없습니다: %filename%         |
| Dutch                  | Kan bestand niet lezen: %filename%          |
| Portuguese (Brazilian) | Não é possível ler arquivo: %filename%      |
| Russian                | Невозможно прочитать файл: %filename%       |
| Swedish                | Kan inte läsa fil: %filename%               |
| Turkish                | Dosya okunamıyor: %filename%                |
| Ukrainian              | Неможливо прочитати файл: %filename%        |
| Chinese (Simplified)   | 无法读取文件：%filename%                    |

### `yaml.file_does_not_exist`

| Locale                 | Translation                               |
| ---------------------- | ----------------------------------------- |
| German                 | Datei existiert nicht: %filename%         |
| English                | File does not exist: %filename%           |
| Spanish                | El archivo no existe: %filename%          |
| French                 | Le fichier n&#039;existe pas : %filename% |
| Italian                | Il file non esiste: %filename%            |
| Japanese               | ファイルが存在しません: %filename%        |
| Korean                 | 파일이 존재하지 않습니다: %filename%      |
| Dutch                  | Bestand bestaat niet: %filename%          |
| Portuguese (Brazilian) | Arquivo não existe: %filename%            |
| Russian                | Файл не существует: %filename%            |
| Swedish                | Filen existerar inte: %filename%          |
| Turkish                | Dosya mevcut değil: %filename%            |
| Ukrainian              | Файл не існує: %filename%                 |
| Chinese (Simplified)   | 文件不存在：%filename%                    |

### `yaml.invalid_structure`

| Locale                 | Translation                                         |
| ---------------------- | --------------------------------------------------- |
| German                 | Ungültige YAML-Struktur in Zeile %line_number%      |
| English                | Invalid YAML structure on line %line_number%        |
| Spanish                | Estructura YAML inválida en la línea %line_number%  |
| French                 | Structure YAML invalide à la ligne %line_number%    |
| Italian                | Struttura YAML non valida alla riga %line_number%   |
| Japanese               | 行%line_number%のYAML構造が無効です                 |
| Korean                 | %line_number%번째 줄의 YAML 구조가 잘못되었습니다   |
| Dutch                  | Ongeldige YAML-structuur op regel %line_number%     |
| Portuguese (Brazilian) | Estrutura YAML inválida na linha %line_number%      |
| Russian                | Недопустимая структура YAML на строке %line_number% |
| Swedish                | Ogiltig YAML-struktur på rad %line_number%          |
| Turkish                | %line_number% satırında geçersiz YAML yapısı        |
| Ukrainian              | Недійсна структура YAML на рядку %line_number%      |
| Chinese (Simplified)   | 第%line_number%行YAML结构无效                       |

### `yaml.invalid_syntax_empty_key`

| Locale                 | Translation                                                      |
| ---------------------- | ---------------------------------------------------------------- |
| German                 | Ungültige YAML-Syntax in Zeile %line_number%: leerer Schlüssel   |
| English                | Invalid YAML syntax on line %line_number%: empty key             |
| Spanish                | Sintaxis YAML inválida en la línea %line_number%: clave vacía    |
| French                 | Syntaxe YAML invalide à la ligne %line_number% : clé vide        |
| Italian                | Sintassi YAML non valida alla riga %line_number%: chiave vuota   |
| Japanese               | 行%line_number%のYAML構文が無効です: キーが空です                |
| Korean                 | %line_number%번째 줄의 YAML 구문이 잘못되었습니다: 빈 키         |
| Dutch                  | Ongeldige YAML-syntaxis op regel %line_number%: lege sleutel     |
| Portuguese (Brazilian) | Sintaxe YAML inválida na linha %line_number%: chave vazia        |
| Russian                | Недопустимый синтаксис YAML на строке %line_number%: пустой ключ |
| Swedish                | Ogiltig YAML-syntax på rad %line_number%: tom nyckel             |
| Turkish                | %line_number% satırında geçersiz YAML sözdizimi: boş anahtar     |
| Ukrainian              | Недійсний синтаксис YAML на рядку %line_number%: порожній ключ   |
| Chinese (Simplified)   | 第%line_number%行YAML语法无效：空键                              |

### `yaml.invalid_syntax_missing_colon`

| Locale                 | Translation                                                                |
| ---------------------- | -------------------------------------------------------------------------- |
| German                 | Ungültige YAML-Syntax in Zeile %line_number%: fehlender Doppelpunkt        |
| English                | Invalid YAML syntax on line %line_number%: missing colon                   |
| Spanish                | Sintaxis YAML inválida en la línea %line_number%: falta dos puntos         |
| French                 | Syntaxe YAML invalide à la ligne %line_number% : deux-points manquants     |
| Italian                | Sintassi YAML non valida alla riga %line_number%: due punti mancanti       |
| Japanese               | 行%line_number%のYAML構文が無効です: コロンがありません                    |
| Korean                 | %line_number%번째 줄의 YAML 구문이 잘못되었습니다: 콜론 누락               |
| Dutch                  | Ongeldige YAML-syntaxis op regel %line_number%: ontbrekende dubbele punt   |
| Portuguese (Brazilian) | Sintaxe YAML inválida na linha %line_number%: dois pontos faltando         |
| Russian                | Недопустимый синтаксис YAML на строке %line_number%: отсутствует двоеточие |
| Swedish                | Ogiltig YAML-syntax på rad %line_number%: saknar kolon                     |
| Turkish                | %line_number% satırında geçersiz YAML sözdizimi: iki nokta eksik           |
| Ukrainian              | Недійсний синтаксис YAML на рядку %line_number%: відсутня двокрапка        |
| Chinese (Simplified)   | 第%line_number%行YAML语法无效：缺少冒号                                    |

### `yaml.invalid_syntax_missing_value`

| Locale                 | Translation                                                               |
| ---------------------- | ------------------------------------------------------------------------- |
| German                 | Ungültige YAML-Syntax in Zeile %line_number%: fehlender Wert              |
| English                | Invalid YAML syntax on line %line_number%: missing value                  |
| Spanish                | Sintaxis YAML inválida en la línea %line_number%: falta valor             |
| French                 | Syntaxe YAML invalide à la ligne %line_number% : valeur manquante         |
| Italian                | Sintassi YAML non valida alla riga %line_number%: valore mancante         |
| Japanese               | 行%line_number%のYAML構文が無効です: 値がありません                       |
| Korean                 | %line_number%번째 줄의 YAML 구문이 잘못되었습니다: 값 누락                |
| Dutch                  | Ongeldige YAML-syntaxis op regel %line_number%: ontbrekende waarde        |
| Portuguese (Brazilian) | Sintaxe YAML inválida na linha %line_number%: valor faltando              |
| Russian                | Недопустимый синтаксис YAML на строке %line_number%: отсутствует значение |
| Swedish                | Ogiltig YAML-syntax på rad %line_number%: saknar värde                    |
| Turkish                | %line_number% satırında geçersiz YAML sözdizimi: değer eksik              |
| Ukrainian              | Недійсний синтаксис YAML на рядку %line_number%: відсутнє значення        |
| Chinese (Simplified)   | 第%line_number%行YAML语法无效：缺少值                                     |

## Methods

#### key

```php
public function key(): string

```

Get the translation key for this message.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Messages.php#L369)

#### Returns

`string`
