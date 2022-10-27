<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Attach extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = ['attachment', 'description', 'task_id', 'user_id'];

    protected $searchableFields = ['*'];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
