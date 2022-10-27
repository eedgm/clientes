<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = ['name', 'url', 'description', 'client_id', 'RUC', 'DV', 'direction'];

    protected $searchableFields = ['*'];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function payables()
    {
        return $this->hasMany(Payable::class);
    }
}
