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
        'manual_value',
        'version_id'
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

    public function version()
    {
        return $this->belongsTo(Version::class);
    }

    public function totalTickets()
    {
        return $this->hasMany(Ticket::class)
            ->selectRaw('SUM(total) as total')
            ->groupBy('receipt_id');
    }

    public function totalPayables()
    {
        return $this->hasMany(Payable::class)
            ->selectRaw('SUM(total) as total')
            ->groupBy('receipt_id');
    }
}
