<?php

namespace App\Traits;

trait HumanSizeCounterTrait
{
    private const string TIME_PREFIX = 'Время выполнения скрипта - ';

    private function getHumanSize(string $data): string
    {
        $size = strlen($data);
        $units = ['B', 'kB', 'MB', 'GB'];

        foreach ($units as $unit) {
            if ($size < 1024) {
                break;
            }
            $size /= 1024;
        }

        return round($size, 2) . $unit;
    }

    private function formatTime(float $seconds): string
    {
        if ($seconds < 1) {
            /** return 00:00:00.48 */
            return self::TIME_PREFIX . '00:00:' . sprintf('%05.2f', $seconds);
        }

        if ($seconds < 60) {
            /** return 00:00:07.39 */
            return self::TIME_PREFIX . '00:00:' . sprintf('%05.2f', $seconds);
        }

        if ($seconds < 3600) {
            $minutes = floor($seconds / 60);
            $seconds = $seconds % 60;
            /** return 00:27:09 */
            return self::TIME_PREFIX . '00:' . sprintf('%02d:%02d', $minutes, $seconds);
        }

        $hours = floor($seconds / 3600);
        $remaining = $seconds % 3600;
        $minutes = floor($remaining / 60);
        $seconds = $remaining % 60;

        return self::TIME_PREFIX . sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    }


    public function getExecutionTime(float $startScriptTime): string
    {
        return $this->formatTime(microtime(true) - $startScriptTime);
    }

    public function getScriptStartTime(): float
    {
        return microtime(true);
    }

    public static function humanizeUsageMemory(bool $realUsage = false): string
    {
        $memoryUsage = memory_get_peak_usage($realUsage);

        return match (true) {
            $memoryUsage < 1024 => "{$memoryUsage} bytes",
            $memoryUsage < 1048576 => round($memoryUsage / 1024) . " KB",
            default => round($memoryUsage / 1048576, 2) . " MB",
        };
    }
}
