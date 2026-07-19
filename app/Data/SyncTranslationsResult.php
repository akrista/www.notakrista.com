<?php

declare(strict_types=1);

namespace App\Data;

final class SyncTranslationsResult
{
    public int $created = 0;

    public int $updated = 0;

    public int $skipped = 0;

    public int $filesProcessed = 0;

    public function total(): int
    {
        return $this->created + $this->updated + $this->skipped;
    }

    public function isEmpty(): bool
    {
        return $this->total() === 0;
    }
}
