<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Developer extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = ['user_id', 'rol_id'];

    protected $searchableFields = ['*'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function rol()
    {
        return $this->belongsTo(Rol::class);
    }

    public function tasks()
    {
        return $this->belongsToMany(Task::class);
    }

    public function tickets()
    {
        return $this->belongsToMany(Ticket::class);
    }

    public function skills()
    {
        return $this->belongsToMany(Skill::class);
    }
}
