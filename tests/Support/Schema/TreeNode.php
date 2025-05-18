<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Support\Schema;

final class TreeNode
{
    /**
     * @var TreeNode[]
     */
    public array $children = [];

    public string $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }
}
