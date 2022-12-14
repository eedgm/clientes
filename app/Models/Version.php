<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Version extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = [
        'proposal_id',
        'attachment',
        'user_id',
        'total',
        'time',
        'cost_per_hour',
        'hour_per_day',
        'months_to_pay',
        'unexpected',
        'company_gain',
        'bank_tax',
        'first_payment',
        'hours'
    ];

    protected $searchableFields = ['*'];

    public function proposal()
    {
        return $this->belongsTo(Proposal::class);
    }

    public function client()
    {
        return $this->belongsTo(User::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function people()
    {
        return $this->belongsToMany(Person::class);
    }

    public function receipts()
    {
        return $this->hasMany(Receipt::class);
    }
}
