<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Person extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = [
        'description',
        'phone',
        'skype',
        'client_id',
        'rol_id',
        'photo',
        'user_id',
    ];

    protected $searchableFields = ['*'];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function rol()
    {
        return $this->belongsTo(Rol::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function versions()
    {
        return $this->belongsToMany(Version::class);
    }
}
