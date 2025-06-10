<?php

// PHPStan-specific stub to handle Fiber generics
if (false) {
    /**
     * @template TStart
     * @template TResume  
     * @template TReturn
     * @template TSuspend
     */
    final class Fiber
    {
        /**
         * @param callable(): TReturn $callback
         */
        public function __construct(callable $callback) {}
        
        /**
         * @param TStart ...$args
         * @return TSuspend
         */
        public function start(mixed ...$args): mixed {}
        
        /**
         * @param TResume $value
         * @return TSuspend  
         */
        public function resume(mixed $value = null): mixed {}
        
        /**
         * @param Throwable $exception
         * @return TSuspend
         */
        public function throw(Throwable $exception): mixed {}
        
        /**
         * @return TReturn
         */
        public function getReturn(): mixed {}
        
        public function isStarted(): bool {}
        
        public function isSuspended(): bool {}
        
        public function isRunning(): bool {}
        
        public function isTerminated(): bool {}
        
        /**
         * @return static<TStart, TResume, TReturn, TSuspend>|null
         */
        public static function getCurrent(): ?self {}
        
        /**
         * @param TSuspend $value
         * @return TResume
         */
        public static function suspend(mixed $value = null): mixed {}
    }
}