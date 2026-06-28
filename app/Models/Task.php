<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory;
    use Searchable;
    use SoftDeletes;

    // protected $appends = ["open"];

    // public function getOpenAttribute(){
    //     return true;
    // }

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
        'parent',
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

    public function proposal()
    {
        return $this->belongsTo(Proposal::class);
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
        return $this->belongsToMany(Developer::class)
            ->withPivot('comments', 'assignations', 'gain', 'hours');
    }

    /**
     * Sum of pivot hours for the task's developer assignments.
     *
     * Returns null when no assignment has hours recorded so the caller
     * can keep the legacy `tasks.hours` fallback in place.
     */
    public function getAssignmentHoursTotalAttribute()
    {
        $hasAnyHours = $this->developers()
            ->wherePivot('hours', '!=', null)
            ->exists();

        if (! $hasAnyHours) {
            return null;
        }

        return (float) $this->developers()
            ->wherePivot('hours', '!=', null)
            ->sum('developer_task.hours');
    }

    /**
     * Effective task hours: sum of developer assignment hours when
     * available, otherwise the legacy `tasks.hours` column.
     */
    public function getEffectiveHoursAttribute()
    {
        return $this->assignment_hours_total ?? (float) $this->hours;
    }
}
