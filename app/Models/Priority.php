<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Priority extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = ['name', 'color_id'];

    protected $searchableFields = ['*'];

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function color()
    {
        return $this->belongsTo(Color::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
