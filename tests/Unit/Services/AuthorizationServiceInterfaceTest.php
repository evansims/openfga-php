<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Services;

use OpenFGA\Services\AuthorizationServiceInterface;
use ReflectionClass;

describe('AuthorizationServiceInterface', function (): void {
    it('exists and is an interface', function (): void {
        expect(interface_exists(AuthorizationServiceInterface::class))->toBeTrue();
        expect(new ReflectionClass(AuthorizationServiceInterface::class))->toBeInstanceOf(ReflectionClass::class);
    });

    it('defines the check method', function (): void {
        $reflection = new ReflectionClass(AuthorizationServiceInterface::class);
        expect($reflection->hasMethod('check'))->toBeTrue();

        $method = $reflection->getMethod('check');
        expect($method->isPublic())->toBeTrue();
        expect($method->getNumberOfParameters())->toBe(7);
    });

    it('defines the expand method', function (): void {
        $reflection = new ReflectionClass(AuthorizationServiceInterface::class);
        expect($reflection->hasMethod('expand'))->toBeTrue();

        $method = $reflection->getMethod('expand');
        expect($method->isPublic())->toBeTrue();
        expect($method->getNumberOfParameters())->toBe(5);
    });

    it('defines the listObjects method', function (): void {
        $reflection = new ReflectionClass(AuthorizationServiceInterface::class);
        expect($reflection->hasMethod('listObjects'))->toBeTrue();

        $method = $reflection->getMethod('listObjects');
        expect($method->isPublic())->toBeTrue();
        expect($method->getNumberOfParameters())->toBe(8);
    });

    it('defines the listUsers method', function (): void {
        $reflection = new ReflectionClass(AuthorizationServiceInterface::class);
        expect($reflection->hasMethod('listUsers'))->toBeTrue();

        $method = $reflection->getMethod('listUsers');
        expect($method->isPublic())->toBeTrue();
        expect($method->getNumberOfParameters())->toBe(8);
    });

    it('defines the batchCheck method', function (): void {
        $reflection = new ReflectionClass(AuthorizationServiceInterface::class);
        expect($reflection->hasMethod('batchCheck'))->toBeTrue();

        $method = $reflection->getMethod('batchCheck');
        expect($method->isPublic())->toBeTrue();
        expect($method->getNumberOfParameters())->toBe(3);
    });

    it('has all methods returning Result pattern', function (): void {
        $reflection = new ReflectionClass(AuthorizationServiceInterface::class);
        $methods = ['check', 'expand', 'listObjects', 'listUsers', 'batchCheck'];

        foreach ($methods as $methodName) {
            $method = $reflection->getMethod($methodName);
            $returnType = $method->getReturnType();

            expect($returnType)->not->toBeNull();
            expect($returnType?->__toString())->toBe('OpenFGA\Results\FailureInterface|OpenFGA\Results\SuccessInterface');
        }
    });
});
