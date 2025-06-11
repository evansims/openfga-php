<?php

declare(strict_types=1);

use OpenFGA\ServiceNotFoundException;

it('constructs with service name', function (): void {
    $serviceName = 'TestService';
    $exception = new ServiceNotFoundException($serviceName);

    expect($exception)->toBeInstanceOf(ServiceNotFoundException::class);
    expect($exception)->toBeInstanceOf(InvalidArgumentException::class);
    expect($exception->getMessage())->toBe("Service \"{$serviceName}\" not found. Please ensure the service is registered with the configuration provider.");
});

it('has correct exception message format', function (): void {
    $serviceName = 'SomeComplexServiceName';
    $exception = new ServiceNotFoundException($serviceName);

    expect($exception->getMessage())->toBe('Service "SomeComplexServiceName" not found. Please ensure the service is registered with the configuration provider.');
});

it('handles empty service name', function (): void {
    $exception = new ServiceNotFoundException('');

    expect($exception->getMessage())->toBe('Service "" not found. Please ensure the service is registered with the configuration provider.');
});

it('handles service name with special characters', function (): void {
    $serviceName = 'Service\\With\\Namespace\\And-Dashes_And_Underscores';
    $exception = new ServiceNotFoundException($serviceName);

    expect($exception->getMessage())->toBe('Service "Service\\With\\Namespace\\And-Dashes_And_Underscores" not found. Please ensure the service is registered with the configuration provider.');
});
