<?php

declare(strict_types=1);

namespace Imjoehaines\Flowder\Fresher;

interface FresherInterface
{
    /**
     * Check the minty freshness of a table.
     * (i.e. has the table been modified since it was last flowded).
     *
     * @param string $table
     * @return boolean
     */
    public function needsFreshening($table): boolean;

    /**
     * Mark a table as fresh.
     *
     * @param string $table
     * @return void
     */
    public function markAsFresh($table): void;
}
