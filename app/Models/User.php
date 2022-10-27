<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use App\Models\Scopes\Searchable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Jetstream\HasProfilePhoto;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasRoles;
    use Notifiable;
    use HasFactory;
    use Searchable;
    use HasApiTokens;
    use HasProfilePhoto;
    use TwoFactorAuthenticatable;

    protected $fillable = ['name', 'email', 'password'];

    protected $searchableFields = ['*'];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'two_factor_confirmed_at' => 'datetime',
    ];

    public function versions()
    {
        return $this->hasMany(Version::class);
    }

    public function people()
    {
        return $this->hasMany(Person::class);
    }

    public function developers()
    {
        return $this->hasMany(Developer::class);
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class);
    }

    public function attaches()
    {
        return $this->hasMany(Attach::class);
    }

    public function clients()
    {
        return $this->belongsToMany(Client::class);
    }

    public function isSuperAdmin()
    {
        return $this->hasRole('super-admin');
    }
}
