<?php

declare(strict_types=1);

namespace OpenFGA\Models;

/**
 * @psalm-type UsersetUserShape = array{type: string, id: string, relation: string}
 */
interface UsersetUserInterface extends ModelInterface
{
    public function getId(): string;

    public function getRelation(): string;

    public function getType(): string;

    /**
     * @return UsersetUserShape
     */
    public function jsonSerialize(): array;
}
