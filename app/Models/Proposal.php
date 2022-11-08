<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Proposal extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = ['product_name', 'description', 'client_id'];

    protected $searchableFields = ['*'];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function versions()
    {
        return $this->hasMany(Version::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
