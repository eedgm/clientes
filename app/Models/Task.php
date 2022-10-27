<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Task extends Model
{
    use HasFactory;
    use Searchable;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'hours',
        'statu_id',
        'priority_id',
        'real_hours',
        'version_id',
        'receipt_id',
    ];

    protected $searchableFields = ['*'];

    public function statu()
    {
        return $this->belongsTo(Statu::class);
    }

    public function priority()
    {
        return $this->belongsTo(Priority::class);
    }

    public function version()
    {
        return $this->belongsTo(Version::class);
    }

    public function attaches()
    {
        return $this->hasMany(Attach::class);
    }

    public function receipt()
    {
        return $this->belongsTo(Receipt::class);
    }

    public function developers()
    {
        return $this->belongsToMany(Developer::class);
    }
}
