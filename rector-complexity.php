<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\SetList;
use Rector\CodeQuality\Rector\If_\SimplifyIfElseToTernaryRector;
use Rector\CodeQuality\Rector\If_\SimplifyIfReturnBoolRector;
use Rector\DeadCode\Rector\ClassMethod\RemoveUnusedPrivateMethodRector;
use Rector\DeadCode\Rector\Property\RemoveUnusedPrivatePropertyRector;
use Rector\EarlyReturn\Rector\If_\RemoveAlwaysElseRector;

/**
 * Rector configuration focused on reducing code complexity
 * 
 * These rules help automatically refactor complex code patterns
 * to simpler, more maintainable alternatives.
 */
return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        __DIR__ . '/src',
    ]);

    // Skip test files and vendor
    $rectorConfig->skip([
        __DIR__ . '/vendor',
        __DIR__ . '/tests',
    ]);

    // Use predefined rule sets for code quality and early returns
    $rectorConfig->sets([
        SetList::CODE_QUALITY,
        SetList::EARLY_RETURN,
        SetList::DEAD_CODE,
    ]);

    // Additional specific rules for complexity reduction
    $rectorConfig->rules([
        // Simplify if/else to ternary where appropriate
        SimplifyIfElseToTernaryRector::class,
        
        // Simplify if return bool patterns
        SimplifyIfReturnBoolRector::class,
        
        // Remove unnecessary else statements
        RemoveAlwaysElseRector::class,
        
        // Remove unused private methods
        RemoveUnusedPrivateMethodRector::class,
        
        // Remove unused private properties
        RemoveUnusedPrivatePropertyRector::class,
    ]);

    // Configure import options
    $rectorConfig->importNames();
    $rectorConfig->removeUnusedImports();
};