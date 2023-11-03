<?php

namespace App\Models;

use App\Repositories\Function\FunctionRepository;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;

class  EventFunction extends Model
{
    use HasFactory, SoftDeletes, HasRelationships;

    protected $table = "functions";

    protected $fillable = [
        'name',
        'note',
        'event_id',
        'start_date',
        'end_date',
        'status',
        'priority',
        'color_code',
        'venue_id'
    ];

    protected $casts = [
        'color_code' => 'array'
    ];

    protected static function boot()
    {
        parent::boot();

        static::updated(function (EventFunction $eventFunction) {
            if ($eventFunction->isDirty(['status'])) {
                (new FunctionRepository())->updateParentStatus($eventFunction);
            }
        });
    }

    public function event()
    {
        return $this->belongsTo(Event::class,'event_id');
    }

    public function venue()
    {
        return $this->belongsTo(Venue::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'function_id')
            ->where('parent_id', null)
            ->with('vendor:id,name')
            ->withCount('vendors')
            ->withCount('complete_subtasks', 'subtasks')
            ->orderBy('tasks.priority', 'asc')
            ->orderBy('tasks.id', 'asc');
    }

    public function complete_tasks()
    {
        return $this->hasMany(Task::class, 'function_id')
            ->orderBy('tasks.order', 'desc')
            ->orderBy('tasks.id', 'desc')
            ->where('status', 'complete');
    }

    public function vendors()
    {
        return $this->hasManyDeepFromRelations($this->tasks(), (new Task())->vendor());
    }
}
