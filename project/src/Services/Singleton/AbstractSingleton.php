<?php

namespace App\Services\Singleton;

use Exception;

class AbstractSingleton
{
    protected static array $instances = [];

    protected function __construct()
    {
    }

    protected function __clone(): void
    {
    }

    /**
     * @throws Exception
     */
    public function __wakeup(): void
    {
        throw new Exception("Cannot unserialize singleton");
    }

    public static function getInstance(): static
    {
        $subclass = static::class;
        if (!isset(self::$instances[$subclass])) {
            self::$instances[$subclass] = new static();
        }

        return self::$instances[$subclass];
    }
}
