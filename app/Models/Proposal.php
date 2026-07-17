<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    /**
     * Tasks ordered by the manual gantt sort_order with a stable
     * id fallback. Used by the gantt view so the rendered chart
     * matches the persisted user ordering.
     */
    public function orderedTasks()
    {
        return $this->tasks()
            ->orderBy('sort_order')
            ->orderBy('id');
    }
}
