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

| Locale  | Translation                                           |
| ------- | ----------------------------------------------------- |
| `de`    | Assertion-Sammlung darf nicht leer sein               |
| `en`    | Assertions collection cannot be empty                 |
| `es`    | La colección de aserciones no puede estar vacía       |
| `fr`    | La collection d&#039;assertions ne peut pas être vide |
| `it`    | La collezione di asserzioni non può essere vuota      |
| `ja`    | アサーションコレクションを空にすることはできません                             |
| `ko`    | 어설션 컬렉션은 비어있을 수 없습니다                                  |
| `nl`    | Assertieverzameling kan niet leeg zijn                |
| `pt_BR` | Coleção de assertions não pode estar vazia            |
| `ru`    | Коллекция утверждений не может быть пустой            |
| `sv`    | Påståendesamling kan inte vara tom                    |
| `tr`    | Onaylama koleksiyonu boş olamaz                       |
| `uk`    | Колекція тверджень не може бути порожньою             |
| `zh_CN` | 断言集合不能为空                                              |

### `assertions.invalid_tuple_key`

| Locale  | Translation                                                                                                |
| ------- | ---------------------------------------------------------------------------------------------------------- |
| `de`    | Assertion enthält ungültigen Tupel-Schlüssel: Benutzer, Relation und Objekt sind erforderlich              |
| `en`    | Assertion contains invalid tuple key: user, relation, and object are required                              |
| `es`    | La aserción contiene una clave de tupla inválida: se requieren usuario, relación y objeto                  |
| `fr`    | L&#039;assertion contient une clé de tuple invalide : utilisateur, relation et objet sont requis           |
| `it`    | L&#039;&#039;asserzione contiene una chiave tupla non valida: utente, relazione e oggetto sono obbligatori |
| `ja`    | アサーションに無効なタプルキーが含まれています: ユーザー、関係、オブジェクトが必須です                                                               |
| `ko`    | 어설션에 잘못된 튜플 키가 포함되어 있습니다: 사용자, 관계, 객체가 필요합니다                                                               |
| `nl`    | Assertie bevat ongeldige tuple-sleutel: gebruiker, relatie en object zijn vereist                          |
| `pt_BR` | Assertion contém chave de tupla inválida: usuário, relação e objeto são obrigatórios                       |
| `ru`    | Утверждение содержит недопустимый ключ кортежа: требуются пользователь, отношение и объект                 |
| `sv`    | Påstående innehåller ogiltig tupel-nyckel: användare, relation och objekt krävs                            |
| `tr`    | Onaylama geçersiz tuple anahtarı içeriyor: kullanıcı, ilişki ve nesne gerekli                              |
| `uk`    | Твердження містить недійсний ключ кортежу: потрібні користувач, відношення та об&#039;єкт                  |
| `zh_CN` | 断言包含无效的元组键：用户、关系和对象是必需的                                                                                    |

### `auth.access_token_must_be_string`

| Locale  | Translation                              |
| ------- | ---------------------------------------- |
| `de`    | access_token muss eine Zeichenkette sein |
| `en`    | access_token must be a string            |
| `es`    | access_token debe ser una cadena         |
| `fr`    | access_token doit être une chaîne        |
| `it`    | access_token deve essere una stringa     |
| `ja`    | access_tokenは文字列である必要があります               |
| `ko`    | access_token은 문자열이어야 합니다                 |
| `nl`    | access_token moet een string zijn        |
| `pt_BR` | access_token deve ser uma string         |
| `ru`    | access_token должен быть строкой         |
| `sv`    | access_token måste vara en sträng        |
| `tr`    | access_token bir dize olmalıdır          |
| `uk`    | access_token має бути рядком             |
| `zh_CN` | access_token必须是字符串                       |

### `exception.auth.token_expired`

| Locale  | Translation                               |
| ------- | ----------------------------------------- |
| `de`    | Authentifizierungstoken ist abgelaufen    |
| `en`    | Authentication token has expired          |
| `es`    | El token de autenticación ha expirado     |
| `fr`    | Le jeton d&#039;authentification a expiré |
| `it`    | Il token di autenticazione è scaduto      |
| `ja`    | 認証トークンの有効期限が切れました                         |
| `ko`    | 인증 토큰이 만료되었습니다                            |
| `nl`    | Authenticatietoken is verlopen            |
| `pt_BR` | Token de autenticação expirou             |
| `ru`    | Токен аутентификации истек                |
| `sv`    | Autentiseringstoken har gått ut           |
| `tr`    | Kimlik doğrulama tokeni süresi doldu      |
| `uk`    | Токен автентифікації закінчився           |
| `zh_CN` | 身份验证令牌已过期                                 |

### `exception.auth.token_invalid`

| Locale  | Translation                                   |
| ------- | --------------------------------------------- |
| `de`    | Authentifizierungstoken ist ungültig          |
| `en`    | Authentication token is invalid               |
| `es`    | El token de autenticación es inválido         |
| `fr`    | Le jeton d&#039;authentification est invalide |
| `it`    | Il token di autenticazione non è valido       |
| `ja`    | 認証トークンが無効です                                   |
| `ko`    | 인증 토큰이 잘못되었습니다                                |
| `nl`    | Authenticatietoken is ongeldig                |
| `pt_BR` | Token de autenticação é inválido              |
| `ru`    | Токен аутентификации недействителен           |
| `sv`    | Autentiseringstoken är ogiltig                |
| `tr`    | Kimlik doğrulama tokeni geçersiz              |
| `uk`    | Токен автентифікації недійсний                |
| `zh_CN` | 身份验证令牌无效                                      |

### `auth.expires_in_must_be_integer`

| Locale  | Translation                             |
| ------- | --------------------------------------- |
| `de`    | expires_in muss eine Ganzzahl sein      |
| `en`    | expires_in must be an integer           |
| `es`    | expires_in debe ser un entero           |
| `fr`    | expires_in doit être un entier          |
| `it`    | expires_in deve essere un numero intero |
| `ja`    | expires_inは整数である必要があります                 |
| `ko`    | expires_in은 정수여야 합니다                    |
| `nl`    | expires_in moet een geheel getal zijn   |
| `pt_BR` | expires_in deve ser um inteiro          |
| `ru`    | expires_in должен быть целым числом     |
| `sv`    | expires_in måste vara ett heltal        |
| `tr`    | expires_in bir tamsayı olmalıdır        |
| `uk`    | expires_in має бути цілим числом        |
| `zh_CN` | expires_in必须是整数                         |

### `auth.invalid_response_format`

| Locale  | Translation                    |
| ------- | ------------------------------ |
| `de`    | Ungültiges Antwortformat       |
| `en`    | Invalid response format        |
| `es`    | Formato de respuesta inválido  |
| `fr`    | Format de réponse invalide     |
| `it`    | Formato di risposta non valido |
| `ja`    | 無効なレスポンス形式です                   |
| `ko`    | 잘못된 응답 형식                      |
| `nl`    | Ongeldig responsformaat        |
| `pt_BR` | Formato de resposta inválido   |
| `ru`    | Недопустимый формат ответа     |
| `sv`    | Ogiltigt svarsformat           |
| `tr`    | Geçersiz yanıt formatı         |
| `uk`    | Недійсний формат відповіді     |
| `zh_CN` | 无效的响应格式                        |

### `auth.missing_required_fields`

| Locale  | Translation                                   |
| ------- | --------------------------------------------- |
| `de`    | Erforderliche Felder in der Antwort fehlen    |
| `en`    | Missing required fields in response           |
| `es`    | Faltan campos requeridos en la respuesta      |
| `fr`    | Champs obligatoires manquants dans la réponse |
| `it`    | Campi obbligatori mancanti nella risposta     |
| `ja`    | レスポンスに必須フィールドがありません                           |
| `ko`    | 응답에 필수 필드가 누락되었습니다                            |
| `nl`    | Ontbrekende vereiste velden in respons        |
| `pt_BR` | Campos obrigatórios faltando na resposta      |
| `ru`    | Отсутствуют обязательные поля в ответе        |
| `sv`    | Saknade obligatoriska fält i svar             |
| `tr`    | Yanıtta gerekli alanlar eksik                 |
| `uk`    | Відсутні обов&#039;язкові поля у відповіді    |
| `zh_CN` | 响应中缺少必需字段                                     |

### `auth.user_message.token_expired`

| Locale  | Translation                                                   |
| ------- | ------------------------------------------------------------- |
| `de`    | Ihre Sitzung ist abgelaufen. Bitte melden Sie sich erneut an. |
| `en`    | Your session has expired. Please sign in again.               |
| `es`    | Su sesión ha expirado. Por favor, inicie sesión nuevamente.   |
| `fr`    | Votre session a expiré. Veuillez vous reconnecter.            |
| `it`    | La tua sessione è scaduta. Per favore, accedi di nuovo.       |
| `ja`    | セッションの有効期限が切れました。再度サインインしてください。                               |
| `ko`    | 세션이 만료되었습니다. 다시 로그인해 주세요.                                     |
| `nl`    | Uw sessie is verlopen. Log opnieuw in.                        |
| `pt_BR` | Sua sessão expirou. Por favor, faça login novamente.          |
| `ru`    | Ваша сессия истекла. Пожалуйста, войдите снова.               |
| `sv`    | Din session har gått ut. Vänligen logga in igen.              |
| `tr`    | Oturumunuzun süresi doldu. Lütfen tekrar giriş yapın.         |
| `uk`    | Ваша сесія закінчилася. Будь ласка, увійдіть знову.           |
| `zh_CN` | 您的会话已过期，请重新登录。                                                |

### `auth.user_message.token_invalid`

| Locale  | Translation                                               |
| ------- | --------------------------------------------------------- |
| `de`    | Ungültige Authentifizierungsdaten bereitgestellt.         |
| `en`    | Invalid authentication credentials provided.              |
| `es`    | Credenciales de autenticación inválidas proporcionadas.   |
| `fr`    | Identifiants d&#039;authentification invalides fournis.   |
| `it`    | Credenziali di autenticazione non valide fornite.         |
| `ja`    | 無効な認証資格情報が提供されました。                                        |
| `ko`    | 잘못된 인증 자격 증명이 제공되었습니다.                                    |
| `nl`    | Ongeldige authenticatiegegevens verstrekt.                |
| `pt_BR` | Credenciais de autenticação inválidas fornecidas.         |
| `ru`    | Предоставлены недопустимые учетные данные аутентификации. |
| `sv`    | Ogiltiga autentiseringsuppgifter tillhandahållna.         |
| `tr`    | Geçersiz kimlik doğrulama bilgileri sağlandı.             |
| `uk`    | Надано недійсні облікові дані автентифікації.             |
| `zh_CN` | 提供的身份验证凭据无效。                                              |

### `validation.batch_tuple_chunk_size_exceeded`

| Locale  | Translation                                         |
| ------- | --------------------------------------------------- |
| `de`    | Chunk-Größe darf %max_size% nicht überschreiten     |
| `en`    | Chunk size cannot exceed %max_size%                 |
| `es`    | El tamaño del fragmento no puede exceder %max_size% |
| `fr`    | La taille du bloc ne peut pas dépasser %max_size%   |
| `it`    | La dimensione del chunk non può superare %max_size% |
| `ja`    | チャンクサイズは%max_size%を超えることはできません                      |
| `ko`    | 청크 크기는 %max_size%를 초과할 수 없습니다                       |
| `nl`    | Chunkgrootte kan %max_size% niet overschrijden      |
| `pt_BR` | Tamanho do chunk não pode exceder %max_size%        |
| `ru`    | Размер блока не может превышать %max_size%          |
| `sv`    | Chunkstorlek kan inte överstiga %max_size%          |
| `tr`    | Parça boyutu %max_size% değerini aşamaz             |
| `uk`    | Розмір блоку не може перевищувати %max_size%        |
| `zh_CN` | 块大小不能超过%max_size%                                   |

### `validation.batch_tuple_chunk_size_positive`

| Locale  | Translation                                                   |
| ------- | ------------------------------------------------------------- |
| `de`    | Chunk-Größe muss eine positive Ganzzahl sein                  |
| `en`    | Chunk size must be a positive integer                         |
| `es`    | El tamaño del fragmento debe ser un entero positivo           |
| `fr`    | La taille du bloc doit être un entier positif                 |
| `it`    | La dimensione del chunk deve essere un numero intero positivo |
| `ja`    | チャンクサイズは正の整数である必要があります                                        |
| `ko`    | 청크 크기는 양의 정수여야 합니다                                            |
| `nl`    | Chunkgrootte moet een positief geheel getal zijn              |
| `pt_BR` | Tamanho do chunk deve ser um inteiro positivo                 |
| `ru`    | Размер блока должен быть положительным целым числом           |
| `sv`    | Chunkstorlek måste vara ett positivt heltal                   |
| `tr`    | Parça boyutu pozitif bir tamsayı olmalıdır                    |
| `uk`    | Розмір блоку має бути додатним цілим числом                   |
| `zh_CN` | 块大小必须是正整数                                                     |

### `exception.client.authentication`

| Locale  | Translation                                 |
| ------- | ------------------------------------------- |
| `de`    | Authentifizierungsfehler aufgetreten        |
| `en`    | Authentication error occurred               |
| `es`    | Error de autenticación                      |
| `fr`    | Erreur d&#039;authentification survenue     |
| `it`    | Si è verificato un errore di autenticazione |
| `ja`    | 認証エラーが発生しました                                |
| `ko`    | 인증 오류가 발생했습니다                               |
| `nl`    | Authenticatiefout opgetreden                |
| `pt_BR` | Erro de autenticação ocorreu                |
| `ru`    | Произошла ошибка аутентификации             |
| `sv`    | Autentiseringsfel inträffade                |
| `tr`    | Kimlik doğrulama hatası oluştu              |
| `uk`    | Сталася помилка автентифікації              |
| `zh_CN` | 发生身份验证错误                                    |

### `exception.client.configuration`

| Locale  | Translation                       |
| ------- | --------------------------------- |
| `de`    | Konfigurationsfehler erkannt      |
| `en`    | Configuration error detected      |
| `es`    | Error de configuración detectado  |
| `fr`    | Erreur de configuration détectée  |
| `it`    | Rilevato errore di configurazione |
| `ja`    | 設定エラーが検出されました                     |
| `ko`    | 구성 오류가 감지되었습니다                    |
| `nl`    | Configuratiefout gedetecteerd     |
| `pt_BR` | Erro de configuração detectado    |
| `ru`    | Обнаружена ошибка конфигурации    |
| `sv`    | Konfigurationsfel upptäckt        |
| `tr`    | Yapılandırma hatası tespit edildi |
| `uk`    | Виявлено помилку конфігурації     |
| `zh_CN` | 检测到配置错误                           |

### `exception.client.network`

| Locale  | Translation                       |
| ------- | --------------------------------- |
| `de`    | Netzwerkkommunikationsfehler      |
| `en`    | Network communication error       |
| `es`    | Error de comunicación de red      |
| `fr`    | Erreur de communication réseau    |
| `it`    | Errore di comunicazione di rete   |
| `ja`    | ネットワーク通信エラー                       |
| `ko`    | 네트워크 통신 오류                        |
| `nl`    | Netwerkcommunicatiefout           |
| `pt_BR` | Erro de comunicação de rede       |
| `ru`    | Ошибка сетевого соединения        |
| `sv`    | Nätverkskommunikationsfel         |
| `tr`    | Ağ iletişim hatası                |
| `uk`    | Помилка мережевого з&#039;єднання |
| `zh_CN` | 网络通信错误                            |

### `exception.client.serialization`

| Locale  | Translation                         |
| ------- | ----------------------------------- |
| `de`    | Datenserialisierungsfehler          |
| `en`    | Data serialization error            |
| `es`    | Error de serialización de datos     |
| `fr`    | Erreur de sérialisation des données |
| `it`    | Errore di serializzazione dati      |
| `ja`    | データシリアライゼーションエラー                    |
| `ko`    | 데이터 직렬화 오류                          |
| `nl`    | Data serialisatiefout               |
| `pt_BR` | Erro de serialização de dados       |
| `ru`    | Ошибка сериализации данных          |
| `sv`    | Dataserialiseringsfel               |
| `tr`    | Veri serileştirme hatası            |
| `uk`    | Помилка серіалізації даних          |
| `zh_CN` | 数据序列化错误                             |

### `exception.client.validation`

| Locale  | Translation                          |
| ------- | ------------------------------------ |
| `de`    | Anfragvalidierung fehlgeschlagen     |
| `en`    | Request validation failed            |
| `es`    | La validación de la solicitud falló  |
| `fr`    | Échec de la validation de la requête |
| `it`    | Validazione della richiesta fallita  |
| `ja`    | リクエストの検証に失敗しました                      |
| `ko`    | 요청 검증에 실패했습니다                        |
| `nl`    | Verzoekvalidatie mislukt             |
| `pt_BR` | Validação de requisição falhou       |
| `ru`    | Проверка запроса не удалась          |
| `sv`    | Begäranvalidering misslyckades       |
| `tr`    | İstek doğrulaması başarısız          |
| `uk`    | Перевірка запиту не вдалася          |
| `zh_CN` | 请求验证失败                               |

### `collection.invalid_item_instance`

| Locale  | Translation                                                     |
| ------- | --------------------------------------------------------------- |
| `de`    | Erwartete Instanz von %expected%, %given% gegeben               |
| `en`    | Expected instance of %expected%, %given% given                  |
| `es`    | Se esperaba una instancia de %expected%, se proporcionó %given% |
| `fr`    | Instance attendue de %expected%, %given% donné                  |
| `it`    | Attesa istanza di %expected%, fornito %given%                   |
| `ja`    | %expected%のインスタンスが期待されます。%given%が提供されました                        |
| `ko`    | %expected%의 인스턴스가 예상됩니다. %given%이 제공되었습니다                       |
| `nl`    | Verwacht instantie van %expected%, %given% gegeven              |
| `pt_BR` | Esperada instância de %expected%, %given% fornecido             |
| `ru`    | Ожидается экземпляр %expected%, предоставлено %given%           |
| `sv`    | Förväntad instans av %expected%, %given% given                  |
| `tr`    | %expected% örneği bekleniyor, %given% verildi                   |
| `uk`    | Очікується екземпляр %expected%, надано %given%                 |
| `zh_CN` | 期望%expected%的实例，提供了%given%                                      |

### `collection.invalid_item_type_interface`

| Locale  | Translation                                                                        |
| ------- | ---------------------------------------------------------------------------------- |
| `de`    | Erwarteter Elementtyp sollte %interface% implementieren, %given% gegeben           |
| `en`    | Expected item type to implement %interface%, %given% given                         |
| `es`    | Se esperaba que el tipo de elemento implemente %interface%, se proporcionó %given% |
| `fr`    | Type d&#039;élément attendu pour implémenter %interface%, %given% donné            |
| `it`    | Il tipo di elemento dovrebbe implementare %interface%, fornito %given%             |
| `ja`    | アイテムタイプは%interface%を実装する必要があります。%given%が提供されました                                    |
| `ko`    | 항목 타입이 %interface%를 구현해야 합니다. %given%이 제공되었습니다                                     |
| `nl`    | Verwacht itemtype om %interface% te implementeren, %given% gegeven                 |
| `pt_BR` | Esperado tipo de item para implementar %interface%, %given% fornecido              |
| `ru`    | Ожидается, что тип элемента реализует %interface%, предоставлено %given%           |
| `sv`    | Förväntad objekttyp att implementera %interface%, %given% given                    |
| `tr`    | Öğe türünün %interface% uygulaması bekleniyor, %given% verildi                     |
| `uk`    | Очікується, що тип елемента реалізує %interface%, надано %given%                   |
| `zh_CN` | 期望项目类型实现%interface%，提供了%given%                                                     |

### `collection.invalid_key_type`

| Locale  | Translation                                                         |
| ------- | ------------------------------------------------------------------- |
| `de`    | Ungültiger Schlüsseltyp; Zeichenkette erwartet, %given% gegeben.    |
| `en`    | Invalid key type; expected string, %given% given.                   |
| `es`    | Tipo de clave inválido; se esperaba cadena, se proporcionó %given%. |
| `fr`    | Type de clé invalide ; chaîne attendue, %given% donné.              |
| `it`    | Tipo di chiave non valido; attesa stringa, fornito %given%.         |
| `ja`    | 無効なキータイプです。文字列が期待されます。%given%が提供されました。                              |
| `ko`    | 잘못된 키 타입; 문자열이 예상됩니다. %given%이 제공되었습니다.                             |
| `nl`    | Ongeldig sleuteltype; verwacht string, %given% gegeven.             |
| `pt_BR` | Tipo de chave inválido; esperada string, %given% fornecido.         |
| `ru`    | Недопустимый тип ключа; ожидается строка, предоставлено %given%.    |
| `sv`    | Ogiltig nyckeltyp; förväntad sträng, %given% given.                 |
| `tr`    | Geçersiz anahtar türü; dize bekleniyor, %given% verildi.            |
| `uk`    | Недійсний тип ключа; очікується рядок, надано %given%.              |
| `zh_CN` | 无效的键类型；期望字符串，提供了%given%。                                            |

### `collection.invalid_position`

| Locale  | Translation          |
| ------- | -------------------- |
| `de`    | Ungültige Position   |
| `en`    | Invalid position     |
| `es`    | Posición inválida    |
| `fr`    | Position invalide    |
| `it`    | Posizione non valida |
| `ja`    | 無効な位置です              |
| `ko`    | 잘못된 위치               |
| `nl`    | Ongeldige positie    |
| `pt_BR` | Posição inválida     |
| `ru`    | Недопустимая позиция |
| `sv`    | Ogiltig position     |
| `tr`    | Geçersiz konum       |
| `uk`    | Недійсна позиція     |
| `zh_CN` | 无效的位置                |

### `collection.invalid_value_type`

| Locale  | Translation                                                      |
| ------- | ---------------------------------------------------------------- |
| `de`    | Erwartete Instanz von %expected%, %given% gegeben.               |
| `en`    | Expected instance of %expected%, %given% given.                  |
| `es`    | Se esperaba una instancia de %expected%, se proporcionó %given%. |
| `fr`    | Instance attendue de %expected%, %given% donné.                  |
| `it`    | Attesa istanza di %expected%, fornito %given%.                   |
| `ja`    | %expected%のインスタンスが期待されます。%given%が提供されました。                        |
| `ko`    | %expected%의 인스턴스가 예상됩니다. %given%이 제공되었습니다.                       |
| `nl`    | Verwacht instantie van %expected%, %given% gegeven.              |
| `pt_BR` | Esperada instância de %expected%, %given% fornecido.             |
| `ru`    | Ожидается экземпляр %expected%, предоставлено %given%.           |
| `sv`    | Förväntad instans av %expected%, %given% given.                  |
| `tr`    | %expected% örneği bekleniyor, %given% verildi.                   |
| `uk`    | Очікується екземпляр %expected%, надано %given%.                 |
| `zh_CN` | 期望%expected%的实例，提供了%given%。                                      |

### `collection.key_must_be_string`

| Locale  | Translation                            |
| ------- | -------------------------------------- |
| `de`    | Schlüssel muss eine Zeichenkette sein. |
| `en`    | Key must be a string.                  |
| `es`    | La clave debe ser una cadena.          |
| `fr`    | La clé doit être une chaîne.           |
| `it`    | La chiave deve essere una stringa.     |
| `ja`    | キーは文字列である必要があります。                      |
| `ko`    | 키는 문자열이어야 합니다.                         |
| `nl`    | Sleutel moet een string zijn.          |
| `pt_BR` | Chave deve ser uma string.             |
| `ru`    | Ключ должен быть строкой.              |
| `sv`    | Nyckel måste vara en sträng.           |
| `tr`    | Anahtar bir dize olmalıdır.            |
| `uk`    | Ключ має бути рядком.                  |
| `zh_CN` | 键必须是字符串。                               |

### `collection.undefined_item_type`

| Locale  | Translation                                                                                                            |
| ------- | ---------------------------------------------------------------------------------------------------------------------- |
| `de`    | Undefinierter Elementtyp für %class%. Definieren Sie die $itemType-Eigenschaft oder überschreiben Sie den Konstruktor. |
| `en`    | Undefined item type for %class%. Define the $itemType property or override the constructor.                            |
| `es`    | Tipo de elemento indefinido para %class%. Define la propiedad $itemType o sobrescribe el constructor.                  |
| `fr`    | Type d&#039;élément non défini pour %class%. Définissez la propriété $itemType ou surchargez le constructeur.          |
| `it`    | Tipo di elemento non definito per %class%. Definire la proprietà $itemType o sovrascrivere il costruttore.             |
| `ja`    | %class%のアイテムタイプが未定義です。$itemTypeプロパティを定義するかコンストラクターをオーバーライドしてください。                                                      |
| `ko`    | %class%의 항목 타입이 정의되지 않았습니다. $itemType 속성을 정의하거나 생성자를 재정의하세요.                                                           |
| `nl`    | Ongedefinieerd itemtype voor %class%. Definieer de $itemType eigenschap of overschrijf de constructor.                 |
| `pt_BR` | Tipo de item indefinido para %class%. Defina a propriedade $itemType ou sobrescreva o construtor.                      |
| `ru`    | Неопределенный тип элемента для %class%. Определите свойство $itemType или переопределите конструктор.                 |
| `sv`    | Odefinierad objekttyp för %class%. Definiera $itemType-egenskapen eller åsidosätt konstruktorn.                        |
| `tr`    | %class% için tanımlanmamış öğe türü. $itemType özelliğini tanımlayın veya yapıcıyı geçersiz kılın.                     |
| `uk`    | Невизначений тип елемента для %class%. Визначте властивість $itemType або перевизначте конструктор.                    |
| `zh_CN` | %class%的项目类型未定义。请定义$itemType属性或覆盖构造函数。                                                                                 |

### `exception.config.http_client_missing`

| Locale  | Translation                             |
| ------- | --------------------------------------- |
| `de`    | HTTP-Client ist nicht konfiguriert      |
| `en`    | HTTP client is not configured           |
| `es`    | El cliente HTTP no está configurado     |
| `fr`    | Le client HTTP n&#039;est pas configuré |
| `it`    | Client HTTP non configurato             |
| `ja`    | HTTPクライアントが設定されていません                    |
| `ko`    | HTTP 클라이언트가 구성되지 않았습니다                  |
| `nl`    | HTTP-client is niet geconfigureerd      |
| `pt_BR` | Cliente HTTP não está configurado       |
| `ru`    | HTTP клиент не настроен                 |
| `sv`    | HTTP-klient är inte konfigurerad        |
| `tr`    | HTTP istemci yapılandırılmamış          |
| `uk`    | HTTP клієнт не налаштований             |
| `zh_CN` | HTTP客户端未配置                              |

### `exception.config.http_request_factory_missing`

| Locale  | Translation                                            |
| ------- | ------------------------------------------------------ |
| `de`    | HTTP-Request-Factory ist nicht konfiguriert            |
| `en`    | HTTP request factory is not configured                 |
| `es`    | La fábrica de solicitudes HTTP no está configurada     |
| `fr`    | La fabrique de requêtes HTTP n&#039;est pas configurée |
| `it`    | Factory delle richieste HTTP non configurata           |
| `ja`    | HTTPリクエストファクトリが設定されていません                               |
| `ko`    | HTTP 요청 팩토리가 구성되지 않았습니다                                |
| `nl`    | HTTP-verzoek factory is niet geconfigureerd            |
| `pt_BR` | Factory de requisição HTTP não está configurada        |
| `ru`    | Фабрика HTTP запросов не настроена                     |
| `sv`    | HTTP-begäranfabrik är inte konfigurerad                |
| `tr`    | HTTP istek fabrikası yapılandırılmamış                 |
| `uk`    | Фабрика HTTP запитів не налаштована                    |
| `zh_CN` | HTTP请求工厂未配置                                            |

### `exception.config.http_response_factory_missing`

| Locale  | Translation                                            |
| ------- | ------------------------------------------------------ |
| `de`    | HTTP-Response-Factory ist nicht konfiguriert           |
| `en`    | HTTP response factory is not configured                |
| `es`    | La fábrica de respuestas HTTP no está configurada      |
| `fr`    | La fabrique de réponses HTTP n&#039;est pas configurée |
| `it`    | Factory delle risposte HTTP non configurata            |
| `ja`    | HTTPレスポンスファクトリが設定されていません                               |
| `ko`    | HTTP 응답 팩토리가 구성되지 않았습니다                                |
| `nl`    | HTTP-respons factory is niet geconfigureerd            |
| `pt_BR` | Factory de resposta HTTP não está configurada          |
| `ru`    | Фабрика HTTP ответов не настроена                      |
| `sv`    | HTTP-svarsfabrik är inte konfigurerad                  |
| `tr`    | HTTP yanıt fabrikası yapılandırılmamış                 |
| `uk`    | Фабрика HTTP відповідей не налаштована                 |
| `zh_CN` | HTTP响应工厂未配置                                            |

### `exception.config.http_stream_factory_missing`

| Locale  | Translation                                        |
| ------- | -------------------------------------------------- |
| `de`    | HTTP-Stream-Factory ist nicht konfiguriert         |
| `en`    | HTTP stream factory is not configured              |
| `es`    | La fábrica de streams HTTP no está configurada     |
| `fr`    | La fabrique de flux HTTP n&#039;est pas configurée |
| `it`    | Factory degli stream HTTP non configurata          |
| `ja`    | HTTPストリームファクトリが設定されていません                           |
| `ko`    | HTTP 스트림 팩토리가 구성되지 않았습니다                           |
| `nl`    | HTTP-stream factory is niet geconfigureerd         |
| `pt_BR` | Factory de stream HTTP não está configurada        |
| `ru`    | Фабрика HTTP потоков не настроена                  |
| `sv`    | HTTP-strömfabrik är inte konfigurerad              |
| `tr`    | HTTP akış fabrikası yapılandırılmamış              |
| `uk`    | Фабрика HTTP потоків не налаштована                |
| `zh_CN` | HTTP流工厂未配置                                         |

### `exception.config.invalid_language`

| Locale  | Translation                                         |
| ------- | --------------------------------------------------- |
| `de`    | Ungültiger Sprachcode bereitgestellt: %language%    |
| `en`    | Invalid language code provided: %language%          |
| `es`    | Código de idioma inválido proporcionado: %language% |
| `fr`    | Code de langue invalide fourni : %language%         |
| `it`    | Codice lingua non valido fornito: %language%        |
| `ja`    | 無効な言語コードが提供されました: %language%                        |
| `ko`    | 잘못된 언어 코드가 제공되었습니다: %language%                      |
| `nl`    | Ongeldige taalcode verstrekt: %language%            |
| `pt_BR` | Código de idioma inválido fornecido: %language%     |
| `ru`    | Предоставлен недопустимый код языка: %language%     |
| `sv`    | Ogiltig språkkod tillhandahållen: %language%        |
| `tr`    | Geçersiz dil kodu sağlandı: %language%              |
| `uk`    | Надано недійсний код мови: %language%               |
| `zh_CN` | 提供的语言代码无效：%language%                                |

### `exception.config.invalid_retry_count`

| Locale  | Translation                                               |
| ------- | --------------------------------------------------------- |
| `de`    | Ungültige Wiederholungsanzahl bereitgestellt: %retries%   |
| `en`    | Invalid retry count provided: %retries%                   |
| `es`    | Número de reintentos inválido proporcionado: %retries%    |
| `fr`    | Nombre de tentatives invalide fourni : %retries%          |
| `it`    | Numero di tentativi non valido fornito: %retries%         |
| `ja`    | 無効な再試行回数が提供されました: %retries%                               |
| `ko`    | 잘못된 재시도 횟수가 제공되었습니다: %retries%                            |
| `nl`    | Ongeldig aantal herhalingen verstrekt: %retries%          |
| `pt_BR` | Contagem de tentativas inválida fornecida: %retries%      |
| `ru`    | Предоставлено недопустимое количество повторов: %retries% |
| `sv`    | Ogiltigt antal återförsök tillhandahållet: %retries%      |
| `tr`    | Geçersiz yeniden deneme sayısı sağlandı: %retries%        |
| `uk`    | Надано недійсну кількість повторів: %retries%             |
| `zh_CN` | 提供的重试次数无效：%retries%                                       |

### `exception.config.invalid_url`

| Locale  | Translation                          |
| ------- | ------------------------------------ |
| `de`    | Ungültige URL bereitgestellt: %url%  |
| `en`    | Invalid URL provided: %url%          |
| `es`    | URL inválida proporcionada: %url%    |
| `fr`    | URL invalide fournie : %url%         |
| `it`    | URL non valido fornito: %url%        |
| `ja`    | 無効なURLが提供されました: %url%                |
| `ko`    | 잘못된 URL이 제공되었습니다: %url%              |
| `nl`    | Ongeldige URL verstrekt: %url%       |
| `pt_BR` | URL inválida fornecida: %url%        |
| `ru`    | Предоставлен недопустимый URL: %url% |
| `sv`    | Ogiltig URL tillhandahållen: %url%   |
| `tr`    | Geçersiz URL sağlandı: %url%         |
| `uk`    | Надано недійсний URL: %url%          |
| `zh_CN` | 提供的URL无效：%url%                       |

### `consistency.higher_consistency.description`

| Locale  | Translation                                                                                                              |
| ------- | ------------------------------------------------------------------------------------------------------------------------ |
| `de`    | Priorisiert Datenkonsistenz über Abfrageleistung und gewährleistet die aktuellsten Ergebnisse                            |
| `en`    | Prioritizes data consistency over query performance, ensuring the most up-to-date results                                |
| `es`    | Prioriza la consistencia de datos sobre el rendimiento de consultas, asegurando los resultados más actualizados          |
| `fr`    | Privilégie la cohérence des données par rapport aux performances de requête, garantissant les résultats les plus récents |
| `it`    | Prioritizza la coerenza dei dati rispetto alle prestazioni delle query, garantendo i risultati più aggiornati            |
| `ja`    | クエリパフォーマンスよりもデータ整合性を優先し、最新の結果を保証します                                                                                      |
| `ko`    | 쿼리 성능보다 데이터 일관성을 우선시하여 가장 최신 결과를 보장합니다                                                                                   |
| `nl`    | Geeft prioriteit aan dataconsistentie boven queryprestaties, zorgt voor de meest actuele resultaten                      |
| `pt_BR` | Prioriza consistência de dados sobre performance de consulta, garantindo resultados mais atualizados                     |
| `ru`    | Приоритизирует согласованность данных над производительностью запросов, обеспечивая самые актуальные результаты          |
| `sv`    | Prioriterar datakonsistens över frågeprestanda, säkerställer de mest uppdaterade resultaten                              |
| `tr`    | Sorgu performansından ziyade veri tutarlılığını önceleyerek en güncel sonuçları sağlar                                   |
| `uk`    | Пріоритизує узгодженість даних над продуктивністю запитів, забезпечуючи найновіші результати                             |
| `zh_CN` | 优先考虑数据一致性而非查询性能，确保最新的结果                                                                                                  |

### `consistency.minimize_latency.description`

| Locale  | Translation                                                                                                                               |
| ------- | ----------------------------------------------------------------------------------------------------------------------------------------- |
| `de`    | Priorisiert Abfrageleistung über Datenkonsistenz, verwendet möglicherweise leicht veraltete Daten                                         |
| `en`    | Prioritizes query performance over data consistency, potentially using slightly stale data                                                |
| `es`    | Prioriza el rendimiento de consultas sobre la consistencia de datos, potencialmente usando datos ligeramente obsoletos                    |
| `fr`    | Privilégie les performances de requête par rapport à la cohérence des données, utilisant potentiellement des données légèrement obsolètes |
| `it`    | Prioritizza le prestazioni delle query rispetto alla coerenza dei dati, potenzialmente utilizzando dati leggermente obsoleti              |
| `ja`    | データ整合性よりもクエリパフォーマンスを優先し、わずかに古いデータを使用する可能性があります                                                                                            |
| `ko`    | 데이터 일관성보다 쿼리 성능을 우선시하여 약간 오래된 데이터를 사용할 수 있습니다                                                                                             |
| `nl`    | Geeft prioriteit aan queryprestaties boven dataconsistentie, mogelijk met gebruik van enigszins verouderde data                           |
| `pt_BR` | Prioriza performance de consulta sobre consistência de dados, potencialmente usando dados ligeiramente desatualizados                     |
| `ru`    | Приоритизирует производительность запросов над согласованностью данных, потенциально используя слегка устаревшие данные                   |
| `sv`    | Prioriterar frågeprestanda över datakonsistens, potentiellt använder något föråldrad data                                                 |
| `tr`    | Veri tutarlılığından ziyade sorgu performansını önceleyerek potansiyel olarak biraz eski veri kullanır                                    |
| `uk`    | Пріоритизує продуктивність запитів над узгодженістю даних, потенційно використовуючи дещо застарілі дані                                  |
| `zh_CN` | 优先考虑查询性能而非数据一致性，可能使用稍旧的数据                                                                                                                 |

### `consistency.unspecified.description`

| Locale  | Translation                                                                                       |
| ------- | ------------------------------------------------------------------------------------------------- |
| `de`    | Verwendet die Standard-Konsistenzebene, die durch die OpenFGA-Serverkonfiguration bestimmt wird   |
| `en`    | Uses the default consistency level determined by the OpenFGA server configuration                 |
| `es`    | Usa el nivel de consistencia predeterminado determinado por la configuración del servidor OpenFGA |
| `fr`    | Utilise le niveau de cohérence par défaut déterminé par la configuration du serveur OpenFGA       |
| `it`    | Utilizza il livello di coerenza predefinito determinato dalla configurazione del server OpenFGA   |
| `ja`    | OpenFGAサーバー設定によって決定されるデフォルトの整合性レベルを使用します                                                          |
| `ko`    | OpenFGA 서버 구성에 의해 결정되는 기본 일관성 수준을 사용합니다                                                           |
| `nl`    | Gebruikt het standaard consistentieniveau bepaald door de OpenFGA-serverconfiguratie              |
| `pt_BR` | Usa o nível de consistência padrão determinado pela configuração do servidor OpenFGA              |
| `ru`    | Использует уровень согласованности по умолчанию, определяемый конфигурацией сервера OpenFGA       |
| `sv`    | Använder standardkonsistensnivån som bestäms av OpenFGA-serverkonfigurationen                     |
| `tr`    | OpenFGA sunucu yapılandırması tarafından belirlenen varsayılan tutarlılık seviyesini kullanır     |
| `uk`    | Використовує рівень узгодженості за замовчуванням, визначений конфігурацією сервера OpenFGA       |
| `zh_CN` | 使用由OpenFGA服务器配置确定的默认一致性级别                                                                         |

### `dsl.input_empty`

| Locale  | Translation                                   |
| ------- | --------------------------------------------- |
| `de`    | Eingabezeichenkette darf nicht leer sein      |
| `en`    | Input string cannot be empty                  |
| `es`    | La cadena de entrada no puede estar vacía     |
| `fr`    | La chaîne d&#039;entrée ne peut pas être vide |
| `it`    | La stringa di input non può essere vuota      |
| `ja`    | 入力文字列を空にすることはできません                            |
| `ko`    | 입력 문자열은 비어있을 수 없습니다                           |
| `nl`    | Invoerstring kan niet leeg zijn               |
| `pt_BR` | String de entrada não pode estar vazia        |
| `ru`    | Строка ввода не может быть пустой             |
| `sv`    | Inmatningssträngen kan inte vara tom          |
| `tr`    | Giriş dizesi boş olamaz                       |
| `uk`    | Рядок введення не може бути порожнім          |
| `zh_CN` | 输入字符串不能为空                                     |

### `dsl.invalid_computed_userset`

| Locale  | Translation                                   |
| ------- | --------------------------------------------- |
| `de`    | Ungültiges berechnetes Benutzerset            |
| `en`    | Invalid computed userset                      |
| `es`    | Conjunto de usuarios calculado inválido       |
| `fr`    | Ensemble d&#039;utilisateurs calculé invalide |
| `it`    | Set di utenti calcolato non valido            |
| `ja`    | 無効な計算済みユーザーセットです                              |
| `ko`    | 잘못된 계산된 사용자 집합                                |
| `nl`    | Ongeldige berekende gebruikersset             |
| `pt_BR` | Conjunto de usuários computado inválido       |
| `ru`    | Недопустимый вычисленный набор пользователей  |
| `sv`    | Ogiltig beräknad användaruppsättning          |
| `tr`    | Geçersiz hesaplanmış kullanıcı kümesi         |
| `uk`    | Недійсний обчислений набір користувачів       |
| `zh_CN` | 无效的计算用户集                                      |

### `dsl.invalid_computed_userset_relation`

| Locale  | Translation                                                                       |
| ------- | --------------------------------------------------------------------------------- |
| `de`    | Berechnete Benutzerset-Relation darf nicht leer sein.                             |
| `en`    | Computed userset relation cannot be empty.                                        |
| `es`    | La relación del userset computado no puede estar vacía.                           |
| `fr`    | La relation de l&#039;ensemble d&#039;utilisateurs calculé ne peut pas être vide. |
| `it`    | La relazione del set di utenti calcolato non può essere vuota.                    |
| `ja`    | 計算済みユーザーセットの関係を空にすることはできません。                                                      |
| `ko`    | 계산된 사용자 집합 관계는 비어있을 수 없습니다.                                                       |
| `nl`    | Berekende gebruikersset relatie kan niet leeg zijn.                               |
| `pt_BR` | Relação do conjunto de usuários computado não pode estar vazia.                   |
| `ru`    | Отношение вычисленного набора пользователей не может быть пустым.                 |
| `sv`    | Beräknad användaruppsättnings relation kan inte vara tom.                         |
| `tr`    | Hesaplanmış kullanıcı kümesi ilişkisi boş olamaz.                                 |
| `uk`    | Відношення обчисленого набору користувачів не може бути порожнім.                 |
| `zh_CN` | 计算用户集关系不能为空。                                                                      |

### `dsl.parse_failed`

| Locale  | Translation                                   |
| ------- | --------------------------------------------- |
| `de`    | DSL-Eingabe konnte nicht geparst werden       |
| `en`    | Failed to parse DSL input                     |
| `es`    | No se pudo analizar la entrada DSL            |
| `fr`    | Échec de l&#039;analyse de l&#039;entrée DSL  |
| `it`    | Impossibile analizzare l&#039;&#039;input DSL |
| `ja`    | DSL入力の解析に失敗しました                               |
| `ko`    | DSL 입력 구문분석에 실패했습니다                           |
| `nl`    | Verwerken van DSL-invoer is mislukt           |
| `pt_BR` | Falha ao analisar entrada DSL                 |
| `ru`    | Не удалось разобрать ввод DSL                 |
| `sv`    | Misslyckades med att tolka DSL-inmatning      |
| `tr`    | DSL girişi ayrıştırılamadı                    |
| `uk`    | Не вдалося розібрати введення DSL             |
| `zh_CN` | 解析DSL输入失败                                     |

### `dsl.pattern_empty`

| Locale  | Translation                     |
| ------- | ------------------------------- |
| `de`    | Muster darf nicht leer sein     |
| `en`    | Pattern cannot be empty         |
| `es`    | El patrón no puede estar vacío  |
| `fr`    | Le modèle ne peut pas être vide |
| `it`    | Il pattern non può essere vuoto |
| `ja`    | パターンを空にすることはできません               |
| `ko`    | 패턴은 비어있을 수 없습니다                 |
| `nl`    | Patroon kan niet leeg zijn      |
| `pt_BR` | Padrão não pode estar vazio     |
| `ru`    | Шаблон не может быть пустым     |
| `sv`    | Mönstret kan inte vara tomt     |
| `tr`    | Desen boş olamaz                |
| `uk`    | Шаблон не може бути порожнім    |
| `zh_CN` | 模式不能为空                          |

### `dsl.unbalanced_parentheses_closing`

| Locale  | Translation                                                                           |
| ------- | ------------------------------------------------------------------------------------- |
| `de`    | Unausgeglichene Klammern: zu viele schließende Klammern an Position %position%        |
| `en`    | Unbalanced parentheses: too many closing parentheses at position %position%           |
| `es`    | Paréntesis desequilibrados: demasiados paréntesis de cierre en la posición %position% |
| `fr`    | Parenthèses déséquilibrées : trop de parenthèses fermantes à la position %position%   |
| `it`    | Parentesi non bilanciate: troppe parentesi di chiusura alla posizione %position%      |
| `ja`    | 括弧の対応が取れていません: 位置%position%に閉じ括弧が多すぎます                                                |
| `ko`    | 불균형한 괄호: 위치 %position%에 닫는 괄호가 너무 많습니다                                                |
| `nl`    | Ongelijke haakjes: te veel sluithaakjes op positie %position%                         |
| `pt_BR` | Parênteses desequilibrados: muitos parênteses de fechamento na posição %position%     |
| `ru`    | Несбалансированные скобки: слишком много закрывающих скобок в позиции %position%      |
| `sv`    | Obalanserade parenteser: för många avslutande parenteser vid position %position%      |
| `tr`    | Dengesiz parantezler: %position% konumunda çok fazla kapanış parantezi                |
| `uk`    | Незбалансовані дужки: забагато закриваючих дужок у позиції %position%                 |
| `zh_CN` | 括号不匹配：位置%position%有过多的右括号                                                             |

### `dsl.unbalanced_parentheses_opening`

| Locale  | Translation                                                                |
| ------- | -------------------------------------------------------------------------- |
| `de`    | Unausgeglichene Klammern: %count% ungeschlossene öffnende %parentheses%    |
| `en`    | Unbalanced parentheses: %count% unclosed opening %parentheses%             |
| `es`    | Paréntesis desequilibrados: %count% %parentheses% de apertura sin cerrar   |
| `fr`    | Parenthèses déséquilibrées : %count% %parentheses% ouvrantes non fermées   |
| `it`    | Parentesi non bilanciate: %count% %parentheses% di apertura non chiuse     |
| `ja`    | 括弧の対応が取れていません: %count%個の開き%parentheses%が閉じられていません                          |
| `ko`    | 불균형한 괄호: %count%개의 열린 %parentheses%가 닫히지 않았습니다                             |
| `nl`    | Ongelijke haakjes: %count% ongesloten %parentheses%                        |
| `pt_BR` | Parênteses desequilibrados: %count% %parentheses% de abertura não fechados |
| `ru`    | Несбалансированные скобки: %count% незакрытых открывающих %parentheses%    |
| `sv`    | Obalanserade parenteser: %count% ostängda öppnande %parentheses%           |
| `tr`    | Dengesiz parantezler: %count% kapatılmamış açılış %parentheses%            |
| `uk`    | Незбалансовані дужки: %count% незакритих відкриваючих %parentheses%        |
| `zh_CN` | 括号不匹配：%count%个未关闭的%parentheses%                                            |

### `dsl.unrecognized_term`

| Locale  | Translation                          |
| ------- | ------------------------------------ |
| `de`    | Unerkannter DSL-Begriff: %term%      |
| `en`    | Unrecognized DSL term: %term%        |
| `es`    | Término DSL no reconocido: %term%    |
| `fr`    | Terme DSL non reconnu : %term%       |
| `it`    | Termine DSL non riconosciuto: %term% |
| `ja`    | 認識されないDSL用語です: %term%                |
| `ko`    | 인식되지 않은 DSL 용어: %term%               |
| `nl`    | Onbekende DSL-term: %term%           |
| `pt_BR` | Termo DSL não reconhecido: %term%    |
| `ru`    | Нераспознанный термин DSL: %term%    |
| `sv`    | Okänd DSL-term: %term%               |
| `tr`    | Tanınmayan DSL terimi: %term%        |
| `uk`    | Нерозпізнаний термін DSL: %term%     |
| `zh_CN` | 无法识别的DSL术语：%term%                    |

### `validation.batch_check_empty`

| Locale  | Translation                                                 |
| ------- | ----------------------------------------------------------- |
| `de`    | Batch-Check-Anfrage darf nicht leer sein                    |
| `en`    | Batch check request cannot be empty                         |
| `es`    | La solicitud de verificación por lotes no puede estar vacía |
| `fr`    | La requête de vérification par lot ne peut pas être vide    |
| `it`    | La richiesta di controllo batch non può essere vuota        |
| `ja`    | バッチチェックリクエストを空にすることはできません                                   |
| `ko`    | 배치 확인 요청은 비어있을 수 없습니다                                       |
| `nl`    | Batchcontrole verzoek kan niet leeg zijn                    |
| `pt_BR` | Requisição de verificação em lote não pode estar vazia      |
| `ru`    | Запрос пакетной проверки не может быть пустым               |
| `sv`    | Batch-kontrollförfrågan kan inte vara tom                   |
| `tr`    | Toplu kontrol isteği boş olamaz                             |
| `uk`    | Запит пакетної перевірки не може бути порожнім              |
| `zh_CN` | 批量检查请求不能为空                                                  |

### `validation.invalid_correlation_id`

| Locale  | Translation                                                                                                        |
| ------- | ------------------------------------------------------------------------------------------------------------------ |
| `de`    | Korrelations-ID &#039;%correlationId%&#039; ist ungültig. Muss dem Muster entsprechen: %pattern%                   |
| `en`    | Correlation ID &quot;%correlationId%&quot; is invalid. Must match pattern: %pattern%                               |
| `es`    | ID de correlación &quot;%correlationId%&quot; es inválido. Debe coincidir con el patrón: %pattern%                 |
| `fr`    | L&#039;&#039;ID de corrélation &quot;%correlationId%&quot; est invalide. Doit correspondre au modèle : %pattern%   |
| `it`    | L&#039;&#039;ID di correlazione &quot;%correlationId%&quot; non è valido. Deve corrispondere al pattern: %pattern% |
| `ja`    | 相関ID「%correlationId%」は無効です。パターンと一致する必要があります: %pattern%                                                             |
| `ko`    | 상관관계 ID &quot;%correlationId%&quot;가 잘못되었습니다. 패턴과 일치해야 합니다: %pattern%                                              |
| `nl`    | Correlatie-ID &quot;%correlationId%&quot; is ongeldig. Moet overeenkomen met patroon: %pattern%                    |
| `pt_BR` | ID de correlação &quot;%correlationId%&quot; é inválido. Deve corresponder ao padrão: %pattern%                    |
| `ru`    | ID корреляции &quot;%correlationId%&quot; недопустим. Должен соответствовать шаблону: %pattern%                    |
| `sv`    | Korrelations-ID &quot;%correlationId%&quot; är ogiltigt. Måste matcha mönster: %pattern%                           |
| `tr`    | Korelasyon ID &quot;%correlationId%&quot; geçersiz. Desene uymalı: %pattern%                                       |
| `uk`    | ID кореляції &quot;%correlationId%&quot; недійсний. Має відповідати шаблону: %pattern%                             |
| `zh_CN` | 关联ID&quot;%correlationId%&quot;无效。必须匹配模式：%pattern%                                                                 |

### `auth.jwt.invalid_audience`

| Locale  | Translation                                                                        |
| ------- | ---------------------------------------------------------------------------------- |
| `de`    | JWT-Token-Zielgruppe stimmt nicht mit erwarteter Zielgruppe überein                |
| `en`    | JWT token audience does not match expected audience                                |
| `es`    | La audiencia del token JWT no coincide con la audiencia esperada                   |
| `fr`    | L&#039;audience du jeton JWT ne correspond pas à l&#039;audience attendue          |
| `it`    | L&#039;&#039;audience del token JWT non corrisponde all&#039;&#039;audience atteso |
| `ja`    | JWTトークンの対象者が期待される対象者と一致しません                                                        |
| `ko`    | JWT 토큰 대상이 예상 대상과 일치하지 않습니다                                                        |
| `nl`    | JWT-token doelgroep komt niet overeen met verwachte doelgroep                      |
| `pt_BR` | Audiência do token JWT não corresponde à audiência esperada                        |
| `ru`    | Аудитория JWT токена не соответствует ожидаемой аудитории                          |
| `sv`    | JWT-tokens målgrupp matchar inte förväntad målgrupp                                |
| `tr`    | JWT token hedef kitlesi beklenen hedef kitle ile eşleşmiyor                        |
| `uk`    | Аудиторія JWT токена не відповідає очікуваній аудиторії                            |
| `zh_CN` | JWT令牌受众与期望受众不匹配                                                                    |

### `auth.jwt.invalid_format`

| Locale  | Translation                    |
| ------- | ------------------------------ |
| `de`    | Ungültiges JWT-Token-Format    |
| `en`    | Invalid JWT token format       |
| `es`    | Formato de token JWT inválido  |
| `fr`    | Format de jeton JWT invalide   |
| `it`    | Formato token JWT non valido   |
| `ja`    | 無効なJWTトークン形式です                 |
| `ko`    | 잘못된 JWT 토큰 형식                  |
| `nl`    | Ongeldig JWT-tokenformaat      |
| `pt_BR` | Formato de token JWT inválido  |
| `ru`    | Недопустимый формат JWT токена |
| `sv`    | Ogiltigt JWT-tokenformat       |
| `tr`    | Geçersiz JWT token formatı     |
| `uk`    | Недійсний формат JWT токена    |
| `zh_CN` | 无效的JWT令牌格式                     |

### `auth.jwt.invalid_header`

| Locale  | Translation                |
| ------- | -------------------------- |
| `de`    | Ungültiger JWT-Header      |
| `en`    | Invalid JWT header         |
| `es`    | Encabezado JWT inválido    |
| `fr`    | En-tête JWT invalide       |
| `it`    | Header JWT non valido      |
| `ja`    | 無効なJWTヘッダーです               |
| `ko`    | 잘못된 JWT 헤더                 |
| `nl`    | Ongeldige JWT-header       |
| `pt_BR` | Cabeçalho JWT inválido     |
| `ru`    | Недопустимый заголовок JWT |
| `sv`    | Ogiltig JWT-header         |
| `tr`    | Geçersiz JWT başlığı       |
| `uk`    | Недійсний заголовок JWT    |
| `zh_CN` | 无效的JWT标头                   |

### `auth.jwt.invalid_issuer`

| Locale  | Translation                                                                          |
| ------- | ------------------------------------------------------------------------------------ |
| `de`    | JWT-Token-Aussteller stimmt nicht mit erwartetem Aussteller überein                  |
| `en`    | JWT token issuer does not match expected issuer                                      |
| `es`    | El emisor del token JWT no coincide con el emisor esperado                           |
| `fr`    | L&#039;émetteur du jeton JWT ne correspond pas à l&#039;émetteur attendu             |
| `it`    | L&#039;&#039;emittente del token JWT non corrisponde all&#039;&#039;emittente atteso |
| `ja`    | JWTトークンの発行者が期待される発行者と一致しません                                                          |
| `ko`    | JWT 토큰 발급자가 예상 발급자와 일치하지 않습니다                                                        |
| `nl`    | JWT-token uitgever komt niet overeen met verwachte uitgever                          |
| `pt_BR` | Emissor do token JWT não corresponde ao emissor esperado                             |
| `ru`    | Издатель JWT токена не соответствует ожидаемому издателю                             |
| `sv`    | JWT-tokens utgivare matchar inte förväntad utgivare                                  |
| `tr`    | JWT token veren beklenen veren ile eşleşmiyor                                        |
| `uk`    | Видавець JWT токена не відповідає очікуваному видавцю                                |
| `zh_CN` | JWT令牌颁发者与期望颁发者不匹配                                                                    |

### `auth.jwt.invalid_payload`

| Locale  | Translation                        |
| ------- | ---------------------------------- |
| `de`    | Ungültige JWT-Nutzlast             |
| `en`    | Invalid JWT payload                |
| `es`    | Carga útil JWT inválida            |
| `fr`    | Charge utile JWT invalide          |
| `it`    | Payload JWT non valido             |
| `ja`    | 無効なJWTペイロードです                      |
| `ko`    | 잘못된 JWT 페이로드                       |
| `nl`    | Ongeldige JWT-payload              |
| `pt_BR` | Payload JWT inválido               |
| `ru`    | Недопустимая полезная нагрузка JWT |
| `sv`    | Ogiltig JWT-payload                |
| `tr`    | Geçersiz JWT yükü                  |
| `uk`    | Недійсне корисне навантаження JWT  |
| `zh_CN` | 无效的JWT有效负载                         |

### `auth.jwt.missing_required_claims`

| Locale  | Translation                              |
| ------- | ---------------------------------------- |
| `de`    | Erforderliche JWT-Claims fehlen          |
| `en`    | Missing required JWT claims              |
| `es`    | Faltan claims requeridos en el JWT       |
| `fr`    | Revendications JWT requises manquantes   |
| `it`    | Claims JWT obbligatori mancanti          |
| `ja`    | 必要なJWTクレームがありません                         |
| `ko`    | 필수 JWT 클레임이 누락되었습니다                      |
| `nl`    | Ontbrekende vereiste JWT-claims          |
| `pt_BR` | Claims JWT obrigatórios faltando         |
| `ru`    | Отсутствуют обязательные утверждения JWT |
| `sv`    | Saknade obligatoriska JWT-anspråk        |
| `tr`    | Gerekli JWT talepleri eksik              |
| `uk`    | Відсутні обов&#039;язкові твердження JWT |
| `zh_CN` | 缺少必需的JWT声明                               |

### `auth.jwt.token_expired`

| Locale  | Translation              |
| ------- | ------------------------ |
| `de`    | JWT-Token ist abgelaufen |
| `en`    | JWT token has expired    |
| `es`    | El token JWT ha expirado |
| `fr`    | Le jeton JWT a expiré    |
| `it`    | Il token JWT è scaduto   |
| `ja`    | JWTトークンの有効期限が切れました       |
| `ko`    | JWT 토큰이 만료되었습니다          |
| `nl`    | JWT-token is verlopen    |
| `pt_BR` | Token JWT expirou        |
| `ru`    | JWT токен истек          |
| `sv`    | JWT-token har gått ut    |
| `tr`    | JWT token süresi doldu   |
| `uk`    | JWT токен закінчився     |
| `zh_CN` | JWT令牌已过期                 |

### `auth.jwt.token_not_yet_valid`

| Locale  | Translation                               |
| ------- | ----------------------------------------- |
| `de`    | JWT-Token ist noch nicht gültig           |
| `en`    | JWT token is not yet valid                |
| `es`    | El token JWT aún no es válido             |
| `fr`    | Le jeton JWT n&#039;est pas encore valide |
| `it`    | Il token JWT non è ancora valido          |
| `ja`    | JWTトークンはまだ有効ではありません                       |
| `ko`    | JWT 토큰이 아직 유효하지 않습니다                      |
| `nl`    | JWT-token is nog niet geldig              |
| `pt_BR` | Token JWT ainda não é válido              |
| `ru`    | JWT токен еще не действителен             |
| `sv`    | JWT-token är inte giltigt ännu            |
| `tr`    | JWT token henüz geçerli değil             |
| `uk`    | JWT токен ще не дійсний                   |
| `zh_CN` | JWT令牌尚未生效                                 |

### `model.duplicate_type`

| Locale  | Translation                                          |
| ------- | ---------------------------------------------------- |
| `de`    | Doppelte Typdefinition gefunden: %type%              |
| `en`    | Duplicate type definition found: %type%              |
| `es`    | Se encontró una definición de tipo duplicada: %type% |
| `fr`    | Définition de type dupliquée trouvée : %type%        |
| `it`    | Definizione di tipo duplicata trovata: %type%        |
| `ja`    | 重複するタイプ定義が見つかりました: %type%                            |
| `ko`    | 중복된 타입 정의를 발견했습니다: %type%                            |
| `nl`    | Dubbele typedefinitie gevonden: %type%               |
| `pt_BR` | Definição de tipo duplicada encontrada: %type%       |
| `ru`    | Найдено дублирующееся определение типа: %type%       |
| `sv`    | Dubblerad typdefinition hittades: %type%             |
| `tr`    | Yinelenen tür tanımı bulundu: %type%                 |
| `uk`    | Знайдено дублікат визначення типу: %type%            |
| `zh_CN` | 发现重复的类型定义：%type%                                     |

### `model.invalid_identifier_format`

| Locale  | Translation                                                                                                              |
| ------- | ------------------------------------------------------------------------------------------------------------------------ |
| `de`    | Ungültiges Bezeichnerformat: Bezeichner dürfen keine Leerzeichen enthalten. Gefunden in %identifier%                     |
| `en`    | Invalid identifier format: identifiers cannot contain whitespace. Found in %identifier%                                  |
| `es`    | Formato de identificador inválido: los identificadores no pueden contener espacios en blanco. Encontrado en %identifier% |
| `fr`    | Format d&#039;identifiant invalide : les identifiants ne peuvent pas contenir d&#039;espaces. Trouvé dans %identifier%   |
| `it`    | Formato identificatore non valido: gli identificatori non possono contenere spazi. Trovato in %identifier%               |
| `ja`    | 無効な識別子形式です: 識別子に空白文字を含めることはできません。%identifier%で見つかりました                                                                    |
| `ko`    | 잘못된 식별자 형식: 식별자에는 공백이 포함될 수 없습니다. %identifier%에서 발견되었습니다                                                                 |
| `nl`    | Ongeldig identificatieformaat: identificaties kunnen geen witruimte bevatten. Gevonden in %identifier%                   |
| `pt_BR` | Formato de identificador inválido: identificadores não podem conter espaços em branco. Encontrado em %identifier%        |
| `ru`    | Недопустимый формат идентификатора: идентификаторы не могут содержать пробелы. Найдено в %identifier%                    |
| `sv`    | Ogiltigt identifierarformat: identifierare kan inte innehålla mellanslag. Hittades i %identifier%                        |
| `tr`    | Geçersiz tanımlayıcı formatı: tanımlayıcılar boşluk içeremez. %identifier% içinde bulundu                                |
| `uk`    | Недійсний формат ідентифікатора: ідентифікатори не можуть містити пробіли. Знайдено в %identifier%                       |
| `zh_CN` | 无效的标识符格式：标识符不能包含空白字符。在%identifier%中发现                                                                                    |

### `model.invalid_tuple_key`

| Locale  | Translation                                                  |
| ------- | ------------------------------------------------------------ |
| `de`    | Ungültiger tuple_key für Assertion::fromArray bereitgestellt |
| `en`    | Invalid tuple_key provided to Assertion::fromArray           |
| `es`    | tuple_key inválido proporcionado a Assertion::fromArray      |
| `fr`    | tuple_key invalide fourni à Assertion::fromArray             |
| `it`    | tuple_key non valido fornito ad Assertion::fromArray         |
| `ja`    | Assertion::fromArrayに無効なtuple_keyが提供されました                    |
| `ko`    | Assertion::fromArray에 잘못된 tuple_key가 제공되었습니다                 |
| `nl`    | Ongeldige tuple_key verstrekt aan Assertion::fromArray       |
| `pt_BR` | tuple_key inválido fornecido para Assertion::fromArray       |
| `ru`    | Недопустимый tuple_key предоставлен для Assertion::fromArray |
| `sv`    | Ogiltig tuple_key tillhandahållen till Assertion::fromArray  |
| `tr`    | Assertion::fromArray için geçersiz tuple_key sağlandı        |
| `uk`    | Недійсний tuple_key надано для Assertion::fromArray          |
| `zh_CN` | 提供给Assertion::fromArray的tuple_key无效                          |

### `model.leaf_missing_content`

| Locale  | Translation                                                                              |
| ------- | ---------------------------------------------------------------------------------------- |
| `de`    | Blatt muss mindestens eines von users, computed oder tupleToUserset enthalten            |
| `en`    | Leaf must contain at least one of users, computed or tupleToUserset                      |
| `es`    | Leaf debe contener al menos uno de: users, computed o tupleToUserset                     |
| `fr`    | Leaf doit contenir au moins un des éléments suivants : users, computed ou tupleToUserset |
| `it`    | Leaf deve contenere almeno uno tra users, computed o tupleToUserset                      |
| `ja`    | LeafにはusersまたはcomputedまたはtupleToUsersetのうち少なくとも1つが含まれている必要があります                          |
| `ko`    | Leaf는 users, computed 또는 tupleToUserset 중 적어도 하나를 포함해야 합니다                               |
| `nl`    | Leaf moet ten minste één van users, computed of tupleToUserset bevatten                  |
| `pt_BR` | Leaf deve conter pelo menos um de users, computed ou tupleToUserset                      |
| `ru`    | Leaf должен содержать хотя бы одно из users, computed или tupleToUserset                 |
| `sv`    | Leaf måste innehålla minst en av users, computed eller tupleToUserset                    |
| `tr`    | Leaf en az bir users, computed veya tupleToUserset içermelidir                           |
| `uk`    | Leaf має містити принаймні одне з users, computed або tupleToUserset                     |
| `zh_CN` | Leaf必须包含users、computed或tupleToUserset中的至少一个                                              |

### `model.no_models_in_store`

| Locale  | Translation                                                        |
| ------- | ------------------------------------------------------------------ |
| `de`    | Keine Autorisierungsmodelle in Store %store_id% gefunden           |
| `en`    | No authorization models found in store %store_id%                  |
| `es`    | No se encontraron modelos de autorización en el almacén %store_id% |
| `fr`    | Aucun modèle d&#039;autorisation trouvé dans le magasin %store_id% |
| `it`    | Nessun modello di autorizzazione trovato nello store %store_id%    |
| `ja`    | ストア%store_id%に認可モデルが見つかりません                                        |
| `ko`    | 스토어 %store_id%에서 인증 모델을 찾을 수 없습니다                                  |
| `nl`    | Geen autorisatiemodellen gevonden in store %store_id%              |
| `pt_BR` | Nenhum modelo de autorização encontrado no store %store_id%        |
| `ru`    | Модели авторизации не найдены в хранилище %store_id%               |
| `sv`    | Inga auktorisationsmodeller hittades i butik %store_id%            |
| `tr`    | %store_id% mağazasında yetkilendirme modeli bulunamadı             |
| `uk`    | Моделі авторизації не знайдені у сховищі %store_id%                |
| `zh_CN` | 在存储%store_id%中未找到授权模型                                              |

### `model.source_info_file_empty`

| Locale  | Translation                              |
| ------- | ---------------------------------------- |
| `de`    | SourceInfo::$file darf nicht leer sein.  |
| `en`    | SourceInfo::$file cannot be empty.       |
| `es`    | SourceInfo::$file no puede estar vacío.  |
| `fr`    | SourceInfo::$file ne peut pas être vide. |
| `it`    | SourceInfo::$file non può essere vuoto.  |
| `ja`    | SourceInfo::$fileを空にすることはできません。          |
| `ko`    | SourceInfo::$file은 비어있을 수 없습니다.          |
| `nl`    | SourceInfo::$file kan niet leeg zijn.    |
| `pt_BR` | SourceInfo::$file não pode estar vazio.  |
| `ru`    | SourceInfo::$file не может быть пустым.  |
| `sv`    | SourceInfo::$file kan inte vara tom.     |
| `tr`    | SourceInfo::$file boş olamaz.            |
| `uk`    | SourceInfo::$file не може бути порожнім. |
| `zh_CN` | SourceInfo::$file不能为空。                   |

### `model.type_definitions_empty`

| Locale  | Translation                                       |
| ------- | ------------------------------------------------- |
| `de`    | Typdefinitionen dürfen nicht leer sein            |
| `en`    | Type definitions cannot be empty                  |
| `es`    | Las definiciones de tipo no pueden estar vacías   |
| `fr`    | Les définitions de type ne peuvent pas être vides |
| `it`    | Le definizioni di tipo non possono essere vuote   |
| `ja`    | タイプ定義を空にすることはできません                                |
| `ko`    | 타입 정의는 비어있을 수 없습니다                                |
| `nl`    | Typedefinities kunnen niet leeg zijn              |
| `pt_BR` | Definições de tipo não podem estar vazias         |
| `ru`    | Определения типов не могут быть пустыми           |
| `sv`    | Typdefinitioner kan inte vara tomma               |
| `tr`    | Tür tanımları boş olamaz                          |
| `uk`    | Визначення типів не можуть бути порожніми         |
| `zh_CN` | 类型定义不能为空                                          |

### `model.typed_wildcard_type_empty`

| Locale  | Translation                                 |
| ------- | ------------------------------------------- |
| `de`    | TypedWildcard::$type darf nicht leer sein.  |
| `en`    | TypedWildcard::$type cannot be empty.       |
| `es`    | TypedWildcard::$type no puede estar vacío.  |
| `fr`    | TypedWildcard::$type ne peut pas être vide. |
| `it`    | TypedWildcard::$type non può essere vuoto.  |
| `ja`    | TypedWildcard::$typeを空にすることはできません。          |
| `ko`    | TypedWildcard::$type은 비어있을 수 없습니다.          |
| `nl`    | TypedWildcard::$type kan niet leeg zijn.    |
| `pt_BR` | TypedWildcard::$type não pode estar vazio.  |
| `ru`    | TypedWildcard::$type не может быть пустым.  |
| `sv`    | TypedWildcard::$type kan inte vara tom.     |
| `tr`    | TypedWildcard::$type boş olamaz.            |
| `uk`    | TypedWildcard::$type не може бути порожнім. |
| `zh_CN` | TypedWildcard::$type不能为空。                   |

### `network.error`

| Locale  | Translation                 |
| ------- | --------------------------- |
| `de`    | Netzwerkfehler: %message%   |
| `en`    | Network error: %message%    |
| `es`    | Error de red: %message%     |
| `fr`    | Erreur réseau : %message%   |
| `it`    | Errore di rete: %message%   |
| `ja`    | ネットワークエラー: %message%        |
| `ko`    | 네트워크 오류: %message%          |
| `nl`    | Netwerkfout: %message%      |
| `pt_BR` | Erro de rede: %message%     |
| `ru`    | Сетевая ошибка: %message%   |
| `sv`    | Nätverksfel: %message%      |
| `tr`    | Ağ hatası: %message%        |
| `uk`    | Мережева помилка: %message% |
| `zh_CN` | 网络错误：%message%              |

### `exception.network.conflict`

| Locale  | Translation                                                             |
| ------- | ----------------------------------------------------------------------- |
| `de`    | Konflikt (409): Die Anfrage steht im Konflikt mit dem aktuellen Zustand |
| `en`    | Conflict (409): The request conflicts with the current state            |
| `es`    | Conflicto (409): La solicitud entra en conflicto con el estado actual   |
| `fr`    | Conflit (409) : La requête entre en conflit avec l&#039;état actuel     |
| `it`    | Conflitto (409): La richiesta è in conflitto con lo stato attuale       |
| `ja`    | 競合 (409): リクエストが現在の状態と競合しています                                           |
| `ko`    | 충돌 (409): 요청이 현재 상태와 충돌합니다                                              |
| `nl`    | Conflict (409): Het verzoek conflicteert met de huidige staat           |
| `pt_BR` | Conflito (409): A requisição conflita com o estado atual                |
| `ru`    | Конфликт (409): Запрос конфликтует с текущим состоянием                 |
| `sv`    | Konflikt (409): Begäran står i konflikt med nuvarande tillstånd         |
| `tr`    | Çakışma (409): İstek mevcut durumla çakışıyor                           |
| `uk`    | Конфлікт (409): Запит конфліктує з поточним станом                      |
| `zh_CN` | 冲突(409)：请求与当前状态冲突                                                       |

### `exception.network.forbidden`

| Locale  | Translation                                                       |
| ------- | ----------------------------------------------------------------- |
| `de`    | Verboten (403): Zugriff auf die angeforderte Ressource verweigert |
| `en`    | Forbidden (403): Access denied to the requested resource          |
| `es`    | Prohibido (403): Acceso denegado al recurso solicitado            |
| `fr`    | Interdit (403) : Accès refusé à la ressource demandée             |
| `it`    | Vietato (403): Accesso negato alla risorsa richiesta              |
| `ja`    | 禁止 (403): 要求されたリソースへのアクセスが拒否されました                                 |
| `ko`    | 금지됨 (403): 요청된 리소스에 대한 액세스가 거부되었습니다                               |
| `nl`    | Verboden (403): Toegang geweigerd tot de gevraagde bron           |
| `pt_BR` | Proibido (403): Acesso negado ao recurso solicitado               |
| `ru`    | Запрещено (403): Доступ к запрашиваемому ресурсу запрещен         |
| `sv`    | Förbjuden (403): Åtkomst nekad till begärd resurs                 |
| `tr`    | Yasak (403): İstenen kaynağa erişim reddedildi                    |
| `uk`    | Заборонено (403): Доступ до запитуваного ресурсу заборонений      |
| `zh_CN` | 禁止(403)：拒绝访问请求的资源                                                 |

### `exception.network.invalid`

| Locale  | Translation                                           |
| ------- | ----------------------------------------------------- |
| `de`    | Ungültige Anfrage (400): Die Anfrage ist ungültig     |
| `en`    | Bad Request (400): The request is invalid             |
| `es`    | Solicitud incorrecta (400): La solicitud no es válida |
| `fr`    | Requête incorrecte (400) : La requête est invalide    |
| `it`    | Richiesta non valida (400): La richiesta non è valida |
| `ja`    | 無効なリクエスト (400): リクエストが無効です                            |
| `ko`    | 잘못된 요청 (400): 요청이 잘못되었습니다                             |
| `nl`    | Slecht Verzoek (400): Het verzoek is ongeldig         |
| `pt_BR` | Requisição Inválida (400): A requisição é inválida    |
| `ru`    | Неверный запрос (400): Запрос недействителен          |
| `sv`    | Dålig begäran (400): Begäran är ogiltig               |
| `tr`    | Hatalı İstek (400): İstek geçersiz                    |
| `uk`    | Невірний запит (400): Запит недійсний                 |
| `zh_CN` | 错误请求(400)：请求无效                                        |

### `exception.network.request`

| Locale  | Translation                                                            |
| ------- | ---------------------------------------------------------------------- |
| `de`    | Anfrage fehlgeschlagen: HTTP-Anfrage konnte nicht abgeschlossen werden |
| `en`    | Request failed: Unable to complete the HTTP request                    |
| `es`    | Solicitud fallida: No se pudo completar la solicitud HTTP              |
| `fr`    | Échec de la requête : Impossible de terminer la requête HTTP           |
| `it`    | Richiesta fallita: Impossibile completare la richiesta HTTP            |
| `ja`    | リクエスト失敗: HTTPリクエストを完了できませんでした                                          |
| `ko`    | 요청 실패: HTTP 요청을 완료할 수 없습니다                                             |
| `nl`    | Verzoek mislukt: Kan het HTTP-verzoek niet voltooien                   |
| `pt_BR` | Requisição falhou: Não foi possível completar a requisição HTTP        |
| `ru`    | Запрос не удался: Невозможно выполнить HTTP запрос                     |
| `sv`    | Begäran misslyckades: Kunde inte slutföra HTTP-begäran                 |
| `tr`    | İstek başarısız: HTTP isteği tamamlanamadı                             |
| `uk`    | Запит не вдався: Неможливо завершити HTTP запит                        |
| `zh_CN` | 请求失败：无法完成HTTP请求                                                        |

### `exception.network.server`

| Locale  | Translation                                                           |
| ------- | --------------------------------------------------------------------- |
| `de`    | Interner Serverfehler (500): Der Server ist auf einen Fehler gestoßen |
| `en`    | Internal Server Error (500): The server encountered an error          |
| `es`    | Error interno del servidor (500): El servidor encontró un error       |
| `fr`    | Erreur interne du serveur (500) : Le serveur a rencontré une erreur   |
| `it`    | Errore interno del server (500): Il server ha incontrato un errore    |
| `ja`    | 内部サーバーエラー (500): サーバーでエラーが発生しました                                      |
| `ko`    | 내부 서버 오류 (500): 서버에서 오류가 발생했습니다                                       |
| `nl`    | Interne Serverfout (500): De server ondervond een fout                |
| `pt_BR` | Erro Interno do Servidor (500): O servidor encontrou um erro          |
| `ru`    | Внутренняя ошибка сервера (500): На сервере произошла ошибка          |
| `sv`    | Internt serverfel (500): Servern stötte på ett fel                    |
| `tr`    | İç Sunucu Hatası (500): Sunucuda hata oluştu                          |
| `uk`    | Внутрішня помилка сервера (500): На сервері сталася помилка           |
| `zh_CN` | 内部服务器错误(500)：服务器遇到错误                                                  |

### `exception.network.timeout`

| Locale  | Translation                                                                    |
| ------- | ------------------------------------------------------------------------------ |
| `de`    | Nicht verarbeitbare Entität (422): Die Anfrage konnte nicht verarbeitet werden |
| `en`    | Unprocessable Entity (422): The request could not be processed                 |
| `es`    | Entidad no procesable (422): No se pudo procesar la solicitud                  |
| `fr`    | Entité non traitable (422) : La requête n&#039;a pas pu être traitée           |
| `it`    | Entità non processabile (422): La richiesta non può essere processata          |
| `ja`    | 処理不可能エンティティ (422): リクエストを処理できませんでした                                            |
| `ko`    | 처리할 수 없는 엔티티 (422): 요청을 처리할 수 없습니다                                             |
| `nl`    | Onverwerkbare Entiteit (422): Het verzoek kon niet verwerkt worden             |
| `pt_BR` | Entidade Não Processável (422): A requisição não pôde ser processada           |
| `ru`    | Необработанная сущность (422): Запрос не может быть обработан                  |
| `sv`    | Obearbetbar entitet (422): Begäran kunde inte bearbetas                        |
| `tr`    | İşlenemeyen Varlık (422): İstek işlenemedi                                     |
| `uk`    | Необроблювана сутність (422): Запит не може бути оброблений                    |
| `zh_CN` | 无法处理的实体(422)：无法处理请求                                                            |

### `exception.network.unauthenticated`

| Locale  | Translation                                             |
| ------- | ------------------------------------------------------- |
| `de`    | Nicht autorisiert (401): Authentifizierung erforderlich |
| `en`    | Unauthorized (401): Authentication required             |
| `es`    | No autorizado (401): Se requiere autenticación          |
| `fr`    | Non autorisé (401) : Authentification requise           |
| `it`    | Non autorizzato (401): Autenticazione richiesta         |
| `ja`    | 未認証 (401): 認証が必要です                                      |
| `ko`    | 인증되지 않음 (401): 인증이 필요합니다                                |
| `nl`    | Ongeautoriseerd (401): Authenticatie vereist            |
| `pt_BR` | Não Autorizado (401): Autenticação necessária           |
| `ru`    | Неавторизован (401): Требуется аутентификация           |
| `sv`    | Obehörig (401): Autentisering krävs                     |
| `tr`    | Yetkisiz (401): Kimlik doğrulama gerekli                |
| `uk`    | Неавторизований (401): Потрібна автентифікація          |
| `zh_CN` | 未授权(401)：需要身份验证                                         |

### `exception.network.undefined_endpoint`

| Locale  | Translation                                                          |
| ------- | -------------------------------------------------------------------- |
| `de`    | Nicht gefunden (404): Der angeforderte Endpunkt existiert nicht      |
| `en`    | Not Found (404): The requested endpoint does not exist               |
| `es`    | No encontrado (404): El endpoint solicitado no existe                |
| `fr`    | Non trouvé (404) : Le point de terminaison demandé n&#039;existe pas |
| `it`    | Non trovato (404): L&#039;&#039;endpoint richiesto non esiste        |
| `ja`    | 見つかりません (404): 要求されたエンドポイントは存在しません                                   |
| `ko`    | 찾을 수 없음 (404): 요청된 엔드포인트가 존재하지 않습니다                                  |
| `nl`    | Niet Gevonden (404): Het gevraagde eindpunt bestaat niet             |
| `pt_BR` | Não Encontrado (404): O endpoint solicitado não existe               |
| `ru`    | Не найдено (404): Запрашиваемая конечная точка не существует         |
| `sv`    | Inte hittad (404): Den begärda slutpunkten existerar inte            |
| `tr`    | Bulunamadı (404): İstenen uç nokta mevcut değil                      |
| `uk`    | Не знайдено (404): Запитувана кінцева точка не існує                 |
| `zh_CN` | 未找到(404)：请求的端点不存在                                                    |

### `exception.network.unexpected`

| Locale  | Translation                         |
| ------- | ----------------------------------- |
| `de`    | Unerwartete Antwort vom Server      |
| `en`    | Unexpected response from the server |
| `es`    | Respuesta inesperada del servidor   |
| `fr`    | Réponse inattendue du serveur       |
| `it`    | Risposta inaspettata dal server     |
| `ja`    | サーバーからの予期しないレスポンス                   |
| `ko`    | 서버로부터 예상치 못한 응답                     |
| `nl`    | Onverwachte respons van de server   |
| `pt_BR` | Resposta inesperada do servidor     |
| `ru`    | Неожиданный ответ от сервера        |
| `sv`    | Oväntat svar från servern           |
| `tr`    | Sunucudan beklenmeyen yanıt         |
| `uk`    | Неочікувана відповідь від сервера   |
| `zh_CN` | 来自服务器的意外响应                          |

### `network.unexpected_status`

| Locale  | Translation                                                                    |
| ------- | ------------------------------------------------------------------------------ |
| `de`    | API antwortete mit einem unerwarteten Statuscode: %status_code%                |
| `en`    | API responded with an unexpected status code: %status_code%                    |
| `es`    | La API respondió con un código de estado inesperado: %status_code%             |
| `fr`    | L&#039;API a répondu avec un code de statut inattendu : %status_code%          |
| `it`    | L&#039;&#039;API ha risposto con un codice di stato inaspettato: %status_code% |
| `ja`    | APIが予期しないステータスコードで応答しました: %status_code%                                        |
| `ko`    | API가 예상치 못한 상태 코드로 응답했습니다: %status_code%                                       |
| `nl`    | API reageerde met een onverwachte statuscode: %status_code%                    |
| `pt_BR` | API respondeu com código de status inesperado: %status_code%                   |
| `ru`    | API ответил неожиданным кодом состояния: %status_code%                         |
| `sv`    | API svarade med en oväntad statuskod: %status_code%                            |
| `tr`    | API beklenmeyen durum koduyla yanıt verdi: %status_code%                       |
| `uk`    | API відповів неочікуваним кодом стану: %status_code%                           |
| `zh_CN` | API返回了意外的状态代码：%status_code%                                                    |

### `client.no_last_request_found`

| Locale  | Translation                          |
| ------- | ------------------------------------ |
| `de`    | Keine letzte Anfrage gefunden        |
| `en`    | No last request found                |
| `es`    | No se encontró la última solicitud   |
| `fr`    | Aucune dernière requête trouvée      |
| `it`    | Nessuna ultima richiesta trovata     |
| `ja`    | 最後のリクエストが見つかりません                     |
| `ko`    | 마지막 요청을 찾을 수 없습니다                    |
| `nl`    | Geen laatste verzoek gevonden        |
| `pt_BR` | Nenhuma última requisição encontrada |
| `ru`    | Последний запрос не найден           |
| `sv`    | Ingen senaste förfrågan hittades     |
| `tr`    | Son istek bulunamadı                 |
| `uk`    | Останній запит не знайдено           |
| `zh_CN` | 未找到最后的请求                             |

### `request.continuation_token_empty`

| Locale  | Translation                                    |
| ------- | ---------------------------------------------- |
| `de`    | Fortsetzungstoken darf nicht leer sein         |
| `en`    | Continuation token cannot be empty             |
| `es`    | El token de continuación no puede estar vacío  |
| `fr`    | Le jeton de continuation ne peut pas être vide |
| `it`    | Il token di continuazione non può essere vuoto |
| `ja`    | 継続トークンを空にすることはできません                            |
| `ko`    | 연속 토큰은 비어있을 수 없습니다                             |
| `nl`    | Vervolgtoken kan niet leeg zijn                |
| `pt_BR` | Token de continuação não pode estar vazio      |
| `ru`    | Токен продолжения не может быть пустым         |
| `sv`    | Fortsättningstoken kan inte vara tomt          |
| `tr`    | Devam tokeni boş olamaz                        |
| `uk`    | Токен продовження не може бути порожнім        |
| `zh_CN` | 继续令牌不能为空                                       |

### `request.model_id_empty`

| Locale  | Translation                                                        |
| ------- | ------------------------------------------------------------------ |
| `de`    | Autorisierungsmodell-ID darf nicht leer sein                       |
| `en`    | Authorization Model ID cannot be empty                             |
| `es`    | El ID del modelo de autorización no puede estar vacío              |
| `fr`    | L&#039;ID du modèle d&#039;autorisation ne peut pas être vide      |
| `it`    | L&#039;&#039;ID del modello di autorizzazione non può essere vuoto |
| `ja`    | 認可モデルIDを空にすることはできません                                               |
| `ko`    | 인증 모델 ID는 비어있을 수 없습니다                                              |
| `nl`    | Autorisatiemodel-ID kan niet leeg zijn                             |
| `pt_BR` | ID do modelo de autorização não pode estar vazio                   |
| `ru`    | ID модели авторизации не может быть пустым                         |
| `sv`    | Auktorisationsmodell-ID kan inte vara tomt                         |
| `tr`    | Yetkilendirme Modeli ID boş olamaz                                 |
| `uk`    | ID моделі авторизації не може бути порожнім                        |
| `zh_CN` | 授权模型ID不能为空                                                         |

### `request.object_empty`

| Locale  | Translation                               |
| ------- | ----------------------------------------- |
| `de`    | Objekt darf nicht leer sein               |
| `en`    | Object cannot be empty                    |
| `es`    | El objeto no puede estar vacío            |
| `fr`    | L&#039;objet ne peut pas être vide        |
| `it`    | L&#039;&#039;oggetto non può essere vuoto |
| `ja`    | オブジェクトを空にすることはできません                       |
| `ko`    | 객체는 비어있을 수 없습니다                           |
| `nl`    | Object kan niet leeg zijn                 |
| `pt_BR` | Objeto não pode estar vazio               |
| `ru`    | Объект не может быть пустым               |
| `sv`    | Objekt kan inte vara tomt                 |
| `tr`    | Nesne boş olamaz                          |
| `uk`    | Об&#039;єкт не може бути порожнім         |
| `zh_CN` | 对象不能为空                                    |

### `request.object_type_empty`

| Locale  | Translation                                |
| ------- | ------------------------------------------ |
| `de`    | Objekttyp darf nicht leer sein             |
| `en`    | Object type cannot be empty                |
| `es`    | El tipo de objeto no puede estar vacío     |
| `fr`    | Le type d&#039;objet ne peut pas être vide |
| `it`    | Il tipo di oggetto non può essere vuoto    |
| `ja`    | オブジェクトタイプを空にすることはできません                     |
| `ko`    | 객체 타입은 비어있을 수 없습니다                         |
| `nl`    | Objecttype kan niet leeg zijn              |
| `pt_BR` | Tipo de objeto não pode estar vazio        |
| `ru`    | Тип объекта не может быть пустым           |
| `sv`    | Objekttyp kan inte vara tom                |
| `tr`    | Nesne türü boş olamaz                      |
| `uk`    | Тип об&#039;єкта не може бути порожнім     |
| `zh_CN` | 对象类型不能为空                                   |

### `request.page_size_invalid`

| Locale  | Translation                                        |
| ------- | -------------------------------------------------- |
| `de`    | Ungültige pageSize für %className% bereitgestellt  |
| `en`    | Invalid pageSize provided to %className%           |
| `es`    | pageSize inválido proporcionado a %className%      |
| `fr`    | pageSize invalide fourni à %className%             |
| `it`    | pageSize non valido fornito a %className%          |
| `ja`    | %className%に無効なpageSizeが提供されました                    |
| `ko`    | %className%에 잘못된 pageSize가 제공되었습니다                 |
| `nl`    | Ongeldige pageSize verstrekt aan %className%       |
| `pt_BR` | pageSize inválido fornecido para %className%       |
| `ru`    | Недопустимый pageSize предоставлен для %className% |
| `sv`    | Ogiltig pageSize tillhandahållen till %className%  |
| `tr`    | %className% için geçersiz pageSize sağlandı        |
| `uk`    | Недійсний pageSize надано для %className%          |
| `zh_CN` | 提供给%className%的pageSize无效                          |

### `request.relation_empty`

| Locale  | Translation                       |
| ------- | --------------------------------- |
| `de`    | Relation darf nicht leer sein     |
| `en`    | Relation cannot be empty          |
| `es`    | La relación no puede estar vacía  |
| `fr`    | La relation ne peut pas être vide |
| `it`    | La relazione non può essere vuota |
| `ja`    | 関係を空にすることはできません                   |
| `ko`    | 관계는 비어있을 수 없습니다                   |
| `nl`    | Relatie kan niet leeg zijn        |
| `pt_BR` | Relação não pode estar vazia      |
| `ru`    | Отношение не может быть пустым    |
| `sv`    | Relation kan inte vara tom        |
| `tr`    | İlişki boş olamaz                 |
| `uk`    | Відношення не може бути порожнім  |
| `zh_CN` | 关系不能为空                            |

### `request.store_id_empty`

| Locale  | Translation                                      |
| ------- | ------------------------------------------------ |
| `de`    | Store-ID darf nicht leer sein                    |
| `en`    | Store ID cannot be empty                         |
| `es`    | El ID del almacén no puede estar vacío           |
| `fr`    | L&#039;ID du magasin ne peut pas être vide       |
| `it`    | L&#039;&#039;ID dello store non può essere vuoto |
| `ja`    | ストアIDを空にすることはできません                               |
| `ko`    | 스토어 ID는 비어있을 수 없습니다                              |
| `nl`    | Store-ID kan niet leeg zijn                      |
| `pt_BR` | ID do store não pode estar vazio                 |
| `ru`    | ID хранилища не может быть пустым                |
| `sv`    | Butiks-ID kan inte vara tomt                     |
| `tr`    | Mağaza ID boş olamaz                             |
| `uk`    | ID сховища не може бути порожнім                 |
| `zh_CN` | 存储ID不能为空                                         |

### `request.store_name_empty`

| Locale  | Translation                                |
| ------- | ------------------------------------------ |
| `de`    | Store-Name darf nicht leer sein            |
| `en`    | Store name cannot be empty                 |
| `es`    | El nombre del almacén no puede estar vacío |
| `fr`    | Le nom du magasin ne peut pas être vide    |
| `it`    | Il nome dello store non può essere vuoto   |
| `ja`    | ストア名を空にすることはできません                          |
| `ko`    | 스토어 이름은 비어있을 수 없습니다                        |
| `nl`    | Storenaam kan niet leeg zijn               |
| `pt_BR` | Nome do store não pode estar vazio         |
| `ru`    | Имя хранилища не может быть пустым         |
| `sv`    | Butiksnamn kan inte vara tomt              |
| `tr`    | Mağaza adı boş olamaz                      |
| `uk`    | Ім&#039;я сховища не може бути порожнім    |
| `zh_CN` | 存储名称不能为空                                   |

### `request.transactional_limit_exceeded`

| Locale  | Translation                                                                                                                                                             |
| ------- | ----------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `de`    | Transaktionale writeTuples-Grenze überschritten: %count% Operationen (max. 100). Verwenden Sie den nicht-transaktionalen Modus oder teilen Sie in mehrere Anfragen auf. |
| `en`    | Transactional writeTuples exceeded limit: %count% operations (max 100). Use non-transactional mode or split into multiple requests.                                     |
| `es`    | WriteTuples transaccional excedió el límite: %count% operaciones (máx. 100). Use modo no transaccional o divida en múltiples solicitudes.                               |
| `fr`    | Limite de writeTuples transactionnel dépassée : %count% opérations (max 100). Utilisez le mode non transactionnel ou divisez en plusieurs requêtes.                     |
| `it`    | writeTuples transazionale ha superato il limite: %count% operazioni (massimo 100). Usa la modalità non transazionale o dividi in più richieste.                         |
| `ja`    | トランザクショナルwriteTuplesが制限を超えました: %count%個の操作（最大100個）。非トランザクショナルモードを使用するか、複数のリクエストに分割してください。                                                                               |
| `ko`    | 트랜잭션 writeTuples가 제한을 초과했습니다: %count%개 작업 (최대 100개). 비트랜잭션 모드를 사용하거나 여러 요청으로 분할하세요.                                                                                     |
| `nl`    | Transactionele writeTuples heeft limiet overschreden: %count% operaties (max 100). Gebruik niet-transactionele modus of verdeel over meerdere verzoeken.                |
| `pt_BR` | WriteTuples transacional excedeu limite: %count% operações (máx 100). Use modo não-transacional ou divida em múltiplas requisições.                                     |
| `ru`    | Транзакционный writeTuples превысил лимит: %count% операций (максимум 100). Используйте нетранзакционный режим или разделите на несколько запросов.                     |
| `sv`    | Transaktionell writeTuples överskred gränsen: %count% operationer (max 100). Använd icke-transaktionellt läge eller dela upp i flera förfrågningar.                     |
| `tr`    | İşlemsel writeTuples sınırı aştı: %count% işlem (maksimum 100). İşlemsel olmayan modu kullanın veya birden fazla isteğe bölün.                                          |
| `uk`    | Транзакційний writeTuples перевищив ліміт: %count% операцій (максимум 100). Використовуйте нетранзакційний режим або розділіть на кілька запитів.                       |
| `zh_CN` | 事务性writeTuples超出限制：%count%个操作（最大100个）。请使用非事务模式或拆分为多个请求。                                                                                                                 |

### `request.type_empty`

| Locale  | Translation                   |
| ------- | ----------------------------- |
| `de`    | Typ darf nicht leer sein      |
| `en`    | Type cannot be empty          |
| `es`    | El tipo no puede estar vacío  |
| `fr`    | Le type ne peut pas être vide |
| `it`    | Il tipo non può essere vuoto  |
| `ja`    | タイプを空にすることはできません              |
| `ko`    | 타입은 비어있을 수 없습니다               |
| `nl`    | Type kan niet leeg zijn       |
| `pt_BR` | Tipo não pode estar vazio     |
| `ru`    | Тип не может быть пустым      |
| `sv`    | Typ kan inte vara tom         |
| `tr`    | Tür boş olamaz                |
| `uk`    | Тип не може бути порожнім     |
| `zh_CN` | 类型不能为空                        |

### `request.user_empty`

| Locale  | Translation                              |
| ------- | ---------------------------------------- |
| `de`    | Benutzer darf nicht leer sein            |
| `en`    | User cannot be empty                     |
| `es`    | El usuario no puede estar vacío          |
| `fr`    | L&#039;utilisateur ne peut pas être vide |
| `it`    | L&#039;&#039;utente non può essere vuoto |
| `ja`    | ユーザーを空にすることはできません                        |
| `ko`    | 사용자는 비어있을 수 없습니다                         |
| `nl`    | Gebruiker kan niet leeg zijn             |
| `pt_BR` | Usuário não pode estar vazio             |
| `ru`    | Пользователь не может быть пустым        |
| `sv`    | Användare kan inte vara tom              |
| `tr`    | Kullanıcı boş olamaz                     |
| `uk`    | Користувач не може бути порожнім         |
| `zh_CN` | 用户不能为空                                   |

### `response.unexpected_type`

| Locale  | Translation                           |
| ------- | ------------------------------------- |
| `de`    | Unerwarteter Antworttyp erhalten      |
| `en`    | Unexpected response type received     |
| `es`    | Tipo de respuesta inesperado recibido |
| `fr`    | Type de réponse inattendu reçu        |
| `it`    | Tipo di risposta inaspettato ricevuto |
| `ja`    | 予期しないレスポンスタイプを受信しました                  |
| `ko`    | 예상치 못한 응답 타입을 받았습니다                   |
| `nl`    | Onverwacht responstype ontvangen      |
| `pt_BR` | Tipo de resposta inesperado recebido  |
| `ru`    | Получен неожиданный тип ответа        |
| `sv`    | Oväntad svarstyp mottagen             |
| `tr`    | Beklenmeyen yanıt türü alındı         |
| `uk`    | Отримано неочікуваний тип відповіді   |
| `zh_CN` | 收到意外的响应类型                             |

### `result.failure_no_value`

| Locale  | Translation                         |
| ------- | ----------------------------------- |
| `de`    | Fehlschlag hat keinen Wert          |
| `en`    | Failure has no value                |
| `es`    | El resultado fallido no tiene valor |
| `fr`    | L&#039;échec n&#039;a pas de valeur |
| `it`    | Il fallimento non ha valore         |
| `ja`    | 失敗には値がありません                         |
| `ko`    | 실패에는 값이 없습니다                        |
| `nl`    | Falen heeft geen waarde             |
| `pt_BR` | Falha não tem valor                 |
| `ru`    | Неудача не имеет значения           |
| `sv`    | Misslyckande har inget värde        |
| `tr`    | Başarısızlığın değeri yok           |
| `uk`    | Невдача не має значення             |
| `zh_CN` | 失败没有值                               |

### `result.success_no_error`

| Locale  | Translation                          |
| ------- | ------------------------------------ |
| `de`    | Erfolg hat keinen Fehler             |
| `en`    | Success has no error                 |
| `es`    | El resultado exitoso no tiene error  |
| `fr`    | Le succès n&#039;a pas d&#039;erreur |
| `it`    | Il successo non ha errori            |
| `ja`    | 成功にはエラーがありません                        |
| `ko`    | 성공에는 오류가 없습니다                        |
| `nl`    | Succes heeft geen fout               |
| `pt_BR` | Sucesso não tem erro                 |
| `ru`    | Успех не имеет ошибки                |
| `sv`    | Framgång har inget fel               |
| `tr`    | Başarının hatası yok                 |
| `uk`    | Успіх не має помилки                 |
| `zh_CN` | 成功没有错误                               |

### `schema.class_not_found`

| Locale  | Translation                                                                                           |
| ------- | ----------------------------------------------------------------------------------------------------- |
| `de`    | Klasse &#039;%className%&#039; existiert nicht oder kann nicht automatisch geladen werden             |
| `en`    | Class &quot;%className%&quot; does not exist or cannot be autoloaded                                  |
| `es`    | La clase &quot;%className%&quot; no existe o no se puede cargar automáticamente                       |
| `fr`    | La classe &quot;%className%&quot; n&#039;&#039;existe pas ou ne peut pas être chargée automatiquement |
| `it`    | La classe &quot;%className%&quot; non esiste o non può essere auto-caricata                           |
| `ja`    | クラス「%className%」は存在しないか、自動読み込みできません                                                                   |
| `ko`    | 클래스 &quot;%className%&quot;가 존재하지 않거나 자동로드할 수 없습니다                                                    |
| `nl`    | Klasse &quot;%className%&quot; bestaat niet of kan niet automatisch geladen worden                    |
| `pt_BR` | Classe &quot;%className%&quot; não existe ou não pode ser carregada automaticamente                   |
| `ru`    | Класс &quot;%className%&quot; не существует или не может быть автозагружен                            |
| `sv`    | Klass &quot;%className%&quot; existerar inte eller kan inte autoladdas                                |
| `tr`    | Sınıf &quot;%className%&quot; mevcut değil veya otomatik yüklenemiyor                                 |
| `uk`    | Клас &quot;%className%&quot; не існує або не може бути автозавантажений                               |
| `zh_CN` | 类&quot;%className%&quot;不存在或无法自动加载                                                                    |

### `schema.item_type_not_found`

| Locale  | Translation                                                                                                            |
| ------- | ---------------------------------------------------------------------------------------------------------------------- |
| `de`    | Elementtyp &#039;%itemType%&#039; existiert nicht oder kann nicht automatisch geladen werden                           |
| `en`    | Item type &quot;%itemType%&quot; does not exist or cannot be autoloaded                                                |
| `es`    | El tipo de elemento &quot;%itemType%&quot; no existe o no se puede cargar automáticamente                              |
| `fr`    | Le type d&#039;&#039;élément &quot;%itemType%&quot; n&#039;&#039;existe pas ou ne peut pas être chargé automatiquement |
| `it`    | Il tipo di elemento &quot;%itemType%&quot; non esiste o non può essere auto-caricato                                   |
| `ja`    | アイテムタイプ「%itemType%」は存在しないか、自動読み込みできません                                                                                 |
| `ko`    | 항목 타입 &quot;%itemType%&quot;이 존재하지 않거나 자동로드할 수 없습니다                                                                    |
| `nl`    | Itemtype &quot;%itemType%&quot; bestaat niet of kan niet automatisch geladen worden                                    |
| `pt_BR` | Tipo de item &quot;%itemType%&quot; não existe ou não pode ser carregado automaticamente                               |
| `ru`    | Тип элемента &quot;%itemType%&quot; не существует или не может быть автозагружен                                       |
| `sv`    | Objekttyp &quot;%itemType%&quot; existerar inte eller kan inte autoladdas                                              |
| `tr`    | Öğe türü &quot;%itemType%&quot; mevcut değil veya otomatik yüklenemiyor                                                |
| `uk`    | Тип елемента &quot;%itemType%&quot; не існує або не може бути автозавантажений                                         |
| `zh_CN` | 项目类型&quot;%itemType%&quot;不存在或无法自动加载                                                                                   |

### `exception.serialization.could_not_add_items_to_collection`

| Locale  | Translation                                                        |
| ------- | ------------------------------------------------------------------ |
| `de`    | Elemente konnten nicht zu Sammlung %className% hinzugefügt werden  |
| `en`    | Could not add items to collection %className%                      |
| `es`    | No se pudieron agregar elementos a la colección %className%        |
| `fr`    | Impossible d&#039;ajouter des éléments à la collection %className% |
| `it`    | Impossibile aggiungere elementi alla collezione %className%        |
| `ja`    | コレクション%className%にアイテムを追加できませんでした                                  |
| `ko`    | 컬렉션 %className%에 항목을 추가할 수 없습니다                                    |
| `nl`    | Kon geen items toevoegen aan verzameling %className%               |
| `pt_BR` | Não foi possível adicionar itens à coleção %className%             |
| `ru`    | Не удалось добавить элементы в коллекцию %className%               |
| `sv`    | Kunde inte lägga till objekt i samling %className%                 |
| `tr`    | %className% koleksiyonuna öğeler eklenemedi                        |
| `uk`    | Не вдалося додати елементи до колекції %className%                 |
| `zh_CN` | 无法向集合%className%添加项目                                               |

### `exception.serialization.empty_collection`

| Locale  | Translation                         |
| ------- | ----------------------------------- |
| `de`    | Sammlung darf nicht leer sein       |
| `en`    | Collection cannot be empty          |
| `es`    | La colección no puede estar vacía   |
| `fr`    | La collection ne peut pas être vide |
| `it`    | La collezione non può essere vuota  |
| `ja`    | コレクションを空にすることはできません                 |
| `ko`    | 컬렉션은 비어있을 수 없습니다                    |
| `nl`    | Verzameling kan niet leeg zijn      |
| `pt_BR` | Coleção não pode estar vazia        |
| `ru`    | Коллекция не может быть пустой      |
| `sv`    | Samling kan inte vara tom           |
| `tr`    | Koleksiyon boş olamaz               |
| `uk`    | Колекція не може бути порожньою     |
| `zh_CN` | 集合不能为空                              |

### `exception.serialization.invalid_item_type`

| Locale  | Translation                                                                                               |
| ------- | --------------------------------------------------------------------------------------------------------- |
| `de`    | Ungültiger Elementtyp für %property% in %className%: erwartet %expected%, erhalten %actual_type%          |
| `en`    | Invalid item type for %property% in %className%: expected %expected%, got %actual_type%                   |
| `es`    | Tipo de elemento inválido para %property% en %className%: se esperaba %expected%, se obtuvo %actual_type% |
| `fr`    | Type d&#039;élément invalide pour %property% dans %className% : %expected% attendu, %actual_type% obtenu  |
| `it`    | Tipo di elemento non valido per %property% in %className%: atteso %expected%, ottenuto %actual_type%      |
| `ja`    | %className%の%property%に無効なアイテムタイプです: %expected%が期待されますが%actual_type%が取得されました                              |
| `ko`    | %className%의 %property%에 대한 잘못된 항목 타입: %expected%이 예상되지만 %actual_type%을 받았습니다                             |
| `nl`    | Ongeldig itemtype voor %property% in %className%: verwacht %expected%, kreeg %actual_type%                |
| `pt_BR` | Tipo de item inválido para %property% em %className%: esperado %expected%, obtido %actual_type%           |
| `ru`    | Недопустимый тип элемента для %property% в %className%: ожидается %expected%, получено %actual_type%      |
| `sv`    | Ogiltig objekttyp för %property% i %className%: förväntad %expected%, fick %actual_type%                  |
| `tr`    | %className% içindeki %property% için geçersiz öğe türü: %expected% bekleniyor, %actual_type% alındı       |
| `uk`    | Недійсний тип елемента для %property% в %className%: очікується %expected%, отримано %actual_type%        |
| `zh_CN` | %className%中%property%的项目类型无效：期望%expected%，得到%actual_type%                                                |

### `exception.serialization.missing_required_constructor_parameter`

| Locale  | Translation                                                                                       |
| ------- | ------------------------------------------------------------------------------------------------- |
| `de`    | Erforderlicher Konstruktorparameter &#039;%paramName%&#039; für Klasse %className% fehlt          |
| `en`    | Missing required constructor parameter &quot;%paramName%&quot; for class %className%              |
| `es`    | Falta el parámetro requerido del constructor &quot;%paramName%&quot; para la clase %className%    |
| `fr`    | Paramètre de constructeur requis manquant &quot;%paramName%&quot; pour la classe %className%      |
| `it`    | Parametro del costruttore obbligatorio &quot;%paramName%&quot; mancante per la classe %className% |
| `ja`    | クラス%className%の必須コンストラクターパラメーター「%paramName%」がありません                                                |
| `ko`    | 클래스 %className%의 필수 생성자 매개변수 &quot;%paramName%&quot;이 누락되었습니다                                     |
| `nl`    | Ontbrekende vereiste constructor parameter &quot;%paramName%&quot; voor klasse %className%        |
| `pt_BR` | Parâmetro obrigatório do construtor &quot;%paramName%&quot; faltando para classe %className%      |
| `ru`    | Отсутствует обязательный параметр конструктора &quot;%paramName%&quot; для класса %className%     |
| `sv`    | Saknas obligatorisk konstruktorparameter &quot;%paramName%&quot; för klass %className%            |
| `tr`    | %className% sınıfı için gerekli yapıcı parametresi &quot;%paramName%&quot; eksik                  |
| `uk`    | Відсутній обов&#039;язковий параметр конструктора &#039;%paramName%&#039; для класу %className%   |
| `zh_CN` | 类%className%缺少必需的构造函数参数&quot;%paramName%&quot;                                                    |

### `exception.serialization.response`

| Locale  | Translation                                                       |
| ------- | ----------------------------------------------------------------- |
| `de`    | Serialisierung/Deserialisierung der Antwortdaten fehlgeschlagen   |
| `en`    | Failed to serialize/deserialize response data                     |
| `es`    | No se pudieron serializar/deserializar los datos de respuesta     |
| `fr`    | Échec de la sérialisation/désérialisation des données de réponse  |
| `it`    | Fallita la serializzazione/deserializzazione dei dati di risposta |
| `ja`    | レスポンスデータのシリアライズ/デシリアライズに失敗しました                                    |
| `ko`    | 응답 데이터 직렬화/역직렬화에 실패했습니다                                           |
| `nl`    | Mislukt om responsdata te serialiseren/deserialiseren             |
| `pt_BR` | Falha ao serializar/deserializar dados de resposta                |
| `ru`    | Не удалось сериализовать/десериализовать данные ответа            |
| `sv`    | Misslyckades med att serialisera/deserialisera svarsdata          |
| `tr`    | Yanıt verilerini serileştirme/deserileştirme başarısız            |
| `uk`    | Не вдалося серіалізувати/десеріалізувати дані відповіді           |
| `zh_CN` | 序列化/反序列化响应数据失败                                                    |

### `exception.serialization.undefined_item_type`

| Locale  | Translation                                                   |
| ------- | ------------------------------------------------------------- |
| `de`    | Elementtyp ist für %className% nicht definiert                |
| `en`    | Item type is not defined for %className%                      |
| `es`    | El tipo de elemento no está definido para %className%         |
| `fr`    | Le type d&#039;élément n&#039;est pas défini pour %className% |
| `it`    | Tipo di elemento non definito per %className%                 |
| `ja`    | %className%のアイテムタイプが定義されていません                                 |
| `ko`    | %className%의 항목 타입이 정의되지 않았습니다                                |
| `nl`    | Itemtype is niet gedefinieerd voor %className%                |
| `pt_BR` | Tipo de item não está definido para %className%               |
| `ru`    | Тип элемента не определен для %className%                     |
| `sv`    | Objekttyp är inte definierad för %className%                  |
| `tr`    | %className% için öğe türü tanımlanmamış                       |
| `uk`    | Тип елемента не визначений для %className%                    |
| `zh_CN` | %className%的项目类型未定义                                           |

### `service.http_not_available`

| Locale  | Translation                   |
| ------- | ----------------------------- |
| `de`    | HTTP-Service nicht verfügbar  |
| `en`    | HTTP service not available    |
| `es`    | Servicio HTTP no disponible   |
| `fr`    | Service HTTP non disponible   |
| `it`    | Servizio HTTP non disponibile |
| `ja`    | HTTPサービスが利用できません              |
| `ko`    | HTTP 서비스를 사용할 수 없습니다          |
| `nl`    | HTTP-service niet beschikbaar |
| `pt_BR` | Serviço HTTP não disponível   |
| `ru`    | HTTP сервис недоступен        |
| `sv`    | HTTP-tjänst inte tillgänglig  |
| `tr`    | HTTP hizmeti kullanılamıyor   |
| `uk`    | HTTP сервіс недоступний       |
| `zh_CN` | HTTP服务不可用                     |

### `service.schema_validator_not_available`

| Locale  | Translation                         |
| ------- | ----------------------------------- |
| `de`    | Schema-Validator nicht verfügbar    |
| `en`    | Schema validator not available      |
| `es`    | Validador de esquema no disponible  |
| `fr`    | Validateur de schéma non disponible |
| `it`    | Validatore schema non disponibile   |
| `ja`    | スキーマバリデーターが利用できません                  |
| `ko`    | 스키마 검증기를 사용할 수 없습니다                 |
| `nl`    | Schema-validator niet beschikbaar   |
| `pt_BR` | Validador de schema não disponível  |
| `ru`    | Валидатор схемы недоступен          |
| `sv`    | Schemavalidator inte tillgänglig    |
| `tr`    | Şema doğrulayıcı kullanılamıyor     |
| `uk`    | Валідатор схеми недоступний         |
| `zh_CN` | 模式验证器不可用                            |

### `service.store_repository_not_available`

| Locale  | Translation                          |
| ------- | ------------------------------------ |
| `de`    | Store-Repository nicht verfügbar     |
| `en`    | Store repository not available       |
| `es`    | Repositorio de almacén no disponible |
| `fr`    | Dépôt de magasin non disponible      |
| `it`    | Repository store non disponibile     |
| `ja`    | ストアリポジトリが利用できません                     |
| `ko`    | 스토어 저장소를 사용할 수 없습니다                  |
| `nl`    | Store-repository niet beschikbaar    |
| `pt_BR` | Repositório de store não disponível  |
| `ru`    | Репозиторий хранилища недоступен     |
| `sv`    | Butiks-repository inte tillgängligt  |
| `tr`    | Mağaza deposu kullanılamıyor         |
| `uk`    | Репозиторій сховища недоступний      |
| `zh_CN` | 存储仓库不可用                              |

### `service.tuple_filter_not_available`

| Locale  | Translation                                |
| ------- | ------------------------------------------ |
| `de`    | Tupel-Filter-Service nicht verfügbar       |
| `en`    | Tuple filter service not available         |
| `es`    | Servicio de filtro de tuplas no disponible |
| `fr`    | Service de filtre de tuple non disponible  |
| `it`    | Servizio filtro tuple non disponibile      |
| `ja`    | タプルフィルターサービスが利用できません                       |
| `ko`    | 튜플 필터 서비스를 사용할 수 없습니다                      |
| `nl`    | Tuple-filterservice niet beschikbaar       |
| `pt_BR` | Serviço de filtro de tupla não disponível  |
| `ru`    | Служба фильтра кортежей недоступна         |
| `sv`    | Tupel-filtertjänst inte tillgänglig        |
| `tr`    | Tuple filtre hizmeti kullanılamıyor        |
| `uk`    | Служба фільтра кортежів недоступна         |
| `zh_CN` | 元组过滤器服务不可用                                 |

### `service.tuple_repository_not_available`

| Locale  | Translation                         |
| ------- | ----------------------------------- |
| `de`    | Tupel-Repository nicht verfügbar    |
| `en`    | Tuple repository not available      |
| `es`    | Repositorio de tuplas no disponible |
| `fr`    | Dépôt de tuple non disponible       |
| `it`    | Repository tuple non disponibile    |
| `ja`    | タプルリポジトリが利用できません                    |
| `ko`    | 튜플 저장소를 사용할 수 없습니다                  |
| `nl`    | Tuple-repository niet beschikbaar   |
| `pt_BR` | Repositório de tupla não disponível |
| `ru`    | Репозиторий кортежей недоступен     |
| `sv`    | Tupel-repository inte tillgängligt  |
| `tr`    | Tuple deposu kullanılamıyor         |
| `uk`    | Репозиторій кортежів недоступний    |
| `zh_CN` | 元组仓库不可用                             |

### `store.name_required`

| Locale  | Translation                                                |
| ------- | ---------------------------------------------------------- |
| `de`    | Store-Name ist erforderlich und darf nicht leer sein       |
| `en`    | Store name is required and cannot be empty                 |
| `es`    | El nombre del almacén es requerido y no puede estar vacío  |
| `fr`    | Le nom du magasin est requis et ne peut pas être vide      |
| `it`    | Il nome dello store è obbligatorio e non può essere vuoto  |
| `ja`    | ストア名は必須で、空にすることはできません                                      |
| `ko`    | 스토어 이름은 필수이며 비어있을 수 없습니다                                   |
| `nl`    | Storenaam is vereist en kan niet leeg zijn                 |
| `pt_BR` | Nome do store é obrigatório e não pode estar vazio         |
| `ru`    | Имя хранилища обязательно и не может быть пустым           |
| `sv`    | Butiksnamn krävs och kan inte vara tomt                    |
| `tr`    | Mağaza adı gereklidir ve boş olamaz                        |
| `uk`    | Ім&#039;я сховища обов&#039;язкове і не може бути порожнім |
| `zh_CN` | 存储名称是必需的，不能为空                                              |

### `store.name_too_long`

| Locale  | Translation                                                                          |
| ------- | ------------------------------------------------------------------------------------ |
| `de`    | Store-Name überschreitet maximale Länge von %d Zeichen (bereitgestellt: %d)          |
| `en`    | Store name exceeds maximum length of %d characters (provided: %d)                    |
| `es`    | El nombre del almacén excede la longitud máxima de %d caracteres (proporcionado: %d) |
| `fr`    | Le nom du magasin dépasse la longueur maximale de %d caractères (fourni : %d)        |
| `it`    | Il nome dello store supera la lunghezza massima di %d caratteri (forniti: %d)        |
| `ja`    | ストア名が最大長%d文字を超えています（提供された文字数: %d）                                                    |
| `ko`    | 스토어 이름이 최대 길이 %d자를 초과했습니다 (제공됨: %d)                                                  |
| `nl`    | Storenaam overschrijdt maximale lengte van %d karakters (verstrekt: %d)              |
| `pt_BR` | Nome do store excede comprimento máximo de %d caracteres (fornecido: %d)             |
| `ru`    | Имя хранилища превышает максимальную длину в %d символов (предоставлено: %d)         |
| `sv`    | Butiksnamn överstiger maximal längd på %d tecken (tillhandahållet: %d)               |
| `tr`    | Mağaza adı %d karakter maksimum uzunluğunu aşıyor (sağlanan: %d)                     |
| `uk`    | Ім&#039;я сховища перевищує максимальну довжину %d символів (надано: %d)             |
| `zh_CN` | 存储名称超过最大长度%d个字符（提供：%d）                                                               |

### `store.not_found`

| Locale  | Translation                           |
| ------- | ------------------------------------- |
| `de`    | Store %s wurde nicht gefunden         |
| `en`    | Store %s was not found                |
| `es`    | No se encontró el almacén %s          |
| `fr`    | Le magasin %s n&#039;a pas été trouvé |
| `it`    | Store %s non trovato                  |
| `ja`    | ストア%sが見つかりませんでした                      |
| `ko`    | 스토어 %s를 찾을 수 없습니다                     |
| `nl`    | Store %s niet gevonden                |
| `pt_BR` | Store %s não foi encontrado           |
| `ru`    | Хранилище %s не найдено               |
| `sv`    | Butik %s hittades inte                |
| `tr`    | Mağaza %s bulunamadı                  |
| `uk`    | Сховище %s не знайдено                |
| `zh_CN` | 未找到存储%s                               |

### `translation.file_not_found`

| Locale  | Translation                                     |
| ------- | ----------------------------------------------- |
| `de`    | Übersetzungsdatei nicht gefunden: %resource%    |
| `en`    | Translation file not found: %resource%          |
| `es`    | Archivo de traducción no encontrado: %resource% |
| `fr`    | Fichier de traduction non trouvé : %resource%   |
| `it`    | File di traduzione non trovato: %resource%      |
| `ja`    | 翻訳ファイルが見つかりません: %resource%                      |
| `ko`    | 번역 파일을 찾을 수 없습니다: %resource%                    |
| `nl`    | Vertaalbestand niet gevonden: %resource%        |
| `pt_BR` | Arquivo de tradução não encontrado: %resource%  |
| `ru`    | Файл перевода не найден: %resource%             |
| `sv`    | Översättningsfil hittades inte: %resource%      |
| `tr`    | Çeviri dosyası bulunamadı: %resource%           |
| `uk`    | Файл перекладу не знайдено: %resource%          |
| `zh_CN` | 未找到翻译文件：%resource%                              |

### `translation.unsupported_format`

| Locale  | Translation                                              |
| ------- | -------------------------------------------------------- |
| `de`    | Nicht unterstütztes Übersetzungsdateiformat: %format%    |
| `en`    | Unsupported translation file format: %format%            |
| `es`    | Formato de archivo de traducción no compatible: %format% |
| `fr`    | Format de fichier de traduction non supporté : %format%  |
| `it`    | Formato del file di traduzione non supportato: %format%  |
| `ja`    | サポートされていない翻訳ファイル形式です: %format%                           |
| `ko`    | 지원되지 않는 번역 파일 형식: %format%                               |
| `nl`    | Niet ondersteund vertaalbestandformaat: %format%         |
| `pt_BR` | Formato de arquivo de tradução não suportado: %format%   |
| `ru`    | Неподдерживаемый формат файла перевода: %format%         |
| `sv`    | Ostödd översättningsfilformat: %format%                  |
| `tr`    | Desteklenmeyen çeviri dosya formatı: %format%            |
| `uk`    | Непідтримуваний формат файлу перекладу: %format%         |
| `zh_CN` | 不支持的翻译文件格式：%format%                                      |

### `tuple_operation.delete.description`

| Locale  | Translation                                                                                     |
| ------- | ----------------------------------------------------------------------------------------------- |
| `de`    | Entfernt ein vorhandenes Beziehungstupel, widerruft Berechtigungen oder entfernt Beziehungen    |
| `en`    | Removes an existing relationship tuple, revoking permissions or removing relationships          |
| `es`    | Elimina una tupla de relación existente, revocando permisos o eliminando relaciones             |
| `fr`    | Supprime un tuple de relation existant, révoquant des permissions ou supprimant des relations   |
| `it`    | Rimuove una tupla di relazione esistente, revocando permessi o rimuovendo relazioni             |
| `ja`    | 既存の関係タプルを削除し、権限を取り消すか関係を削除します                                                                   |
| `ko`    | 기존 관계 튜플을 제거하여 권한을 취소하거나 관계를 삭제합니다                                                              |
| `nl`    | Verwijdert een bestaande relatietuple, trekt machtigingen in of verwijdert relaties             |
| `pt_BR` | Remove uma tupla de relacionamento existente, revogando permissões ou removendo relacionamentos |
| `ru`    | Удаляет существующий кортеж отношений, отзывая разрешения или удаляя отношения                  |
| `sv`    | Tar bort en befintlig relationstupel, återkallar behörigheter eller tar bort relationer         |
| `tr`    | Mevcut bir ilişki tuple kaldırır, izinleri iptal eder veya ilişkileri siler                     |
| `uk`    | Видаляє існуючий кортеж відношень, відкликаючи дозволи або видаляючи відношення                 |
| `zh_CN` | 删除现有的关系元组，撤销权限或移除关系                                                                             |

### `tuple_operation.write.description`

| Locale  | Translation                                                                                       |
| ------- | ------------------------------------------------------------------------------------------------- |
| `de`    | Fügt ein neues Beziehungstupel hinzu, gewährt Berechtigungen oder stellt Beziehungen her          |
| `en`    | Adds a new relationship tuple, granting permissions or establishing relationships                 |
| `es`    | Agrega una nueva tupla de relación, otorgando permisos o estableciendo relaciones                 |
| `fr`    | Ajoute un nouveau tuple de relation, accordant des permissions ou établissant des relations       |
| `it`    | Aggiunge una nuova tupla di relazione, concedendo permessi o stabilendo relazioni                 |
| `ja`    | 新しい関係タプルを追加し、権限を付与するか関係を確立します                                                                     |
| `ko`    | 새로운 관계 튜플을 추가하여 권한을 부여하거나 관계를 설정합니다                                                               |
| `nl`    | Voegt een nieuwe relatietuple toe, verleent machtigingen of vestigt relaties                      |
| `pt_BR` | Adiciona uma nova tupla de relacionamento, concedendo permissões ou estabelecendo relacionamentos |
| `ru`    | Добавляет новый кортеж отношений, предоставляя разрешения или устанавливая отношения              |
| `sv`    | Lägger till en ny relationstupel, beviljar behörigheter eller etablerar relationer                |
| `tr`    | Yeni bir ilişki tuple ekler, izinler verir veya ilişkiler kurar                                   |
| `uk`    | Додає новий кортеж відношень, надаючи дозволи або встановлюючи відношення                         |
| `zh_CN` | 添加新的关系元组，授予权限或建立关系                                                                                |

### `yaml.cannot_read_file`

| Locale  | Translation                                 |
| ------- | ------------------------------------------- |
| `de`    | Datei kann nicht gelesen werden: %filename% |
| `en`    | Cannot read file: %filename%                |
| `es`    | No se puede leer el archivo: %filename%     |
| `fr`    | Impossible de lire le fichier : %filename%  |
| `it`    | Impossibile leggere il file: %filename%     |
| `ja`    | ファイルを読み取れません: %filename%                    |
| `ko`    | 파일을 읽을 수 없습니다: %filename%                   |
| `nl`    | Kan bestand niet lezen: %filename%          |
| `pt_BR` | Não é possível ler arquivo: %filename%      |
| `ru`    | Невозможно прочитать файл: %filename%       |
| `sv`    | Kan inte läsa fil: %filename%               |
| `tr`    | Dosya okunamıyor: %filename%                |
| `uk`    | Неможливо прочитати файл: %filename%        |
| `zh_CN` | 无法读取文件：%filename%                           |

### `yaml.file_does_not_exist`

| Locale  | Translation                               |
| ------- | ----------------------------------------- |
| `de`    | Datei existiert nicht: %filename%         |
| `en`    | File does not exist: %filename%           |
| `es`    | El archivo no existe: %filename%          |
| `fr`    | Le fichier n&#039;existe pas : %filename% |
| `it`    | Il file non esiste: %filename%            |
| `ja`    | ファイルが存在しません: %filename%                   |
| `ko`    | 파일이 존재하지 않습니다: %filename%                 |
| `nl`    | Bestand bestaat niet: %filename%          |
| `pt_BR` | Arquivo não existe: %filename%            |
| `ru`    | Файл не существует: %filename%            |
| `sv`    | Filen existerar inte: %filename%          |
| `tr`    | Dosya mevcut değil: %filename%            |
| `uk`    | Файл не існує: %filename%                 |
| `zh_CN` | 文件不存在：%filename%                          |

### `yaml.invalid_structure`

| Locale  | Translation                                         |
| ------- | --------------------------------------------------- |
| `de`    | Ungültige YAML-Struktur in Zeile %line_number%      |
| `en`    | Invalid YAML structure on line %line_number%        |
| `es`    | Estructura YAML inválida en la línea %line_number%  |
| `fr`    | Structure YAML invalide à la ligne %line_number%    |
| `it`    | Struttura YAML non valida alla riga %line_number%   |
| `ja`    | 行%line_number%のYAML構造が無効です                          |
| `ko`    | %line_number%번째 줄의 YAML 구조가 잘못되었습니다                 |
| `nl`    | Ongeldige YAML-structuur op regel %line_number%     |
| `pt_BR` | Estrutura YAML inválida na linha %line_number%      |
| `ru`    | Недопустимая структура YAML на строке %line_number% |
| `sv`    | Ogiltig YAML-struktur på rad %line_number%          |
| `tr`    | %line_number% satırında geçersiz YAML yapısı        |
| `uk`    | Недійсна структура YAML на рядку %line_number%      |
| `zh_CN` | 第%line_number%行YAML结构无效                             |

### `yaml.invalid_syntax_empty_key`

| Locale  | Translation                                                      |
| ------- | ---------------------------------------------------------------- |
| `de`    | Ungültige YAML-Syntax in Zeile %line_number%: leerer Schlüssel   |
| `en`    | Invalid YAML syntax on line %line_number%: empty key             |
| `es`    | Sintaxis YAML inválida en la línea %line_number%: clave vacía    |
| `fr`    | Syntaxe YAML invalide à la ligne %line_number% : clé vide        |
| `it`    | Sintassi YAML non valida alla riga %line_number%: chiave vuota   |
| `ja`    | 行%line_number%のYAML構文が無効です: キーが空です                               |
| `ko`    | %line_number%번째 줄의 YAML 구문이 잘못되었습니다: 빈 키                         |
| `nl`    | Ongeldige YAML-syntaxis op regel %line_number%: lege sleutel     |
| `pt_BR` | Sintaxe YAML inválida na linha %line_number%: chave vazia        |
| `ru`    | Недопустимый синтаксис YAML на строке %line_number%: пустой ключ |
| `sv`    | Ogiltig YAML-syntax på rad %line_number%: tom nyckel             |
| `tr`    | %line_number% satırında geçersiz YAML sözdizimi: boş anahtar     |
| `uk`    | Недійсний синтаксис YAML на рядку %line_number%: порожній ключ   |
| `zh_CN` | 第%line_number%行YAML语法无效：空键                                       |

### `yaml.invalid_syntax_missing_colon`

| Locale  | Translation                                                                |
| ------- | -------------------------------------------------------------------------- |
| `de`    | Ungültige YAML-Syntax in Zeile %line_number%: fehlender Doppelpunkt        |
| `en`    | Invalid YAML syntax on line %line_number%: missing colon                   |
| `es`    | Sintaxis YAML inválida en la línea %line_number%: falta dos puntos         |
| `fr`    | Syntaxe YAML invalide à la ligne %line_number% : deux-points manquants     |
| `it`    | Sintassi YAML non valida alla riga %line_number%: due punti mancanti       |
| `ja`    | 行%line_number%のYAML構文が無効です: コロンがありません                                      |
| `ko`    | %line_number%번째 줄의 YAML 구문이 잘못되었습니다: 콜론 누락                                 |
| `nl`    | Ongeldige YAML-syntaxis op regel %line_number%: ontbrekende dubbele punt   |
| `pt_BR` | Sintaxe YAML inválida na linha %line_number%: dois pontos faltando         |
| `ru`    | Недопустимый синтаксис YAML на строке %line_number%: отсутствует двоеточие |
| `sv`    | Ogiltig YAML-syntax på rad %line_number%: saknar kolon                     |
| `tr`    | %line_number% satırında geçersiz YAML sözdizimi: iki nokta eksik           |
| `uk`    | Недійсний синтаксис YAML на рядку %line_number%: відсутня двокрапка        |
| `zh_CN` | 第%line_number%行YAML语法无效：缺少冒号                                               |

### `yaml.invalid_syntax_missing_value`

| Locale  | Translation                                                               |
| ------- | ------------------------------------------------------------------------- |
| `de`    | Ungültige YAML-Syntax in Zeile %line_number%: fehlender Wert              |
| `en`    | Invalid YAML syntax on line %line_number%: missing value                  |
| `es`    | Sintaxis YAML inválida en la línea %line_number%: falta valor             |
| `fr`    | Syntaxe YAML invalide à la ligne %line_number% : valeur manquante         |
| `it`    | Sintassi YAML non valida alla riga %line_number%: valore mancante         |
| `ja`    | 行%line_number%のYAML構文が無効です: 値がありません                                       |
| `ko`    | %line_number%번째 줄의 YAML 구문이 잘못되었습니다: 값 누락                                 |
| `nl`    | Ongeldige YAML-syntaxis op regel %line_number%: ontbrekende waarde        |
| `pt_BR` | Sintaxe YAML inválida na linha %line_number%: valor faltando              |
| `ru`    | Недопустимый синтаксис YAML на строке %line_number%: отсутствует значение |
| `sv`    | Ogiltig YAML-syntax på rad %line_number%: saknar värde                    |
| `tr`    | %line_number% satırında geçersiz YAML sözdizimi: değer eksik              |
| `uk`    | Недійсний синтаксис YAML на рядку %line_number%: відсутнє значення        |
| `zh_CN` | 第%line_number%行YAML语法无效：缺少值                                               |

## Methods

#### key

```php
public function key(): string

```

Get the translation key for this message.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Messages.php#L369)

#### Returns

`string`
