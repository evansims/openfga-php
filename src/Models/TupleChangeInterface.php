<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use DateTimeImmutable;
use OpenFGA\Models\Enums\TupleOperation;
use Override;

/**
 * Represents a change event for a relationship tuple in OpenFGA.
 *
 * Tuple changes capture the history of relationship modifications in the
 * authorization store. Each change records whether a tuple was written
 * (created) or deleted, along with the timestamp and the specific tuple
 * that was affected.
 *
 * These change events are essential for:
 * - Auditing relationship modifications
 * - Implementing consistency across distributed systems
 * - Debugging authorization issues
 * - Maintaining change history for compliance
 *
 * @see https://openfga.dev/docs/concepts#relationship-tuples OpenFGA Relationship Tuples
 * @see https://openfga.dev/api/service#/Relationship%20Tuples/ReadChanges Read Changes API
 */
interface TupleChangeInterface extends ModelInterface
{
    /**
     * Get the type of operation performed on the tuple.
     *
     * Operations indicate whether the tuple was written (created)
     * or deleted from the authorization store. This information
     * is crucial for understanding the nature of the change.
     *
     * @return TupleOperation The operation type (write or delete)
     */
    public function getOperation(): TupleOperation;

    /**
     * Get the timestamp when this tuple change occurred.
     *
     * Timestamps help track the chronological order of changes
     * and provide audit trail capabilities. They are essential
     * for understanding the sequence of relationship modifications.
     *
     * @return DateTimeImmutable The change timestamp
     */
    public function getTimestamp(): DateTimeImmutable;

    /**
     * Get the tuple key that was affected by this change.
     *
     * The tuple key identifies which specific relationship
     * was created or deleted, containing the user, relation,
     * object, and optional condition information.
     *
     * @return TupleKeyInterface The tuple key that was modified
     */
    public function getTupleKey(): TupleKeyInterface;

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function jsonSerialize(): array;
}
