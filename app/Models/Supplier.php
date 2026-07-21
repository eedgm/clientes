<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = ['name', 'url', 'description', 'login', 'password'];

    protected $searchableFields = ['*'];

    protected $hidden = ['password'];

    public function payables()
    {
        return $this->hasMany(Payable::class);
    }
}
