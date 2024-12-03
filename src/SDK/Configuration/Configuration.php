<?php

declare(strict_types=1);

namespace OpenFGA\SDK\Configuration;

abstract class Configuration extends ConfigurationInterface
{
    abstract public function __construct(
        private array $configuration,
    );

    abstract public function validate(): void;

    final public static function fromJson(
        string $json,
    ): self {
        return new self(
            json_decode($json, true),
        );
    }
}
