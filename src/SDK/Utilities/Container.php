<?php
namespace OpenFGA\SDK\Utilities;

use OpenFGA\SDK\Exceptions\ContainerEntryNotFoundException;
use Psr\Container\ContainerInterface;

final class Container implements ContainerInterface
{
    /**
     * @param array<string,mixed> $services
     */
    public function __construct(
        private array $services = []
    )
    {
    }

    public function get(string $id): mixed
    {
        if ($this->has($id)) {
            return $this->services[$id];
        }

        throw new ContainerEntryNotFoundException();
    }

    public function set(string $id, mixed $data): void
    {
        $this->services[$id] = $data;
    }

    public function has(string $id): bool
    {
        if (isset($this->services[$id])) {
            return true;
        }

        return false;
    }
}
