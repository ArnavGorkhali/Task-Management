<?php

namespace App\Models;

use App\Traits\VenueFilter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venue extends Model
{
    use HasFactory, VenueFilter;

    protected $fillable = [
        'name',
        'email',
        'address',
        'contact_person',
        'phone',
        'floor_plan'
    ];
}
