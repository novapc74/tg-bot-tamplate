<?php

namespace App\Traits;

trait ModelTrait
{
    /**
     * fqcn - Fully Qualified Class Name
     * @param array $body
     * @param string $type
     * @param string $fqcn
     * @return object|null
     */
    public static function getModel(array $body, string $type, string $fqcn): ?object
    {
        static $counter = 0;
        $counter++;

        if ($counter > 100) {
            return null;
        }

        foreach ($body as $key => $value) {
            if ($type === $key) {
                return new $fqcn($value);
            } elseif (is_array($value)) {
                if ($result = self::getModel($value, $type, $fqcn)) {
                    return $result;
                }
            }
        }

        return null;
    }

    public static function getTextFromBody(array $body): ?string
    {
        static $counter = 0;
        $counter++;

        if ($counter > 100) {
            return null;
        }

        foreach ($body as $key => $value) {
            if ('text' === $key && is_string($value)) {
                return trim($value);
            } elseif (is_array($value)) {
                if ($result = self::getTextFromBody($value)) {
                    return $result;
                }
            }
        }

        return null;

    }

}
