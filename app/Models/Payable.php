<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payable extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = [
        'name',
        'date',
        'cost',
        'margin',
        'total',
        'product_id',
        'supplier_id',
        'supplier_id_reference',
        'periodicity',
        'receipt_id',
    ];

    protected $searchableFields = ['*'];

    protected $casts = [
        'date' => 'date',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function receipt()
    {
        return $this->belongsTo(Receipt::class);
    }
}
