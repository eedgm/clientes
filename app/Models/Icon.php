<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Icon extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = ['name', 'icon'];

    protected $searchableFields = ['*'];

    public function status()
    {
        return $this->hasMany(Statu::class);
    }
}
