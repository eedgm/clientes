<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Receipt extends Model
{
    use HasFactory;
    use Searchable;
    use SoftDeletes;

    protected $fillable = [
        'number',
        'description',
        'client_id',
        'real_date',
        'charged',
        'reference_charged',
        'date_charged',
    ];

    protected $searchableFields = ['*'];

    protected $casts = [
        'real_date' => 'date',
        'charged' => 'boolean',
        'date_charged' => 'datetime',
    ];

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function payables()
    {
        return $this->hasMany(Payable::class);
    }
}
