<?php

declare(strict_types=1);

namespace OpenFGA\API\Endpoints\Traits;

trait AuthorizationModelIdentifiers
{
    final public function getModelId(): ?string
    {
        if ($this->modelId === null) {
            return $this->client->getConfiguration()->getAuthorizationModelId();
        }

        return $this->modelId;
    }

    final public function setModelId(?string $modelId): self
    {
        $this->modelId = $modelId;
        return $this;
    }
}
