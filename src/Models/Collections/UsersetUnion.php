<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use OpenFGA\Models\{ModelInterface, Userset, UsersetInterface};
use Override;

/**
 * @extends IndexedCollection<\OpenFGA\Models\UsersetInterface>
 */
final class UsersetUnion extends IndexedCollection implements UsersetUnionInterface
{
    /**
     * @phpstan-var class-string<UsersetInterface>
     *
     * @psalm-var class-string<ModelInterface>
     *
     * @var class-string<ModelInterface>
     */
    protected static string $itemType = Userset::class;

    /**
     * @return array{
     *     child: array<int|string, mixed>
     * }
     */
    #[Override]
    public function jsonSerialize(): array
    {
        return [
            'child' => parent::jsonSerialize(),
        ];
    }
}
