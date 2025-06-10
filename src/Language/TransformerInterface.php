<?php

declare(strict_types=1);

namespace OpenFGA\Language;

use InvalidArgumentException;
use OpenFGA\Exceptions\{ClientThrowable, SerializationException};
use OpenFGA\Models\AuthorizationModelInterface;
use OpenFGA\Schemas\SchemaValidator;

/**
 * OpenFGA DSL Transformer Interface for authorization model conversions.
 *
 * This interface defines methods for converting between OpenFGA's Domain Specific
 * Language (DSL) format and structured authorization model objects. The DSL provides
 * a human-readable way to define authorization relationships and permissions that
 * can be easily reviewed, edited, and version controlled.
 *
 * The transformer supports bidirectional conversion, allowing models to be defined
 * in DSL format and then converted to API objects, or existing models to be exported
 * back to DSL format for documentation and review purposes.
 *
 * @see https://openfga.dev/docs/authorization-concepts OpenFGA authorization concepts
 * @see AuthorizationModelInterface For authorization model structure
 */
interface TransformerInterface
{
    /**
     * Parse a DSL string into an authorization model.
     *
     * This method converts a human-readable DSL (Domain Specific Language) string into a structured
     * authorization model object that can be used with the OpenFGA API. The DSL provides an intuitive
     * way to define authorization relationships and permissions using familiar syntax.
     *
     * @param string          $dsl       The DSL string containing the authorization model definition
     * @param SchemaValidator $validator Schema validator for validating the parsed model structure
     *
     * @throws InvalidArgumentException If the DSL input is invalid
     * @throws ClientThrowable          If the DSL syntax is invalid or cannot be parsed
     *
     * @return AuthorizationModelInterface The parsed authorization model ready for API operations
     */
    public static function fromDsl(string $dsl, SchemaValidator $validator): AuthorizationModelInterface;

    /**
     * Convert an authorization model to its DSL string representation.
     *
     * This method transforms a structured authorization model object back into its human-readable
     * DSL format, making it easy to review, edit, or share authorization model definitions.
     * The output can be saved to files, version controlled, or used for documentation purposes.
     *
     * @param AuthorizationModelInterface $model The authorization model to convert to DSL format
     *
     * @throws SerializationException If the model structure is invalid or cannot be converted
     *
     * @return string The DSL string representation of the authorization model
     */
    public static function toDsl(AuthorizationModelInterface $model): string;
}
