<?php

namespace App\Models;

use App\Traits\VendorFilter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vendor extends Model
{
    use HasFactory, SoftDeletes, VendorFilter;

    protected $fillable = [
        'name',
        'category',
        'grade',
        'notes',
        'phone_number',
        'email',
        'company_name',
        'mobile',
        'address',
        'priority',
    ];

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function complete_tasks()
    {
        return $this->hasMany(Task::class)
            ->where('status', 'complete');
    }

    public function incomplete_tasks()
    {
        return $this->hasMany(Task::class)
            ->where(function($q){
                $q->where('status', null)
                    ->orWhere('status', 'incomplete');
            });
    }
}
