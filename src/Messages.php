<?php

declare(strict_types=1);

namespace OpenFGA;

/**
 * Centralized message keys for all exception messages in the OpenFGA PHP SDK.
 *
 * This enum provides type-safe access to all translatable message keys used
 * throughout the library for exceptions, error messages, and user-facing text.
 * Messages are organized by category and support parameter substitution for
 * dynamic content through the translation system.
 *
 * All message keys map to translations in the translation files located in
 * the translations/ directory, supporting multiple locales for internationalization.
 *
 * @see Translation\TranslatorInterface For the translation system
 */
enum Messages: string
{
    // Assertion validation messages
    case ASSERTIONS_EMPTY_COLLECTION = 'assertions.empty_collection';

    case ASSERTIONS_INVALID_TUPLE_KEY = 'assertions.invalid_tuple_key';

    case AUTH_ACCESS_TOKEN_MUST_BE_STRING = 'auth.access_token_must_be_string';

    // AuthenticationError defaults
    case AUTH_ERROR_TOKEN_EXPIRED = 'exception.auth.token_expired';

    case AUTH_ERROR_TOKEN_INVALID = 'exception.auth.token_invalid';

    case AUTH_EXPIRES_IN_MUST_BE_INTEGER = 'auth.expires_in_must_be_integer';

    // Authentication messages
    case AUTH_INVALID_RESPONSE_FORMAT = 'auth.invalid_response_format';

    case AUTH_MISSING_REQUIRED_FIELDS = 'auth.missing_required_fields';

    // User-friendly authentication messages
    case AUTH_USER_MESSAGE_TOKEN_EXPIRED = 'auth.user_message.token_expired';

    case AUTH_USER_MESSAGE_TOKEN_INVALID = 'auth.user_message.token_invalid';

    case BATCH_TUPLE_CHUNK_SIZE_EXCEEDED = 'validation.batch_tuple_chunk_size_exceeded';

    // Batch tuple operation validation messages
    case BATCH_TUPLE_CHUNK_SIZE_POSITIVE = 'validation.batch_tuple_chunk_size_positive';

    // Default Exception Messages for Error Enums
    // ClientError defaults
    case CLIENT_ERROR_AUTHENTICATION = 'exception.client.authentication';

    case CLIENT_ERROR_CONFIGURATION = 'exception.client.configuration';

    case CLIENT_ERROR_NETWORK = 'exception.client.network';

    case CLIENT_ERROR_SERIALIZATION = 'exception.client.serialization';

    case CLIENT_ERROR_VALIDATION = 'exception.client.validation';

    case COLLECTION_INVALID_ITEM_INSTANCE = 'collection.invalid_item_instance';

    case COLLECTION_INVALID_ITEM_TYPE_INTERFACE = 'collection.invalid_item_type_interface';

    case COLLECTION_INVALID_KEY_TYPE = 'collection.invalid_key_type';

    case COLLECTION_INVALID_POSITION = 'collection.invalid_position';

    case COLLECTION_INVALID_VALUE_TYPE = 'collection.invalid_value_type';

    case COLLECTION_KEY_MUST_BE_STRING = 'collection.key_must_be_string';

    // Collection type validation messages
    case COLLECTION_UNDEFINED_ITEM_TYPE = 'collection.undefined_item_type';

    // ConfigurationError defaults
    case CONFIG_ERROR_HTTP_CLIENT_MISSING = 'exception.config.http_client_missing';

    case CONFIG_ERROR_HTTP_REQUEST_FACTORY_MISSING = 'exception.config.http_request_factory_missing';

    case CONFIG_ERROR_HTTP_RESPONSE_FACTORY_MISSING = 'exception.config.http_response_factory_missing';

    case CONFIG_ERROR_HTTP_STREAM_FACTORY_MISSING = 'exception.config.http_stream_factory_missing';

    case CONFIG_ERROR_INVALID_LANGUAGE = 'exception.config.invalid_language';

    case CONFIG_ERROR_INVALID_RETRY_COUNT = 'exception.config.invalid_retry_count';

    case CONFIG_ERROR_INVALID_URL = 'exception.config.invalid_url';

    // Consistency enum descriptions
    case CONSISTENCY_HIGHER_CONSISTENCY_DESCRIPTION = 'consistency.higher_consistency.description';

    case CONSISTENCY_MINIMIZE_LATENCY_DESCRIPTION = 'consistency.minimize_latency.description';

    case CONSISTENCY_UNSPECIFIED_DESCRIPTION = 'consistency.unspecified.description';

    case DSL_INPUT_EMPTY = 'dsl.input_empty';

    case DSL_INVALID_COMPUTED_USERSET = 'dsl.invalid_computed_userset';

    case DSL_INVALID_COMPUTED_USERSET_RELATION = 'dsl.invalid_computed_userset_relation';

    // DSL Transformer messages
    case DSL_PARSE_FAILED = 'dsl.parse_failed';

    case DSL_PATTERN_EMPTY = 'dsl.pattern_empty';

    case DSL_UNBALANCED_PARENTHESES_CLOSING = 'dsl.unbalanced_parentheses_closing';

    case DSL_UNBALANCED_PARENTHESES_OPENING = 'dsl.unbalanced_parentheses_opening';

    case DSL_UNRECOGNIZED_TERM = 'dsl.unrecognized_term';

    // Batch check validation messages
    case INVALID_BATCH_CHECK_EMPTY = 'validation.batch_check_empty';

    case INVALID_CORRELATION_ID = 'validation.invalid_correlation_id';

    case JWT_INVALID_AUDIENCE = 'auth.jwt.invalid_audience';

    // JWT validation messages
    case JWT_INVALID_FORMAT = 'auth.jwt.invalid_format';

    case JWT_INVALID_HEADER = 'auth.jwt.invalid_header';

    case JWT_INVALID_ISSUER = 'auth.jwt.invalid_issuer';

    case JWT_INVALID_PAYLOAD = 'auth.jwt.invalid_payload';

    case JWT_MISSING_REQUIRED_CLAIMS = 'auth.jwt.missing_required_claims';

    case JWT_TOKEN_EXPIRED = 'auth.jwt.token_expired';

    case JWT_TOKEN_NOT_YET_VALID = 'auth.jwt.token_not_yet_valid';

    case MODEL_DUPLICATE_TYPE = 'model.duplicate_type';

    case MODEL_INVALID_IDENTIFIER_FORMAT = 'model.invalid_identifier_format';

    // Model validation messages
    case MODEL_INVALID_TUPLE_KEY = 'model.invalid_tuple_key';

    case MODEL_LEAF_MISSING_CONTENT = 'model.leaf_missing_content';

    case MODEL_NO_MODELS_IN_STORE = 'model.no_models_in_store';

    case MODEL_SOURCE_INFO_FILE_EMPTY = 'model.source_info_file_empty';

    case MODEL_TYPE_DEFINITIONS_EMPTY = 'model.type_definitions_empty';

    case MODEL_TYPED_WILDCARD_TYPE_EMPTY = 'model.typed_wildcard_type_empty';

    // Network messages
    case NETWORK_ERROR = 'network.error';

    // NetworkError defaults
    case NETWORK_ERROR_CONFLICT = 'exception.network.conflict';

    case NETWORK_ERROR_FORBIDDEN = 'exception.network.forbidden';

    case NETWORK_ERROR_INVALID = 'exception.network.invalid';

    case NETWORK_ERROR_REQUEST = 'exception.network.request';

    case NETWORK_ERROR_SERVER = 'exception.network.server';

    case NETWORK_ERROR_TIMEOUT = 'exception.network.timeout';

    case NETWORK_ERROR_UNAUTHENTICATED = 'exception.network.unauthenticated';

    case NETWORK_ERROR_UNDEFINED_ENDPOINT = 'exception.network.undefined_endpoint';

    case NETWORK_ERROR_UNEXPECTED = 'exception.network.unexpected';

    case NETWORK_UNEXPECTED_STATUS = 'network.unexpected_status';

    // Client validation messages
    case NO_LAST_REQUEST_FOUND = 'client.no_last_request_found';

    case REQUEST_CONTINUATION_TOKEN_EMPTY = 'request.continuation_token_empty';

    case REQUEST_MODEL_ID_EMPTY = 'request.model_id_empty';

    case REQUEST_OBJECT_EMPTY = 'request.object_empty';

    case REQUEST_OBJECT_TYPE_EMPTY = 'request.object_type_empty';

    case REQUEST_PAGE_SIZE_INVALID = 'request.page_size_invalid';

    case REQUEST_RELATION_EMPTY = 'request.relation_empty';

    // Request validation messages
    case REQUEST_STORE_ID_EMPTY = 'request.store_id_empty';

    case REQUEST_STORE_NAME_EMPTY = 'request.store_name_empty';

    case REQUEST_TRANSACTIONAL_LIMIT_EXCEEDED = 'request.transactional_limit_exceeded';

    case REQUEST_TYPE_EMPTY = 'request.type_empty';

    case REQUEST_USER_EMPTY = 'request.user_empty';

    case RESPONSE_UNEXPECTED_TYPE = 'response.unexpected_type';

    case RESULT_FAILURE_NO_VALUE = 'result.failure_no_value';

    // Result pattern messages
    case RESULT_SUCCESS_NO_ERROR = 'result.success_no_error';

    // Schema validation messages
    case SCHEMA_CLASS_NOT_FOUND = 'schema.class_not_found';

    case SCHEMA_ITEM_TYPE_NOT_FOUND = 'schema.item_type_not_found';

    // SerializationError defaults
    case SERIALIZATION_ERROR_COULD_NOT_ADD_ITEMS = 'exception.serialization.could_not_add_items_to_collection';

    case SERIALIZATION_ERROR_EMPTY_COLLECTION = 'exception.serialization.empty_collection';

    case SERIALIZATION_ERROR_INVALID_ITEM_TYPE = 'exception.serialization.invalid_item_type';

    case SERIALIZATION_ERROR_MISSING_REQUIRED_PARAM = 'exception.serialization.missing_required_constructor_parameter';

    case SERIALIZATION_ERROR_RESPONSE = 'exception.serialization.response';

    case SERIALIZATION_ERROR_UNDEFINED_ITEM_TYPE = 'exception.serialization.undefined_item_type';

    // Service availability messages
    case SERVICE_HTTP_NOT_AVAILABLE = 'service.http_not_available';

    case SERVICE_SCHEMA_VALIDATOR_NOT_AVAILABLE = 'service.schema_validator_not_available';

    case SERVICE_STORE_REPOSITORY_NOT_AVAILABLE = 'service.store_repository_not_available';

    case SERVICE_TUPLE_FILTER_NOT_AVAILABLE = 'service.tuple_filter_not_available';

    case SERVICE_TUPLE_REPOSITORY_NOT_AVAILABLE = 'service.tuple_repository_not_available';

    // Store validation messages
    case STORE_NAME_REQUIRED = 'store.name_required';

    case STORE_NAME_TOO_LONG = 'store.name_too_long';

    case STORE_NOT_FOUND = 'store.not_found';

    // Translation system messages
    case TRANSLATION_FILE_NOT_FOUND = 'translation.file_not_found';

    case TRANSLATION_UNSUPPORTED_FORMAT = 'translation.unsupported_format';

    // TupleOperation enum descriptions
    case TUPLE_OPERATION_DELETE_DESCRIPTION = 'tuple_operation.delete.description';

    case TUPLE_OPERATION_WRITE_DESCRIPTION = 'tuple_operation.write.description';

    case YAML_CANNOT_READ_FILE = 'yaml.cannot_read_file';

    // YAML parsing messages
    case YAML_FILE_DOES_NOT_EXIST = 'yaml.file_does_not_exist';

    case YAML_INVALID_STRUCTURE = 'yaml.invalid_structure';

    case YAML_INVALID_SYNTAX_EMPTY_KEY = 'yaml.invalid_syntax_empty_key';

    case YAML_INVALID_SYNTAX_MISSING_COLON = 'yaml.invalid_syntax_missing_colon';

    case YAML_INVALID_SYNTAX_MISSING_VALUE = 'yaml.invalid_syntax_missing_value';

    /**
     * Get all message keys grouped by category.
     *
     * @return string[][]
     *
     * @psalm-return array{client: list{'client.no_last_request_found'}, dsl: list{'dsl.parse_failed', 'dsl.unrecognized_term', 'dsl.input_empty', 'dsl.pattern_empty', 'dsl.unbalanced_parentheses_closing', 'dsl.unbalanced_parentheses_opening', 'dsl.invalid_computed_userset', 'dsl.invalid_computed_userset_relation'}, auth: list{'auth.invalid_response_format', 'auth.missing_required_fields', 'auth.access_token_must_be_string', 'auth.expires_in_must_be_integer', 'auth.jwt.invalid_format', 'auth.jwt.invalid_header', 'auth.jwt.invalid_payload', 'auth.jwt.missing_required_claims', 'auth.jwt.token_expired', 'auth.jwt.token_not_yet_valid', 'auth.jwt.invalid_audience', 'auth.jwt.invalid_issuer'}, network: list{'network.error', 'network.unexpected_status'}, result: list{'result.success_no_error', 'result.failure_no_value'}, request: list{'request.store_id_empty', 'request.model_id_empty', 'request.transactional_limit_exceeded'}, model: list{'model.invalid_tuple_key', 'model.invalid_identifier_format', 'model.typed_wildcard_type_empty', 'model.source_info_file_empty', 'model.leaf_missing_content'}, collection: list{'collection.undefined_item_type', 'collection.invalid_item_type_interface', 'collection.invalid_item_instance', 'collection.invalid_value_type', 'collection.key_must_be_string', 'collection.invalid_position', 'collection.invalid_key_type'}, translation: list{'translation.file_not_found', 'translation.unsupported_format'}, yaml: list{'yaml.file_does_not_exist', 'yaml.cannot_read_file', 'yaml.invalid_syntax_missing_colon', 'yaml.invalid_syntax_missing_value', 'yaml.invalid_syntax_empty_key', 'yaml.invalid_structure'}, validation: list{'validation.batch_check_empty', 'validation.invalid_correlation_id', 'validation.batch_tuple_chunk_size_positive', 'validation.batch_tuple_chunk_size_exceeded'}, service: list{'service.http_not_available', 'service.schema_validator_not_available', 'service.tuple_filter_not_available', 'service.store_repository_not_available', 'service.tuple_repository_not_available'}}
     */
    public static function getGroupedKeys(): array
    {
        return [
            'client' => [
                self::NO_LAST_REQUEST_FOUND->value,
            ],
            'dsl' => [
                self::DSL_PARSE_FAILED->value,
                self::DSL_UNRECOGNIZED_TERM->value,
                self::DSL_INPUT_EMPTY->value,
                self::DSL_PATTERN_EMPTY->value,
                self::DSL_UNBALANCED_PARENTHESES_CLOSING->value,
                self::DSL_UNBALANCED_PARENTHESES_OPENING->value,
                self::DSL_INVALID_COMPUTED_USERSET->value,
                self::DSL_INVALID_COMPUTED_USERSET_RELATION->value,
            ],
            'auth' => [
                self::AUTH_INVALID_RESPONSE_FORMAT->value,
                self::AUTH_MISSING_REQUIRED_FIELDS->value,
                self::AUTH_ACCESS_TOKEN_MUST_BE_STRING->value,
                self::AUTH_EXPIRES_IN_MUST_BE_INTEGER->value,
                self::JWT_INVALID_FORMAT->value,
                self::JWT_INVALID_HEADER->value,
                self::JWT_INVALID_PAYLOAD->value,
                self::JWT_MISSING_REQUIRED_CLAIMS->value,
                self::JWT_TOKEN_EXPIRED->value,
                self::JWT_TOKEN_NOT_YET_VALID->value,
                self::JWT_INVALID_AUDIENCE->value,
                self::JWT_INVALID_ISSUER->value,
            ],
            'network' => [
                self::NETWORK_ERROR->value,
                self::NETWORK_UNEXPECTED_STATUS->value,
            ],
            'result' => [
                self::RESULT_SUCCESS_NO_ERROR->value,
                self::RESULT_FAILURE_NO_VALUE->value,
            ],
            'request' => [
                self::REQUEST_STORE_ID_EMPTY->value,
                self::REQUEST_MODEL_ID_EMPTY->value,
                self::REQUEST_TRANSACTIONAL_LIMIT_EXCEEDED->value,
            ],
            'model' => [
                self::MODEL_INVALID_TUPLE_KEY->value,
                self::MODEL_INVALID_IDENTIFIER_FORMAT->value,
                self::MODEL_TYPED_WILDCARD_TYPE_EMPTY->value,
                self::MODEL_SOURCE_INFO_FILE_EMPTY->value,
                self::MODEL_LEAF_MISSING_CONTENT->value,
            ],
            'collection' => [
                self::COLLECTION_UNDEFINED_ITEM_TYPE->value,
                self::COLLECTION_INVALID_ITEM_TYPE_INTERFACE->value,
                self::COLLECTION_INVALID_ITEM_INSTANCE->value,
                self::COLLECTION_INVALID_VALUE_TYPE->value,
                self::COLLECTION_KEY_MUST_BE_STRING->value,
                self::COLLECTION_INVALID_POSITION->value,
                self::COLLECTION_INVALID_KEY_TYPE->value,
            ],
            'translation' => [
                self::TRANSLATION_FILE_NOT_FOUND->value,
                self::TRANSLATION_UNSUPPORTED_FORMAT->value,
            ],
            'yaml' => [
                self::YAML_FILE_DOES_NOT_EXIST->value,
                self::YAML_CANNOT_READ_FILE->value,
                self::YAML_INVALID_SYNTAX_MISSING_COLON->value,
                self::YAML_INVALID_SYNTAX_MISSING_VALUE->value,
                self::YAML_INVALID_SYNTAX_EMPTY_KEY->value,
                self::YAML_INVALID_STRUCTURE->value,
            ],
            'validation' => [
                self::INVALID_BATCH_CHECK_EMPTY->value,
                self::INVALID_CORRELATION_ID->value,
                self::BATCH_TUPLE_CHUNK_SIZE_POSITIVE->value,
                self::BATCH_TUPLE_CHUNK_SIZE_EXCEEDED->value,
            ],
            'service' => [
                self::SERVICE_HTTP_NOT_AVAILABLE->value,
                self::SERVICE_SCHEMA_VALIDATOR_NOT_AVAILABLE->value,
                self::SERVICE_TUPLE_FILTER_NOT_AVAILABLE->value,
                self::SERVICE_STORE_REPOSITORY_NOT_AVAILABLE->value,
                self::SERVICE_TUPLE_REPOSITORY_NOT_AVAILABLE->value,
            ],
        ];
    }

    /**
     * Get the translation key for this message.
     */
    public function key(): string
    {
        return $this->value;
    }
}
