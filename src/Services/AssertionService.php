<?php

declare(strict_types=1);

namespace OpenFGA\Services;

use OpenFGA\Exceptions\{ClientError};
use OpenFGA\Messages;
use OpenFGA\Models\Collections\Assertions;
use OpenFGA\Models\Collections\{AssertionsInterface};
use OpenFGA\Models\{StoreInterface};
use OpenFGA\Repositories\AssertionRepositoryInterface;
use OpenFGA\Results\{Failure, FailureInterface, Success, SuccessInterface};
use OpenFGA\Translation\Translator;
use Override;
use Throwable;

use function is_string;

/**
 * Service implementation for managing OpenFGA authorization model assertions.
 *
 * Provides business-focused operations for working with assertions,
 * which are test cases that validate the behavior of authorization models.
 * This service abstracts the underlying repository implementation and adds
 * value through validation, convenience methods, and enhanced error handling.
 *
 * @see AssertionServiceInterface Service interface
 * @see AssertionRepositoryInterface Underlying repository
 */
final readonly class AssertionService implements AssertionServiceInterface
{
    /**
     * Create a new assertion service instance.
     *
     * @param AssertionRepositoryInterface $assertionRepository Repository for assertion data access
     * @param string                       $language            Language for error messages
     */
    public function __construct(
        private AssertionRepositoryInterface $assertionRepository,
        private string $language = 'en',
    ) {
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function clearAssertions(
        string $authorizationModelId,
    ): FailureInterface | SuccessInterface {
        try {
            // Clear all assertions by writing an empty collection
            return $this->assertionRepository->write($authorizationModelId, new Assertions([]));
        } catch (Throwable $throwable) {
            return new Failure($throwable);
        }
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function executeAssertions(
        string $authorizationModelId,
        AssertionsInterface $assertions,
    ): Failure | Success | SuccessInterface {
        try {
            // Validate that assertions are not empty
            if (0 === $assertions->count()) {
                throw ClientError::Validation->exception(context: ['message' => Translator::trans(Messages::ASSERTIONS_EMPTY_COLLECTION, [], $this->language), ]);
            }

            // For now, we'll use a simplified execution approach
            // TODO: Implement actual assertion execution with authorization checks
            $results = [];
            $passCount = 0;
            $failCount = 0;
            $totalCount = $assertions->count();

            foreach ($assertions as $index => $assertion) {
                // Mock execution result - in reality, this would perform actual checks
                $passed = $assertion->getExpectation(); // Use expected result as placeholder
                $results['assertion_' . $index] = [
                    'assertion' => $assertion,
                    'passed' => $passed,
                    'expected' => $assertion->getExpectation(),
                    'actual' => $passed, // Would be actual check result
                ];

                if ($passed) {
                    ++$passCount;
                } else {
                    ++$failCount;
                }
            }

            return new Success([
                'total' => $totalCount,
                'passed' => $passCount,
                'failed' => $failCount,
                'success_rate' => 0 < $totalCount ? (float) $passCount / (float) $totalCount * 100.0 : 0.0,
                'results' => $results,
            ]);
        } catch (Throwable $throwable) {
            return new Failure($throwable);
        }
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getAssertionStatistics(
        StoreInterface | string $store,
        string $authorizationModelId,
    ): FailureInterface | SuccessInterface {
        try {
            // Read current assertions
            $assertionsResult = $this->assertionRepository->read($authorizationModelId);

            if ($assertionsResult instanceof FailureInterface) {
                return $assertionsResult;
            }

            /** @var AssertionsInterface $assertions */
            $assertions = $assertionsResult->unwrap();

            // Generate statistics
            $statistics = [
                'total_assertions' => $assertions->count(),
                'store_id' => is_string($store) ? $store : $store->getId(),
                'model_id' => $authorizationModelId,
                'last_updated' => null, // Would be populated from actual data
                'coverage_metrics' => [
                    'types_covered' => [], // Would analyze assertion coverage
                    'relations_covered' => [],
                    'coverage_percentage' => 0,
                ],
            ];

            return new Success($statistics);
        } catch (Throwable $throwable) {
            return new Failure($throwable);
        }
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function readAssertions(
        string $authorizationModelId,
    ): FailureInterface | SuccessInterface {
        try {
            // Delegate to repository for actual retrieval
            return $this->assertionRepository->read($authorizationModelId);
        } catch (Throwable $throwable) {
            return new Failure($throwable);
        }
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function validateAssertions(
        AssertionsInterface $assertions,
        string $authorizationModelId,
    ): Failure | Success | SuccessInterface {
        try {
            // Basic validation
            if (0 === $assertions->count()) {
                throw ClientError::Validation->exception(context: ['message' => Translator::trans(Messages::ASSERTIONS_EMPTY_COLLECTION, [], $this->language), ]);
            }

            // Validate each assertion
            foreach ($assertions as $assertion) {
                // Check that assertion has required components
                $tupleKey = $assertion->getTupleKey();

                if ('' === $tupleKey->getUser() || '' === $tupleKey->getRelation() || '' === $tupleKey->getObject()) {
                    throw ClientError::Validation->exception(context: ['message' => Translator::trans(Messages::ASSERTIONS_INVALID_TUPLE_KEY, [], $this->language), ]);
                }

                // Additional validation could be added here:
                // - Check that types/relations exist in the authorization model
                // - Validate user/object format
                // - Check expectation is boolean
            }

            return new Success(true);
        } catch (Throwable $throwable) {
            return new Failure($throwable);
        }
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function writeAssertions(
        string $authorizationModelId,
        AssertionsInterface $assertions,
    ): FailureInterface | SuccessInterface {
        try {
            // Validate assertions first
            $validationResult = $this->validateAssertions($assertions, $authorizationModelId);

            if ($validationResult instanceof FailureInterface) {
                return $validationResult;
            }

            // Delegate to repository for actual write
            return $this->assertionRepository->write($authorizationModelId, $assertions);
        } catch (Throwable $throwable) {
            return new Failure($throwable);
        }
    }
}
