<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use InvalidArgumentException;

final class TypeDefinition implements TypeDefinitionInterface
{
    use CollectionTrait;

    /**
     * @param string                               $type      The type of the object that this definition is for.
     * @param null|array<string, UsersetInterface> $relations An array of relation names to Userset definitions.
     * @param null|MetadataInterface               $metadata  An array whose keys are the name of the relation and whose value is the Metadata for that relation. It also holds information around the module name and source file if this model was constructed from a modular model.
     */
    public function __construct(
        private string $type,
        private ?array $relations = null,
        private ?MetadataInterface $metadata = null,
    ) {
        foreach ($relations as $relation => $userset) {
            if (! $userset instanceof UsersetInterface) {
                throw new InvalidArgumentException('Userset must implement UsersetInterface');
            }

            $this->relations[$relation] = $userset;
        }
    }

    public function getMetadata(): ?MetadataInterface
    {
        return $this->metadata;
    }

    public function getRelations(): ?array
    {
        return $this->relations;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function jsonSerialize(): array
    {
        $response = [
            'type' => $this->type,
        ];

        if ($this->getRelations()) {
            $response['relations'] = array_map(static fn (UsersetInterface $userset): array => $userset->jsonSerialize(), $this->getRelations());
        }

        if ($this->getMetadata()) {
            $response['metadata'] = $this->getMetadata()->jsonSerialize();
        }

        return $response;
    }

    public static function fromArray(array $data): self
    {
        $relations = [];

        foreach ($data['relations'] as $relation => $userset) {
            $relations[$relation] = Userset::fromArray($userset);
        }

        return new self(
            type: $data['type'],
            relations: $relations !== [] ? $relations : null,
            metadata: isset($data['metadata']) ? Metadata::fromArray($data['metadata']) : null,
        );
    }
}
