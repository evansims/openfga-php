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
| Name | Value | Description |
|------|-------|-------------|
| `AUTH_ACCESS_TOKEN_MUST_BE_STRING` | `\OpenFGA\Messages::AUTH_ACCESS_TOKEN_MUST_BE_STRING` |  |
| `AUTH_ERROR_TOKEN_EXPIRED` | `\OpenFGA\Messages::AUTH_ERROR_TOKEN_EXPIRED` |  |
| `AUTH_ERROR_TOKEN_INVALID` | `\OpenFGA\Messages::AUTH_ERROR_TOKEN_INVALID` |  |
| `AUTH_EXPIRES_IN_MUST_BE_INTEGER` | `\OpenFGA\Messages::AUTH_EXPIRES_IN_MUST_BE_INTEGER` |  |
| `AUTH_INVALID_RESPONSE_FORMAT` | `\OpenFGA\Messages::AUTH_INVALID_RESPONSE_FORMAT` |  |
| `AUTH_MISSING_REQUIRED_FIELDS` | `\OpenFGA\Messages::AUTH_MISSING_REQUIRED_FIELDS` |  |
| `AUTH_USER_MESSAGE_TOKEN_EXPIRED` | `\OpenFGA\Messages::AUTH_USER_MESSAGE_TOKEN_EXPIRED` |  |
| `AUTH_USER_MESSAGE_TOKEN_INVALID` | `\OpenFGA\Messages::AUTH_USER_MESSAGE_TOKEN_INVALID` |  |
| `CLIENT_ERROR_AUTHENTICATION` | `\OpenFGA\Messages::CLIENT_ERROR_AUTHENTICATION` |  |
| `CLIENT_ERROR_CONFIGURATION` | `\OpenFGA\Messages::CLIENT_ERROR_CONFIGURATION` |  |
| `CLIENT_ERROR_NETWORK` | `\OpenFGA\Messages::CLIENT_ERROR_NETWORK` |  |
| `CLIENT_ERROR_SERIALIZATION` | `\OpenFGA\Messages::CLIENT_ERROR_SERIALIZATION` |  |
| `CLIENT_ERROR_VALIDATION` | `\OpenFGA\Messages::CLIENT_ERROR_VALIDATION` |  |
| `COLLECTION_INVALID_ITEM_INSTANCE` | `\OpenFGA\Messages::COLLECTION_INVALID_ITEM_INSTANCE` |  |
| `COLLECTION_INVALID_ITEM_TYPE_INTERFACE` | `\OpenFGA\Messages::COLLECTION_INVALID_ITEM_TYPE_INTERFACE` |  |
| `COLLECTION_INVALID_KEY_TYPE` | `\OpenFGA\Messages::COLLECTION_INVALID_KEY_TYPE` |  |
| `COLLECTION_INVALID_POSITION` | `\OpenFGA\Messages::COLLECTION_INVALID_POSITION` |  |
| `COLLECTION_INVALID_VALUE_TYPE` | `\OpenFGA\Messages::COLLECTION_INVALID_VALUE_TYPE` |  |
| `COLLECTION_KEY_MUST_BE_STRING` | `\OpenFGA\Messages::COLLECTION_KEY_MUST_BE_STRING` |  |
| `COLLECTION_UNDEFINED_ITEM_TYPE` | `\OpenFGA\Messages::COLLECTION_UNDEFINED_ITEM_TYPE` |  |
| `CONFIG_ERROR_HTTP_CLIENT_MISSING` | `\OpenFGA\Messages::CONFIG_ERROR_HTTP_CLIENT_MISSING` |  |
| `CONFIG_ERROR_HTTP_REQUEST_FACTORY_MISSING` | `\OpenFGA\Messages::CONFIG_ERROR_HTTP_REQUEST_FACTORY_MISSING` |  |
| `CONFIG_ERROR_HTTP_RESPONSE_FACTORY_MISSING` | `\OpenFGA\Messages::CONFIG_ERROR_HTTP_RESPONSE_FACTORY_MISSING` |  |
| `CONFIG_ERROR_HTTP_STREAM_FACTORY_MISSING` | `\OpenFGA\Messages::CONFIG_ERROR_HTTP_STREAM_FACTORY_MISSING` |  |
| `CONSISTENCY_HIGHER_CONSISTENCY_DESCRIPTION` | `\OpenFGA\Messages::CONSISTENCY_HIGHER_CONSISTENCY_DESCRIPTION` |  |
| `CONSISTENCY_MINIMIZE_LATENCY_DESCRIPTION` | `\OpenFGA\Messages::CONSISTENCY_MINIMIZE_LATENCY_DESCRIPTION` |  |
| `CONSISTENCY_UNSPECIFIED_DESCRIPTION` | `\OpenFGA\Messages::CONSISTENCY_UNSPECIFIED_DESCRIPTION` |  |
| `DSL_INPUT_EMPTY` | `\OpenFGA\Messages::DSL_INPUT_EMPTY` |  |
| `DSL_INVALID_COMPUTED_USERSET` | `\OpenFGA\Messages::DSL_INVALID_COMPUTED_USERSET` |  |
| `DSL_PARSE_FAILED` | `\OpenFGA\Messages::DSL_PARSE_FAILED` |  |
| `DSL_PATTERN_EMPTY` | `\OpenFGA\Messages::DSL_PATTERN_EMPTY` |  |
| `DSL_UNBALANCED_PARENTHESES_CLOSING` | `\OpenFGA\Messages::DSL_UNBALANCED_PARENTHESES_CLOSING` |  |
| `DSL_UNBALANCED_PARENTHESES_OPENING` | `\OpenFGA\Messages::DSL_UNBALANCED_PARENTHESES_OPENING` |  |
| `DSL_UNRECOGNIZED_TERM` | `\OpenFGA\Messages::DSL_UNRECOGNIZED_TERM` |  |
| `INVALID_BATCH_CHECK_EMPTY` | `\OpenFGA\Messages::INVALID_BATCH_CHECK_EMPTY` |  |
| `INVALID_CORRELATION_ID` | `\OpenFGA\Messages::INVALID_CORRELATION_ID` |  |
| `JWT_INVALID_AUDIENCE` | `\OpenFGA\Messages::JWT_INVALID_AUDIENCE` |  |
| `JWT_INVALID_FORMAT` | `\OpenFGA\Messages::JWT_INVALID_FORMAT` |  |
| `JWT_INVALID_HEADER` | `\OpenFGA\Messages::JWT_INVALID_HEADER` |  |
| `JWT_INVALID_ISSUER` | `\OpenFGA\Messages::JWT_INVALID_ISSUER` |  |
| `JWT_INVALID_PAYLOAD` | `\OpenFGA\Messages::JWT_INVALID_PAYLOAD` |  |
| `JWT_MISSING_REQUIRED_CLAIMS` | `\OpenFGA\Messages::JWT_MISSING_REQUIRED_CLAIMS` |  |
| `JWT_TOKEN_EXPIRED` | `\OpenFGA\Messages::JWT_TOKEN_EXPIRED` |  |
| `JWT_TOKEN_NOT_YET_VALID` | `\OpenFGA\Messages::JWT_TOKEN_NOT_YET_VALID` |  |
| `MODEL_INVALID_TUPLE_KEY` | `\OpenFGA\Messages::MODEL_INVALID_TUPLE_KEY` |  |
| `MODEL_LEAF_MISSING_CONTENT` | `\OpenFGA\Messages::MODEL_LEAF_MISSING_CONTENT` |  |
| `MODEL_SOURCE_INFO_FILE_EMPTY` | `\OpenFGA\Messages::MODEL_SOURCE_INFO_FILE_EMPTY` |  |
| `MODEL_TYPED_WILDCARD_TYPE_EMPTY` | `\OpenFGA\Messages::MODEL_TYPED_WILDCARD_TYPE_EMPTY` |  |
| `NETWORK_ERROR` | `\OpenFGA\Messages::NETWORK_ERROR` |  |
| `NETWORK_ERROR_CONFLICT` | `\OpenFGA\Messages::NETWORK_ERROR_CONFLICT` |  |
| `NETWORK_ERROR_FORBIDDEN` | `\OpenFGA\Messages::NETWORK_ERROR_FORBIDDEN` |  |
| `NETWORK_ERROR_INVALID` | `\OpenFGA\Messages::NETWORK_ERROR_INVALID` |  |
| `NETWORK_ERROR_REQUEST` | `\OpenFGA\Messages::NETWORK_ERROR_REQUEST` |  |
| `NETWORK_ERROR_SERVER` | `\OpenFGA\Messages::NETWORK_ERROR_SERVER` |  |
| `NETWORK_ERROR_TIMEOUT` | `\OpenFGA\Messages::NETWORK_ERROR_TIMEOUT` |  |
| `NETWORK_ERROR_UNAUTHENTICATED` | `\OpenFGA\Messages::NETWORK_ERROR_UNAUTHENTICATED` |  |
| `NETWORK_ERROR_UNDEFINED_ENDPOINT` | `\OpenFGA\Messages::NETWORK_ERROR_UNDEFINED_ENDPOINT` |  |
| `NETWORK_ERROR_UNEXPECTED` | `\OpenFGA\Messages::NETWORK_ERROR_UNEXPECTED` |  |
| `NETWORK_UNEXPECTED_STATUS` | `\OpenFGA\Messages::NETWORK_UNEXPECTED_STATUS` |  |
| `NO_LAST_REQUEST_FOUND` | `\OpenFGA\Messages::NO_LAST_REQUEST_FOUND` |  |
| `REQUEST_CONTINUATION_TOKEN_EMPTY` | `\OpenFGA\Messages::REQUEST_CONTINUATION_TOKEN_EMPTY` |  |
| `REQUEST_MODEL_ID_EMPTY` | `\OpenFGA\Messages::REQUEST_MODEL_ID_EMPTY` |  |
| `REQUEST_OBJECT_EMPTY` | `\OpenFGA\Messages::REQUEST_OBJECT_EMPTY` |  |
| `REQUEST_OBJECT_TYPE_EMPTY` | `\OpenFGA\Messages::REQUEST_OBJECT_TYPE_EMPTY` |  |
| `REQUEST_PAGE_SIZE_INVALID` | `\OpenFGA\Messages::REQUEST_PAGE_SIZE_INVALID` |  |
| `REQUEST_RELATION_EMPTY` | `\OpenFGA\Messages::REQUEST_RELATION_EMPTY` |  |
| `REQUEST_STORE_ID_EMPTY` | `\OpenFGA\Messages::REQUEST_STORE_ID_EMPTY` |  |
| `REQUEST_STORE_NAME_EMPTY` | `\OpenFGA\Messages::REQUEST_STORE_NAME_EMPTY` |  |
| `REQUEST_TYPE_EMPTY` | `\OpenFGA\Messages::REQUEST_TYPE_EMPTY` |  |
| `REQUEST_USER_EMPTY` | `\OpenFGA\Messages::REQUEST_USER_EMPTY` |  |
| `RESULT_FAILURE_NO_VALUE` | `\OpenFGA\Messages::RESULT_FAILURE_NO_VALUE` |  |
| `RESULT_SUCCESS_NO_ERROR` | `\OpenFGA\Messages::RESULT_SUCCESS_NO_ERROR` |  |
| `SCHEMA_CLASS_NOT_FOUND` | `\OpenFGA\Messages::SCHEMA_CLASS_NOT_FOUND` |  |
| `SCHEMA_ITEM_TYPE_NOT_FOUND` | `\OpenFGA\Messages::SCHEMA_ITEM_TYPE_NOT_FOUND` |  |
| `SERIALIZATION_ERROR_COULD_NOT_ADD_ITEMS` | `\OpenFGA\Messages::SERIALIZATION_ERROR_COULD_NOT_ADD_ITEMS` |  |
| `SERIALIZATION_ERROR_EMPTY_COLLECTION` | `\OpenFGA\Messages::SERIALIZATION_ERROR_EMPTY_COLLECTION` |  |
| `SERIALIZATION_ERROR_INVALID_ITEM_TYPE` | `\OpenFGA\Messages::SERIALIZATION_ERROR_INVALID_ITEM_TYPE` |  |
| `SERIALIZATION_ERROR_MISSING_REQUIRED_PARAM` | `\OpenFGA\Messages::SERIALIZATION_ERROR_MISSING_REQUIRED_PARAM` |  |
| `SERIALIZATION_ERROR_RESPONSE` | `\OpenFGA\Messages::SERIALIZATION_ERROR_RESPONSE` |  |
| `SERIALIZATION_ERROR_UNDEFINED_ITEM_TYPE` | `\OpenFGA\Messages::SERIALIZATION_ERROR_UNDEFINED_ITEM_TYPE` |  |
| `TUPLE_OPERATION_DELETE_DESCRIPTION` | `\OpenFGA\Messages::TUPLE_OPERATION_DELETE_DESCRIPTION` |  |
| `TUPLE_OPERATION_WRITE_DESCRIPTION` | `\OpenFGA\Messages::TUPLE_OPERATION_WRITE_DESCRIPTION` |  |

## Cases
| Name | Value | Description |
|------|-------|-------------|
| `AUTH_ACCESS_TOKEN_MUST_BE_STRING` | `auth.access_token_must_be_string` |  |
| `AUTH_ERROR_TOKEN_EXPIRED` | `exception.auth.token_expired` |  |
| `AUTH_ERROR_TOKEN_INVALID` | `exception.auth.token_invalid` |  |
| `AUTH_EXPIRES_IN_MUST_BE_INTEGER` | `auth.expires_in_must_be_integer` |  |
| `AUTH_INVALID_RESPONSE_FORMAT` | `auth.invalid_response_format` |  |
| `AUTH_MISSING_REQUIRED_FIELDS` | `auth.missing_required_fields` |  |
| `AUTH_USER_MESSAGE_TOKEN_EXPIRED` | `auth.user_message.token_expired` |  |
| `AUTH_USER_MESSAGE_TOKEN_INVALID` | `auth.user_message.token_invalid` |  |
| `CLIENT_ERROR_AUTHENTICATION` | `exception.client.authentication` |  |
| `CLIENT_ERROR_CONFIGURATION` | `exception.client.configuration` |  |
| `CLIENT_ERROR_NETWORK` | `exception.client.network` |  |
| `CLIENT_ERROR_SERIALIZATION` | `exception.client.serialization` |  |
| `CLIENT_ERROR_VALIDATION` | `exception.client.validation` |  |
| `COLLECTION_INVALID_ITEM_INSTANCE` | `collection.invalid_item_instance` |  |
| `COLLECTION_INVALID_ITEM_TYPE_INTERFACE` | `collection.invalid_item_type_interface` |  |
| `COLLECTION_INVALID_KEY_TYPE` | `collection.invalid_key_type` |  |
| `COLLECTION_INVALID_POSITION` | `collection.invalid_position` |  |
| `COLLECTION_INVALID_VALUE_TYPE` | `collection.invalid_value_type` |  |
| `COLLECTION_KEY_MUST_BE_STRING` | `collection.key_must_be_string` |  |
| `COLLECTION_UNDEFINED_ITEM_TYPE` | `collection.undefined_item_type` |  |
| `CONFIG_ERROR_HTTP_CLIENT_MISSING` | `exception.config.http_client_missing` |  |
| `CONFIG_ERROR_HTTP_REQUEST_FACTORY_MISSING` | `exception.config.http_request_factory_missing` |  |
| `CONFIG_ERROR_HTTP_RESPONSE_FACTORY_MISSING` | `exception.config.http_response_factory_missing` |  |
| `CONFIG_ERROR_HTTP_STREAM_FACTORY_MISSING` | `exception.config.http_stream_factory_missing` |  |
| `CONSISTENCY_HIGHER_CONSISTENCY_DESCRIPTION` | `consistency.higher_consistency.description` |  |
| `CONSISTENCY_MINIMIZE_LATENCY_DESCRIPTION` | `consistency.minimize_latency.description` |  |
| `CONSISTENCY_UNSPECIFIED_DESCRIPTION` | `consistency.unspecified.description` |  |
| `DSL_INPUT_EMPTY` | `dsl.input_empty` |  |
| `DSL_INVALID_COMPUTED_USERSET` | `dsl.invalid_computed_userset` |  |
| `DSL_PARSE_FAILED` | `dsl.parse_failed` |  |
| `DSL_PATTERN_EMPTY` | `dsl.pattern_empty` |  |
| `DSL_UNBALANCED_PARENTHESES_CLOSING` | `dsl.unbalanced_parentheses_closing` |  |
| `DSL_UNBALANCED_PARENTHESES_OPENING` | `dsl.unbalanced_parentheses_opening` |  |
| `DSL_UNRECOGNIZED_TERM` | `dsl.unrecognized_term` |  |
| `INVALID_BATCH_CHECK_EMPTY` | `validation.batch_check_empty` |  |
| `INVALID_CORRELATION_ID` | `validation.invalid_correlation_id` |  |
| `JWT_INVALID_AUDIENCE` | `auth.jwt.invalid_audience` |  |
| `JWT_INVALID_FORMAT` | `auth.jwt.invalid_format` |  |
| `JWT_INVALID_HEADER` | `auth.jwt.invalid_header` |  |
| `JWT_INVALID_ISSUER` | `auth.jwt.invalid_issuer` |  |
| `JWT_INVALID_PAYLOAD` | `auth.jwt.invalid_payload` |  |
| `JWT_MISSING_REQUIRED_CLAIMS` | `auth.jwt.missing_required_claims` |  |
| `JWT_TOKEN_EXPIRED` | `auth.jwt.token_expired` |  |
| `JWT_TOKEN_NOT_YET_VALID` | `auth.jwt.token_not_yet_valid` |  |
| `MODEL_INVALID_TUPLE_KEY` | `model.invalid_tuple_key` |  |
| `MODEL_LEAF_MISSING_CONTENT` | `model.leaf_missing_content` |  |
| `MODEL_SOURCE_INFO_FILE_EMPTY` | `model.source_info_file_empty` |  |
| `MODEL_TYPED_WILDCARD_TYPE_EMPTY` | `model.typed_wildcard_type_empty` |  |
| `NETWORK_ERROR` | `network.error` |  |
| `NETWORK_ERROR_CONFLICT` | `exception.network.conflict` |  |
| `NETWORK_ERROR_FORBIDDEN` | `exception.network.forbidden` |  |
| `NETWORK_ERROR_INVALID` | `exception.network.invalid` |  |
| `NETWORK_ERROR_REQUEST` | `exception.network.request` |  |
| `NETWORK_ERROR_SERVER` | `exception.network.server` |  |
| `NETWORK_ERROR_TIMEOUT` | `exception.network.timeout` |  |
| `NETWORK_ERROR_UNAUTHENTICATED` | `exception.network.unauthenticated` |  |
| `NETWORK_ERROR_UNDEFINED_ENDPOINT` | `exception.network.undefined_endpoint` |  |
| `NETWORK_ERROR_UNEXPECTED` | `exception.network.unexpected` |  |
| `NETWORK_UNEXPECTED_STATUS` | `network.unexpected_status` |  |
| `NO_LAST_REQUEST_FOUND` | `client.no_last_request_found` |  |
| `REQUEST_CONTINUATION_TOKEN_EMPTY` | `request.continuation_token_empty` |  |
| `REQUEST_MODEL_ID_EMPTY` | `request.model_id_empty` |  |
| `REQUEST_OBJECT_EMPTY` | `request.object_empty` |  |
| `REQUEST_OBJECT_TYPE_EMPTY` | `request.object_type_empty` |  |
| `REQUEST_PAGE_SIZE_INVALID` | `request.page_size_invalid` |  |
| `REQUEST_RELATION_EMPTY` | `request.relation_empty` |  |
| `REQUEST_STORE_ID_EMPTY` | `request.store_id_empty` |  |
| `REQUEST_STORE_NAME_EMPTY` | `request.store_name_empty` |  |
| `REQUEST_TYPE_EMPTY` | `request.type_empty` |  |
| `REQUEST_USER_EMPTY` | `request.user_empty` |  |
| `RESULT_FAILURE_NO_VALUE` | `result.failure_no_value` |  |
| `RESULT_SUCCESS_NO_ERROR` | `result.success_no_error` |  |
| `SCHEMA_CLASS_NOT_FOUND` | `schema.class_not_found` |  |
| `SCHEMA_ITEM_TYPE_NOT_FOUND` | `schema.item_type_not_found` |  |
| `SERIALIZATION_ERROR_COULD_NOT_ADD_ITEMS` | `exception.serialization.could_not_add_items_to_collection` |  |
| `SERIALIZATION_ERROR_EMPTY_COLLECTION` | `exception.serialization.empty_collection` |  |
| `SERIALIZATION_ERROR_INVALID_ITEM_TYPE` | `exception.serialization.invalid_item_type` |  |
| `SERIALIZATION_ERROR_MISSING_REQUIRED_PARAM` | `exception.serialization.missing_required_constructor_parameter` |  |
| `SERIALIZATION_ERROR_RESPONSE` | `exception.serialization.response` |  |
| `SERIALIZATION_ERROR_UNDEFINED_ITEM_TYPE` | `exception.serialization.undefined_item_type` |  |
| `TUPLE_OPERATION_DELETE_DESCRIPTION` | `tuple_operation.delete.description` |  |
| `TUPLE_OPERATION_WRITE_DESCRIPTION` | `tuple_operation.write.description` |  |

## Methods

                        
#### key


```php
public function key(): string
```

Get the translation key for this message.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Messages.php#L275)


#### Returns
`string`
