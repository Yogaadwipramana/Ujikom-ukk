<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;

    protected $table = 'members';

    protected $fillable = [
        'name',
        'no_telepon',
        'point'
    ];

    public function orders()
    {
        return $this->hasMany(Order::class, 'members_id');
    }
}

