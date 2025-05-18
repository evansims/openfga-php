<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Support\Schema;

final class Post
{
    /**
     * @var array<Tag>
     */
    public array $tags = [];

    public string $title;
}
