<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit;

use OpenFGA\Messages;

describe('Messages', function (): void {
    test('Messages enum has all required auth message cases', function (): void {
        expect(Messages::AUTH_ACCESS_TOKEN_MUST_BE_STRING->value)->toBe('auth.access_token_must_be_string');
        expect(Messages::AUTH_ERROR_TOKEN_EXPIRED->value)->toBe('exception.auth.token_expired');
        expect(Messages::AUTH_ERROR_TOKEN_INVALID->value)->toBe('exception.auth.token_invalid');
        expect(Messages::AUTH_EXPIRES_IN_MUST_BE_INTEGER->value)->toBe('auth.expires_in_must_be_integer');
        expect(Messages::AUTH_INVALID_RESPONSE_FORMAT->value)->toBe('auth.invalid_response_format');
        expect(Messages::AUTH_MISSING_REQUIRED_FIELDS->value)->toBe('auth.missing_required_fields');
    });

    test('Messages enum has all required JWT message cases', function (): void {
        expect(Messages::JWT_INVALID_AUDIENCE->value)->toBe('auth.jwt.invalid_audience');
        expect(Messages::JWT_INVALID_FORMAT->value)->toBe('auth.jwt.invalid_format');
        expect(Messages::JWT_INVALID_HEADER->value)->toBe('auth.jwt.invalid_header');
        expect(Messages::JWT_INVALID_ISSUER->value)->toBe('auth.jwt.invalid_issuer');
        expect(Messages::JWT_INVALID_PAYLOAD->value)->toBe('auth.jwt.invalid_payload');
        expect(Messages::JWT_MISSING_REQUIRED_CLAIMS->value)->toBe('auth.jwt.missing_required_claims');
        expect(Messages::JWT_TOKEN_EXPIRED->value)->toBe('auth.jwt.token_expired');
        expect(Messages::JWT_TOKEN_NOT_YET_VALID->value)->toBe('auth.jwt.token_not_yet_valid');
    });

    test('Messages enum has all required client error message cases', function (): void {
        expect(Messages::CLIENT_ERROR_AUTHENTICATION->value)->toBe('exception.client.authentication');
        expect(Messages::CLIENT_ERROR_CONFIGURATION->value)->toBe('exception.client.configuration');
        expect(Messages::CLIENT_ERROR_NETWORK->value)->toBe('exception.client.network');
        expect(Messages::CLIENT_ERROR_SERIALIZATION->value)->toBe('exception.client.serialization');
        expect(Messages::CLIENT_ERROR_VALIDATION->value)->toBe('exception.client.validation');
        expect(Messages::NO_LAST_REQUEST_FOUND->value)->toBe('client.no_last_request_found');
    });

    test('Messages enum has all required config error message cases', function (): void {
        expect(Messages::CONFIG_ERROR_HTTP_CLIENT_MISSING->value)->toBe('exception.config.http_client_missing');
        expect(Messages::CONFIG_ERROR_HTTP_REQUEST_FACTORY_MISSING->value)->toBe('exception.config.http_request_factory_missing');
        expect(Messages::CONFIG_ERROR_HTTP_RESPONSE_FACTORY_MISSING->value)->toBe('exception.config.http_response_factory_missing');
        expect(Messages::CONFIG_ERROR_HTTP_STREAM_FACTORY_MISSING->value)->toBe('exception.config.http_stream_factory_missing');
    });

    test('Messages enum has all required DSL message cases', function (): void {
        expect(Messages::DSL_INPUT_EMPTY->value)->toBe('dsl.input_empty');
        expect(Messages::DSL_INVALID_COMPUTED_USERSET->value)->toBe('dsl.invalid_computed_userset');
        expect(Messages::DSL_PARSE_FAILED->value)->toBe('dsl.parse_failed');
        expect(Messages::DSL_PATTERN_EMPTY->value)->toBe('dsl.pattern_empty');
        expect(Messages::DSL_UNBALANCED_PARENTHESES_CLOSING->value)->toBe('dsl.unbalanced_parentheses_closing');
        expect(Messages::DSL_UNBALANCED_PARENTHESES_OPENING->value)->toBe('dsl.unbalanced_parentheses_opening');
        expect(Messages::DSL_UNRECOGNIZED_TERM->value)->toBe('dsl.unrecognized_term');
    });

    test('Messages enum has all required network error message cases', function (): void {
        expect(Messages::NETWORK_ERROR->value)->toBe('network.error');
        expect(Messages::NETWORK_ERROR_CONFLICT->value)->toBe('exception.network.conflict');
        expect(Messages::NETWORK_ERROR_FORBIDDEN->value)->toBe('exception.network.forbidden');
        expect(Messages::NETWORK_ERROR_INVALID->value)->toBe('exception.network.invalid');
        expect(Messages::NETWORK_ERROR_REQUEST->value)->toBe('exception.network.request');
        expect(Messages::NETWORK_ERROR_SERVER->value)->toBe('exception.network.server');
        expect(Messages::NETWORK_ERROR_TIMEOUT->value)->toBe('exception.network.timeout');
        expect(Messages::NETWORK_ERROR_UNAUTHENTICATED->value)->toBe('exception.network.unauthenticated');
        expect(Messages::NETWORK_ERROR_UNDEFINED_ENDPOINT->value)->toBe('exception.network.undefined_endpoint');
        expect(Messages::NETWORK_ERROR_UNEXPECTED->value)->toBe('exception.network.unexpected');
        expect(Messages::NETWORK_UNEXPECTED_STATUS->value)->toBe('network.unexpected_status');
    });

    test('Messages enum has all required request validation message cases', function (): void {
        expect(Messages::REQUEST_CONTINUATION_TOKEN_EMPTY->value)->toBe('request.continuation_token_empty');
        expect(Messages::REQUEST_MODEL_ID_EMPTY->value)->toBe('request.model_id_empty');
        expect(Messages::REQUEST_OBJECT_EMPTY->value)->toBe('request.object_empty');
        expect(Messages::REQUEST_OBJECT_TYPE_EMPTY->value)->toBe('request.object_type_empty');
        expect(Messages::REQUEST_PAGE_SIZE_INVALID->value)->toBe('request.page_size_invalid');
        expect(Messages::REQUEST_RELATION_EMPTY->value)->toBe('request.relation_empty');
        expect(Messages::REQUEST_STORE_ID_EMPTY->value)->toBe('request.store_id_empty');
        expect(Messages::REQUEST_STORE_NAME_EMPTY->value)->toBe('request.store_name_empty');
        expect(Messages::REQUEST_TRANSACTIONAL_LIMIT_EXCEEDED->value)->toBe('request.transactional_limit_exceeded');
        expect(Messages::REQUEST_TYPE_EMPTY->value)->toBe('request.type_empty');
        expect(Messages::REQUEST_USER_EMPTY->value)->toBe('request.user_empty');
    });

    test('Messages enum has all required model validation message cases', function (): void {
        expect(Messages::MODEL_INVALID_TUPLE_KEY->value)->toBe('model.invalid_tuple_key');
        expect(Messages::MODEL_INVALID_IDENTIFIER_FORMAT->value)->toBe('model.invalid_identifier_format');
        expect(Messages::MODEL_LEAF_MISSING_CONTENT->value)->toBe('model.leaf_missing_content');
        expect(Messages::MODEL_SOURCE_INFO_FILE_EMPTY->value)->toBe('model.source_info_file_empty');
        expect(Messages::MODEL_TYPED_WILDCARD_TYPE_EMPTY->value)->toBe('model.typed_wildcard_type_empty');
    });

    test('Messages enum has all required collection message cases', function (): void {
        expect(Messages::COLLECTION_INVALID_ITEM_INSTANCE->value)->toBe('collection.invalid_item_instance');
        expect(Messages::COLLECTION_INVALID_ITEM_TYPE_INTERFACE->value)->toBe('collection.invalid_item_type_interface');
        expect(Messages::COLLECTION_INVALID_KEY_TYPE->value)->toBe('collection.invalid_key_type');
        expect(Messages::COLLECTION_INVALID_POSITION->value)->toBe('collection.invalid_position');
        expect(Messages::COLLECTION_INVALID_VALUE_TYPE->value)->toBe('collection.invalid_value_type');
        expect(Messages::COLLECTION_KEY_MUST_BE_STRING->value)->toBe('collection.key_must_be_string');
        expect(Messages::COLLECTION_UNDEFINED_ITEM_TYPE->value)->toBe('collection.undefined_item_type');
    });

    test('Messages enum has all required result pattern message cases', function (): void {
        expect(Messages::RESULT_FAILURE_NO_VALUE->value)->toBe('result.failure_no_value');
        expect(Messages::RESULT_SUCCESS_NO_ERROR->value)->toBe('result.success_no_error');
    });

    test('Messages enum has all required schema validation message cases', function (): void {
        expect(Messages::SCHEMA_CLASS_NOT_FOUND->value)->toBe('schema.class_not_found');
        expect(Messages::SCHEMA_ITEM_TYPE_NOT_FOUND->value)->toBe('schema.item_type_not_found');
    });

    test('Messages enum has all required serialization error message cases', function (): void {
        expect(Messages::SERIALIZATION_ERROR_COULD_NOT_ADD_ITEMS->value)->toBe('exception.serialization.could_not_add_items_to_collection');
        expect(Messages::SERIALIZATION_ERROR_EMPTY_COLLECTION->value)->toBe('exception.serialization.empty_collection');
        expect(Messages::SERIALIZATION_ERROR_INVALID_ITEM_TYPE->value)->toBe('exception.serialization.invalid_item_type');
        expect(Messages::SERIALIZATION_ERROR_MISSING_REQUIRED_PARAM->value)->toBe('exception.serialization.missing_required_constructor_parameter');
        expect(Messages::SERIALIZATION_ERROR_RESPONSE->value)->toBe('exception.serialization.response');
        expect(Messages::SERIALIZATION_ERROR_UNDEFINED_ITEM_TYPE->value)->toBe('exception.serialization.undefined_item_type');
    });

    test('key() method returns correct value', function (): void {
        $message = Messages::NO_LAST_REQUEST_FOUND;
        expect($message->key())->toBe('client.no_last_request_found');
        expect($message->key())->toBe($message->value);
    });

    test('getGroupedKeys() returns organized message structure', function (): void {
        $grouped = Messages::getGroupedKeys();

        expect($grouped)->toHaveKey('client');
        expect($grouped)->toHaveKey('dsl');
        expect($grouped)->toHaveKey('auth');
        expect($grouped)->toHaveKey('network');
        expect($grouped)->toHaveKey('result');
        expect($grouped)->toHaveKey('request');
        expect($grouped)->toHaveKey('model');
        expect($grouped)->toHaveKey('collection');

        expect($grouped['client'])->toContain('client.no_last_request_found');
        expect($grouped['auth'])->toContain('auth.invalid_response_format');
        expect($grouped['dsl'])->toContain('dsl.parse_failed');
        expect($grouped['network'])->toContain('network.error');
        expect($grouped['result'])->toContain('result.success_no_error');
        expect($grouped['model'])->toContain('model.invalid_tuple_key');
        expect($grouped['collection'])->toContain('collection.undefined_item_type');
    });

    test('getGroupedKeys() contains all client messages', function (): void {
        $grouped = Messages::getGroupedKeys();

        expect($grouped['client'])->toEqual([
            'client.no_last_request_found',
        ]);
    });

    test('getGroupedKeys() contains all DSL messages', function (): void {
        $grouped = Messages::getGroupedKeys();

        expect($grouped['dsl'])->toEqual([
            'dsl.parse_failed',
            'dsl.unrecognized_term',
            'dsl.input_empty',
            'dsl.pattern_empty',
            'dsl.unbalanced_parentheses_closing',
            'dsl.unbalanced_parentheses_opening',
            'dsl.invalid_computed_userset',
            'dsl.invalid_computed_userset_relation',
        ]);
    });

    test('getGroupedKeys() contains all auth messages', function (): void {
        $grouped = Messages::getGroupedKeys();

        expect($grouped['auth'])->toEqual([
            'auth.invalid_response_format',
            'auth.missing_required_fields',
            'auth.access_token_must_be_string',
            'auth.expires_in_must_be_integer',
            'auth.jwt.invalid_format',
            'auth.jwt.invalid_header',
            'auth.jwt.invalid_payload',
            'auth.jwt.missing_required_claims',
            'auth.jwt.token_expired',
            'auth.jwt.token_not_yet_valid',
            'auth.jwt.invalid_audience',
            'auth.jwt.invalid_issuer',
        ]);
    });

    test('getGroupedKeys() contains all network messages', function (): void {
        $grouped = Messages::getGroupedKeys();

        expect($grouped['network'])->toEqual([
            'network.error',
            'network.unexpected_status',
        ]);
    });

    test('getGroupedKeys() contains all result messages', function (): void {
        $grouped = Messages::getGroupedKeys();

        expect($grouped['result'])->toEqual([
            'result.success_no_error',
            'result.failure_no_value',
        ]);
    });

    test('getGroupedKeys() contains all request messages', function (): void {
        $grouped = Messages::getGroupedKeys();

        expect($grouped['request'])->toEqual([
            'request.store_id_empty',
            'request.model_id_empty',
            'request.transactional_limit_exceeded',
        ]);
    });

    test('getGroupedKeys() contains all model messages', function (): void {
        $grouped = Messages::getGroupedKeys();

        expect($grouped['model'])->toEqual([
            'model.invalid_tuple_key',
            'model.invalid_identifier_format',
            'model.typed_wildcard_type_empty',
            'model.source_info_file_empty',
            'model.leaf_missing_content',
        ]);
    });

    test('getGroupedKeys() contains all collection messages', function (): void {
        $grouped = Messages::getGroupedKeys();

        expect($grouped['collection'])->toEqual([
            'collection.undefined_item_type',
            'collection.invalid_item_type_interface',
            'collection.invalid_item_instance',
            'collection.invalid_value_type',
            'collection.key_must_be_string',
            'collection.invalid_position',
            'collection.invalid_key_type',
        ]);
    });

    test('all enum cases have string values', function (): void {
        $cases = Messages::cases();

        foreach ($cases as $case) {
            expect($case->value)->toBeString();
            expect($case->value)->not()->toBeEmpty();
        }
    });

    test('all enum values follow expected format', function (): void {
        $cases = Messages::cases();

        foreach ($cases as $case) {
            expect($case->value)->toMatch('/^[a-z_]+\.[a-z_]+(\.[a-z_]+)?$/');
        }
    });

    test('enum cases can be serialized to string', function (): void {
        $message = Messages::NO_LAST_REQUEST_FOUND;

        expect((string) $message->value)->toBe('client.no_last_request_found');
        expect($message->key())->toBe('client.no_last_request_found');
    });

    test('covers all message categories used throughout the library', function (): void {
        $grouped = Messages::getGroupedKeys();
        $expectedCategories = [
            'client', 'dsl', 'auth', 'network', 'result',
            'request', 'model', 'collection',
        ];

        foreach ($expectedCategories as $category) {
            expect($grouped)->toHaveKey($category);
            expect($grouped[$category])->toBeArray();
            expect($grouped[$category])->not()->toBeEmpty();
        }
    });
});
