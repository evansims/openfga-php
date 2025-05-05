<?php

declare(strict_types=1);

namespace OpenFGA;

use OpenFGA\Credentials\CredentialInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\{RequestFactoryInterface, ResponseFactoryInterface, StreamFactoryInterface};

final class Configuration implements ConfigurationInterface
{
    public function __construct(
        public ?string $apiUrl = null,
        public ?string $storeId = null,
        public ?string $authorizationModelId = null,
        public ?bool $useOkta = false,
        public ?CredentialInterface $credential = null,
        public ?ClientInterface $httpClient = null,
        public ?ResponseFactoryInterface $httpFactory = null,
        public ?StreamFactoryInterface $httpStreamFactory = null,
        public ?RequestFactoryInterface $httpRequestFactory = null,
    ) {
    }
}
