<?php

declare(strict_types=1);

namespace App\Console\Commands\Upgrade;

enum FileStatus: string
{
    /** File does not exist locally — safe to add automatically. */
    case New = 'new';

    /** File exists and is byte-identical to upstream — nothing to do. */
    case AlreadyPresent = 'already-present';

    /** File exists locally but content differs from upstream — requires user decision. */
    case Differs = 'differs';

    /** File exists locally but upstream has removed it — surface to user. */
    case DeletedUpstream = 'deleted-upstream';

    /**
     * Human-readable label for display in CLI output.
     */
    public function label(): string
    {
        return match ($this) {
            self::New => 'new',
            self::AlreadyPresent => 'already present',
            self::Differs => 'differs',
            self::DeletedUpstream => 'deleted upstream',
        };
    }

    /**
     * Whether this status requires interactive user input.
     */
    public function requiresUserDecision(): bool
    {
        return match ($this) {
            self::Differs, self::DeletedUpstream => true,
            default => false,
        };
    }
}
