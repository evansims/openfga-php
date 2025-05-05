<?php

declare(strict_types=1);

namespace OpenFGA\Endpoints;

use Exception;
use OpenFGA\API\Models\{CheckRequest, CheckResponse, ExpandRequest, ExpandResponse, ListObjectsRequest, ListObjectsResponse, ListUsersRequest, ListUsersResponse, StreamedListObjectsResponse};
use OpenFGA\API\Options\{CheckOptions, ExpandOptions, ListObjectsOptions, ListUsersOptions, StreamedListObjectsOptions};
use OpenFGA\API\Request;
use Psr\Http\Message\{RequestInterface, ResponseInterface};

trait RelationshipQueriesEndpoint
{
    public ?RequestInterface $lastRequest = null;

    public ?ResponseInterface $lastResponse = null;

    final public function check(CheckRequest $request, ?string $storeId = null, ?CheckOptions $options = null): CheckResponse
    {
        $storeId ??= $this->getConfiguration()->getStoreId();

        if (null === $options) {
            $options = new CheckOptions();
        }

        $api = new Request(
            client: $this,
            options: $options,
            endpoint: '/stores/' . $storeId . '/check',
        );

        $this->lastRequest = $api->getRequest();

        $response = $api->post();

        $this->lastResponse = $response;

        if (200 !== $response->getStatusCode()) {
            throw new Exception("POST /stores/{$storeId}/check failed");
        }

        $json = $api->getResponseBodyJson();

        return new CheckResponse($json);
    }

    final public function expand(ExpandRequest $request, ?string $storeId = null, ?ExpandOptions $options = null): ExpandResponse
    {
        $storeId ??= $this->getConfiguration()->getStoreId();

        if (null === $options) {
            $options = new ExpandOptions();
        }

        $api = new Request(
            client: $this,
            options: $options,
            endpoint: '/stores/' . $storeId . '/expand',
        );

        $this->lastRequest = $api->getRequest();

        $response = $api->post();

        $this->lastResponse = $response;

        if (200 !== $response->getStatusCode()) {
            throw new Exception("POST /stores/{$storeId}/expand failed");
        }

        $json = $api->getResponseBodyJson();

        return new ExpandResponse($json);
    }

    final public function listObjects(ListObjectsRequest $request, ?string $storeId = null, ?ListObjectsOptions $options = null): ListObjectsResponse
    {
        $storeId ??= $this->getConfiguration()->getStoreId();

        if (null === $options) {
            $options = new ListObjectsOptions();
        }

        $api = new Request(
            client: $this,
            options: $options,
            endpoint: '/stores/' . $storeId . '/list-objects',
        );

        $this->lastRequest = $api->getRequest();

        $response = $api->post();

        $this->lastResponse = $response;

        if (200 !== $response->getStatusCode()) {
            throw new Exception("POST /stores/{$storeId}/list-objects failed");
        }

        $json = $api->getResponseBodyJson();

        return new ListObjectsResponse($json);
    }

    final public function listUsers(ListUsersRequest $request, ?string $storeId = null, ?ListUsersOptions $options = null): ListUsersResponse
    {
        $storeId ??= $this->getConfiguration()->getStoreId();

        if (null === $options) {
            $options = new ListUsersOptions();
        }

        $api = new Request(
            client: $this,
            options: $options,
            endpoint: '/stores/' . $storeId . '/list-users',
        );

        $this->lastRequest = $api->getRequest();

        $response = $api->post();

        $this->lastResponse = $response;

        if (200 !== $response->getStatusCode()) {
            throw new Exception("POST /stores/{$storeId}/list-users failed");
        }

        $json = $api->getResponseBodyJson();

        return new ListUsersResponse($json);
    }

    final public function streamedListObjects(?string $storeId = null, ?StreamedListObjectsOptions $options = null): StreamedListObjectsResponse
    {
        $storeId ??= $this->getConfiguration()->getStoreId();

        if (null === $options) {
            $options = new StreamedListObjectsOptions();
        }

        $api = new Request(
            client: $this,
            options: $options,
            endpoint: '/stores/' . $storeId . '/streamed-list-users',
        );

        $this->lastRequest = $api->getRequest();

        $response = $api->post();

        $this->lastResponse = $response;

        if (200 !== $response->getStatusCode()) {
            throw new Exception("POST /stores/{$storeId}/streamed-list-users failed");
        }

        $json = $api->getResponseBodyJson();

        return new StreamedListObjectsResponse($json);
    }
}
