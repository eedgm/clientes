<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Color extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = ['name', 'code'];

    protected $searchableFields = ['*'];

    public function status()
    {
        return $this->hasMany(Statu::class);
    }

    public function priorities()
    {
        return $this->hasMany(Priority::class);
    }
}
