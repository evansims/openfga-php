<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use InvalidArgumentException;
use OpenFGA\Exceptions\{ClientError, ClientThrowable};
use OpenFGA\Messages;
use OpenFGA\Schema\{Schema, SchemaInterface, SchemaProperty};
use OpenFGA\Translation\Translator;
use Override;
use ReflectionException;

/**
 * Represents a wildcard that matches all users of a specific type.
 *
 * In authorization models, you sometimes want to grant permissions to all
 * users of a certain type rather than specific individuals. TypedWildcard
 * lets you specify "all users of type X" in your authorization rules.
 *
 * For example, you might want to grant read access to "all employees" or
 * "all customers" without having to list each individual user.
 */
final class TypedWildcard implements TypedWildcardInterface
{
    public const string OPENAPI_MODEL = 'TypedWildcard';

    private static ?SchemaInterface $schema = null;

    /**
     * @param string $type The type name for the wildcard (will be normalized to lowercase)
     *
     * @throws ClientThrowable          If the type is empty after normalization
     * @throws InvalidArgumentException If message translation parameters are invalid
     * @throws ReflectionException      If exception location capture fails
     */
    public function __construct(
        private string $type,
    ) {
        $type = strtolower(trim($type));

        if ('' === $type) {
            throw ClientError::Validation->exception(context: ['message' => Translator::trans(Messages::MODEL_TYPED_WILDCARD_TYPE_EMPTY)]);
        }

        $this->type = $type;
    }

    /**
     * Get the string representation of this typed wildcard.
     *
     * Returns the type name that this wildcard represents, which is used
     * in string contexts and for display purposes.
     *
     * @return string The type name for this wildcard
     */
    public function __toString(): string
    {
        return $this->type;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public static function schema(): SchemaInterface
    {
        return self::$schema ??= new Schema(
            className: self::class,
            properties: [
                new SchemaProperty(name: 'type', type: 'string', required: true),
            ],
        );
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function jsonSerialize(): array
    {
        return [
            'type' => $this->type,
        ];
    }
}
