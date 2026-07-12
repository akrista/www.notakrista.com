<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\HasTeams;
use Database\Factories\UserFactory;
use Filament\Auth\MultiFactor\App\Concerns\InteractsWithAppAuthentication;
use Filament\Auth\MultiFactor\App\Contracts\HasAppAuthentication;
use Filament\Auth\MultiFactor\Email\Concerns\InteractsWithEmailAuthentication;
use Filament\Auth\MultiFactor\Email\Contracts\HasEmailAuthentication;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Models\Contracts\HasTenants;
use Filament\Panel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravel\Fortify\Contracts\PasskeyUser;
use Laravel\Fortify\PasskeyAuthenticatable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Mattiverse\Userstamps\Traits\Userstamps;
use Override;
use SensitiveParameter;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property int|string|null $id
 * @property-read string $name
 * @property ?string $filament_authentication_secret
 * @property ?array<string> $filament_authentication_recovery_codes
 *
 * @mixin Model
 */
#[Fillable([
    'username',
    'firstname',
    'lastname',
    'email',
    'password',
    'current_team_id',
    'avatar_url',
])]
#[Hidden([
    'password',
    'two_factor_secret',
    'two_factor_recovery_codes',
    'filament_authentication_secret',
    'filament_authentication_recovery_codes',
    'remember_token',
])]
final class User extends Authenticatable implements FilamentUser, HasAppAuthentication, HasAvatar, HasEmailAuthentication, HasTenants, MustVerifyEmail, PasskeyUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory;

    use HasRoles, HasTeams {
        HasTeams::teams insteadof HasRoles;
        HasRoles::teams as spatieTeams;
    }
    use HasUuids;
    use InteractsWithAppAuthentication;
    use InteractsWithEmailAuthentication;
    use Notifiable;
    use PasskeyAuthenticatable;
    use SoftDeletes;
    use TwoFactorAuthenticatable;
    use Userstamps;

    /**
     * The model's default values for attributes.
     *
     * @var array<string, mixed>
     */
    #[Override]
    protected $attributes = [
        'deleted_by' => null,
        'created_by' => null,
        'updated_by' => null,
    ];

    public function initials(): string
    {
        $initials = Str::initials($this->firstname . ' ' . $this->lastname, true);

        return Str::length($initials) > 1
            ? Str::substr($initials, 0, 1) . Str::substr($initials, -1)
            : $initials;
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    /**
     * @return Collection<int, Team>
     */
    public function getTenants(Panel $panel): Collection
    {
        return $this->teams;
    }

    public function canAccessTenant(Model $tenant): bool
    {
        return $tenant instanceof Team && $this->belongsToTeam($tenant);
    }

    public function getFilamentName(): string
    {
        $firstName = $this->firstname ?? '';
        $lastName = $this->lastname ?? '';

        $fullName = mb_trim($firstName . ' ' . $lastName);

        if ($fullName !== '') {
            return $fullName;
        }

        if (! empty($this->username)) {
            return (string) $this->username;
        }

        if (! empty($this->email)) {
            return (string) $this->email;
        }

        $key = $this->getKey();

        return is_string($key) ? $key : '';
    }

    public function getAvatarUrl(): ?string
    {
        if (empty($this->avatar_url)) {
            return null;
        }

        if (filter_var($this->avatar_url, FILTER_VALIDATE_URL)) {
            return $this->avatar_url;
        }

        return Storage::disk('public')->url($this->avatar_url);
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->getAvatarUrl();
    }

    public function getAppAuthenticationSecret(): ?string
    {
        return $this->filament_authentication_secret;
    }

    public function saveAppAuthenticationSecret(?string $secret): void
    {
        $this->filament_authentication_secret = $secret;
        $this->save();
    }

    public function getAppAuthenticationHolderName(): string
    {
        return $this->email;
    }

    /**
     * @return ?array<string>
     */
    public function getAppAuthenticationRecoveryCodes(): ?array
    {
        return $this->filament_authentication_recovery_codes;
    }

    /**
     * @param  ?array<string>  $codes
     */
    public function saveAppAuthenticationRecoveryCodes(#[SensitiveParameter] ?array $codes): void
    {
        $this->filament_authentication_recovery_codes = $codes;
        $this->save();
    }

    public function hasEmailAuthentication(): bool
    {
        return $this->has_email_authentication;
    }

    public function toggleEmailAuthentication(bool $condition): void
    {
        $this->has_email_authentication = $condition;
        $this->save();
    }

    /**
     * @return Attribute<string, never>
     */
    protected function name(): Attribute
    {
        return Attribute::make(get: fn (): string => sprintf('%s %s', $this->firstname, $this->lastname));
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
            'filament_authentication_secret' => 'encrypted',
            'filament_authentication_recovery_codes' => 'encrypted:array',
        ];
    }
}
