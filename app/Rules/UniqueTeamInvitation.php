<?php

declare(strict_types=1);

namespace App\Rules;

use App\Models\Team;
use App\Models\TeamInvitation;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

final readonly class UniqueTeamInvitation implements ValidationRule
{
    public function __construct(private Team $team)
    {
        //
    }

    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value)) {
            $fail(__('app.validation_email_string'));

            return;
        }

        $email = mb_strtolower($value);

        $isMember = $this->team->members()
            ->whereRaw('LOWER(email) = ?', [$email])
            ->exists();

        if ($isMember) {
            $fail(__('app.validation_member_exists'));

            return;
        }

        $hasPendingInvitation = TeamInvitation::query()->where('team_id', $this->team->id)
            ->whereRaw('LOWER(email) = ?', [$email])
            ->whereNull('accepted_at')
            ->where(function ($query): void {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->exists();

        if ($hasPendingInvitation) {
            $fail(__('app.validation_invitation_pending'));
        }
    }
}
