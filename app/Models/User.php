<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    protected $fillable = ['name', 'phone', 'email', 'password', 'legacy_pw', 'role', 'city', 'plan', 'status'];
    protected $hidden = ['password', 'legacy_pw', 'remember_token', 'two_factor_secret', 'two_factor_recovery_codes'];
    protected function casts(): array
    {
        return [
            'password' => 'hashed', 'last_login_at' => 'datetime', 'pw_changed_at' => 'datetime',
            'two_factor_secret' => 'encrypted', 'two_factor_recovery_codes' => 'encrypted:array', 'two_factor_confirmed_at' => 'datetime',
        ];
    }

    public function isAdmin(): bool { return $this->role === 'admin'; }
    public function hasTwoFactor(): bool { return $this->two_factor_secret && $this->two_factor_confirmed_at; }
    public function isBlocked(): bool { return $this->status === 'blocked'; }
    public function listings(): HasMany { return $this->hasMany(Listing::class, 'owner_id'); }
    public function orders(): HasMany { return $this->hasMany(Order::class, 'buyer_id'); }
    public function doctor(): HasOne { return $this->hasOne(Doctor::class); }
    public function favorites(): BelongsToMany { return $this->belongsToMany(Listing::class, 'favorites')->withTimestamps(); }
    public function adLimit(): int { return config('janwar.plans')[$this->plan] ?? 3; }
}