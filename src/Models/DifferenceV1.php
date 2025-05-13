<?php

declare(strict_types=1);

namespace OpenFGA\Models;

final class DifferenceV1 implements DifferenceV1Interface
{
    use ModelTrait;

    public function __construct(
        private UsersetInterface $base,
        private UsersetInterface $subtract,
    ) {
    }

    public function getBase(): UsersetInterface
    {
        return $this->base;
    }

    public function getSubtract(): UsersetInterface
    {
        return $this->subtract;
    }

    public function jsonSerialize(): array
    {
        return [
            'base' => $this->base->jsonSerialize(),
            'subtract' => $this->subtract->jsonSerialize(),
        ];
    }

    public static function fromArray(array $data): self
    {
        assert(isset($data['base']) && isset($data['subtract']));

        $base = $data['base'];
        $subtract = $data['subtract'];

        return new self(
            base: Userset::fromArray($base),
            subtract: Userset::fromArray($subtract),
        );
    }
}
