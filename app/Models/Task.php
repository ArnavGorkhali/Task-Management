<?php

namespace App\Models;

use App\Repositories\Task\TaskRepository;
use App\Traits\TaskFilter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;
use Staudenmeir\EloquentHasManyDeep\HasTableAlias;

class Task extends Model
{
    use HasFactory, SoftDeletes, HasRelationships, HasTableAlias, TaskFilter;

    protected $fillable = [
        'name',
        'note',
        'function_id',
        'vendor_id',
        'parent_id',
        'due_date',
        'status',
        'priority',
    ];

    protected $casts = [
        'due_date' => 'datetime'
    ];

    protected $appends = ['type'];

    protected static function boot()
    {
        parent::boot();

        static::updated(function (Task $task) {
            if ($task->isDirty(['status'])) {
                (new TaskRepository())->updateParentStatus($task);
            }
        });
    }

    public function subtasks()
    {
        return $this->hasMany(Task::class, 'parent_id')
            ->with('parent:id,function_id')
            ->with('vendor:id,name')
            ->orderBy('tasks.priority', 'asc')
            ->orderBy('tasks.id', 'asc');
    }

    public function complete_subtasks()
    {
        return $this->hasMany(Task::class, 'parent_id')->orderBy('tasks.order', 'asc')
            ->where('status', 'complete');
    }

    public function parent()
    {
        return $this->belongsTo(Task::class, 'parent_id');
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function function()
    {
        return $this->belongsTo(EventFunction::class);
    }

    public function vendors()
    {
        return $this->hasManyDeepFromRelations($this->subtasks(), (new Task())->vendor());
    }

    public function event()
    {
        return $this->hasOneDeepFromRelations($this->function(), (new EventFunction())->event());
    }

    public function getStatusAttribute($value)
    {
        if($value == null){
            return "incomplete";
        }
        return $value;
    }

    public function getTypeAttribute()
    {
        $type = null;
        $function = $this->function;
        if(!$function){
            return (is_null($this->status) || $this->status == "incomplete") ? "upcoming" : "completed";
        }
        if(is_null($this->status) || $this->status == "incomplete"){
            return "upcoming";
        }
        if(($function->start_date && $function->end_date) && (is_null($this->status) || $this->status == "incomplete") && $function->start_date->format('Y-m-d') < now()->format('Y-m-d') && $function->end_date->format('Y-m-d') > now()->format('Y-m-d')){
            return 'ongoing';
        }
        if($this->status == "complete"){
            return 'completed';
        }
        return $type;
    }
}
