<?php

namespace App\Models;

use App\Traits\EventFilter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;
use Staudenmeir\EloquentHasManyDeep\HasTableAlias;

class Event extends Model
{
    use HasFactory, SoftDeletes, EventFilter, HasRelationships, HasTableAlias;

    protected $fillable = [
        'name',
        'email',
        'note',
        'ethnicity',
        'mobile_number',
        'company_name',
        'company_id',
        'category',
        'client_id',
        'client_name',
        'start_date',
        'end_date',
        'status',
        'priority',
        'color_code',
        'address'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime'
    ];

    protected $hidden = ['laravel_through_key'];

    protected $appends = ['is_favourite', 'type'];

    public function getIsFavouriteAttribute()
    {
        return Auth::user() ? Auth::user()->favourites()->get()->contains($this->id) : false;
    }

    public function functions()
    {
        return $this->hasMany(EventFunction::class)->orderBy('functions.order', 'asc')
            ->with('venue')
            ->orderBy('functions.id', 'desc')
            ->withCount('tasks')
            ->withCount('vendors')
            ->withCount('complete_tasks');
    }

    public function tasks()
    {
        return $this->hasManyDeepFromRelations($this->functions(), (new EventFunction())->tasks());
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function getTypeAttribute()
    {
        $type = null;
        if($this->start_date && (is_null($this->status) || $this->status == "incomplete") && $this->start_date->format('Y-m-d') > now()->format('Y-m-d')){
            return "upcoming";
        }
        if(($this->start_date && $this->end_date) && (is_null($this->status) || $this->status == "incomplete") && $this->start_date->format('Y-m-d') <= now()->format('Y-m-d') && $this->end_date->format('Y-m-d') >= now()->format('Y-m-d')){
            return 'ongoing';
        }
        if($this->end_date && ($this->status == "complete" || $this->end_date->format('Y-m-d') < now()->format('Y-m-d'))){
            return 'completed';
        }
        return $type;
    }
}
