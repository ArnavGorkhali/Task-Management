<?php

namespace App\Models;

use App\Traits\ClientFilter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasFactory, SoftDeletes, ClientFilter;

    protected $fillable = [
        'name',
        'email',
        'company_name',
        'mobile',
        'address'
    ];
}
