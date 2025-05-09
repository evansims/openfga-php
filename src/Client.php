<?php

declare(strict_types=1);

namespace OpenFGA;

use Exception;
use OpenFGA\Authentication\{AuthenticationInterface, ClientCredentialAuthentication, NullCredentialAuthentication};
use OpenFGA\Credentials\ClientCredentialInterface;
use OpenFGA\Endpoints\{AssertionsEndpoint, AuthorizationModelsEndpoint, RelationshipQueriesEndpoint, RelationshipTuplesEndpoint, StoresEndpoint};
use OpenFGA\Requests\RequestFactory;
use Psr\Http\Message\{RequestInterface, ResponseInterface};

final class Client implements ClientInterface
{
    use AssertionsEndpoint;

    use AuthorizationModelsEndpoint;

    use RelationshipQueriesEndpoint;

    use RelationshipTuplesEndpoint;

    use StoresEndpoint;

    public const string VERSION = '0.2.0';

    public ?RequestInterface $lastRequest = null;

    public ?ResponseInterface $lastResponse = null;

    public function __construct(
        private ConfigurationInterface $configuration,
        private ?AuthenticationInterface $authentication = null,
        private ?RequestFactory $requestFactory = null,
    ) {
    }

    public function getAuthentication(): AuthenticationInterface
    {
        if (null === $this->authentication) {
            $credential = $this->getConfiguration()->getCredential();

            if ($credential instanceof ClientCredentialInterface) {
                $this->authentication = new ClientCredentialAuthentication($this);
            } else {
                $this->authentication = new NullCredentialAuthentication($this);
            }
        }

        return $this->authentication;
    }

    /**
     * Get the authorization model ID.
     *
     * @param null|string $modelId Optional model ID to override the one in configuration
     *
     * @throws Exception If no model ID is provided or configured
     *
     * @return string The authorization model ID
     */
    public function getAuthorizationModelId(?string $modelId = null): string
    {
        $modelId ??= $this->getConfiguration()->getAuthorizationModelId();

        if (null === $modelId) {
            throw new Exception('Authorization model ID is required');
        }

        return trim($modelId);
    }

    public function getConfiguration(): ConfigurationInterface
    {
        return $this->configuration;
    }

    public function getRequestFactory(): RequestFactory
    {
        if (null === $this->requestFactory) {
            $apiUrl = $this->getConfiguration()->getApiUrl();

            if (null === $apiUrl) {
                throw new Exception('API URL is required');
            }

            $this->requestFactory = new RequestFactory(
                apiUrl: $apiUrl,
                authorizationHeader: $this->getAuthentication()->getAuthorizationHeader(),
                httpClient: $this->getConfiguration()->getHttpClient(),
                httpStreamFactory: $this->getConfiguration()->getHttpStreamFactory(),
                httpRequestFactory: $this->getConfiguration()->getHttpRequestFactory(),
            );
        }

        return $this->requestFactory;
    }

    /**
     * Get the store ID.
     *
     * @param null|string $storeId Optional store ID to override the one in configuration
     *
     * @throws Exception If no store ID is provided or configured
     *
     * @return string The store ID
     */
    public function getStoreId(?string $storeId = null): string
    {
        $storeId ??= $this->getConfiguration()->getStoreId();

        if (null === $storeId) {
            throw new Exception('Store ID is required');
        }

        return trim($storeId);
    }
}
