<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Client extends Model
{
    use HasFactory;
    use Searchable;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'logo',
        'cost_per_hour',
        'owner',
        'email_contact',
    ];

    protected $searchableFields = ['*'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function people()
    {
        return $this->hasMany(Person::class);
    }

    public function proposals()
    {
        return $this->hasMany(Proposal::class);
    }

    public function receipts()
    {
        return $this->hasMany(Receipt::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
