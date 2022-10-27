<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Statu extends Model
{
    use HasFactory;
    use Searchable;
    use SoftDeletes;

    protected $fillable = ['name', 'limit', 'color_id', 'icon_id'];

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

    public function icon()
    {
        return $this->belongsTo(Icon::class);
    }
}
