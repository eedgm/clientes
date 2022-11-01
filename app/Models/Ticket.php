<?php

namespace App\Models;

use Illuminate\Support\Str;
use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Ticket extends Model
{
    use HasFactory;
    use Searchable;
    use SoftDeletes;

    protected $fillable = [
        'description',
        'statu_id',
        'priority_id',
        'hours',
        'total',
        'finished_ticket',
        'comments',
        'product_id',
        'receipt_id',
        'person_id',
    ];

    protected $searchableFields = ['*'];

    protected $casts = [
        'finished_ticket' => 'date',
    ];

    public function statu()
    {
        return $this->belongsTo(Statu::class);
    }

    public function priority()
    {
        return $this->belongsTo(Priority::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function person()
    {
        return $this->belongsTo(Person::class);
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class);
    }

    public function receipt()
    {
        return $this->belongsTo(Receipt::class);
    }

    public function developers()
    {
        return $this->belongsToMany(Developer::class);
    }

    public function excerpt()
    {
        return Str::limit($this->description, 50);
    }
}
