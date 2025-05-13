<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\HasTenants;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Filament\Models\Contracts\HasAvatar;

class User extends Authenticatable implements HasTenants, HasAvatar
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
        ];
    }

    public function teams():BelongsToMany // to access the teams of the user
    {
        return $this->belongsToMany(Team::class);
    }

    public function getTenants(Panel $panel):Collection // to access the tenants of the user
    {
        return $this->teams;
    }

    public function canAccessTenant(Model $tenant):bool // to check if the user can access the tenant
    {
        return $this->teams()->whereKey($tenant)->exists();
    }

    public function isAdmin():bool
    {
        return $this->email === 'momen@gmail.com';
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return asset('images/avatars/default.png');
    }
}
