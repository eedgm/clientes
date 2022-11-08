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

    protected $appends = ["open"];

    public function getOpenAttribute(){
        return true;
    }

    protected $fillable = [
        'text',
        'hours',
        'statu_id',
        'priority_id',
        'start_date',
        'real_hours',
        'receipt_id',
        'proposal_id',
        'duration',
        'progress',
        'parent'
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
