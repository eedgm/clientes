<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Developer extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = ['user_id', 'rol_id', 'cost_per_hour'];

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
        return $this->belongsToMany(Task::class)->withPivot('comments', 'assignations', 'gain', 'hours');
    }

    public function tickets()
    {
        return $this->belongsToMany(Ticket::class);
    }

    public function skills()
    {
        return $this->belongsToMany(Skill::class);
    }

    public function versions()
    {
        return $this->belongsToMany(Version::class)
            ->withPivot('cost_per_hour')
            ->withTimestamps();
    }
}
