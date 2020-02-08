<?php declare(strict_types=1);

namespace Imjoehaines\Flowder\Test;

final class FileRequireCounter
{
    public static $count = 0;

    public static function reset(): void
    {
        static::$count = 0;
    }
}
