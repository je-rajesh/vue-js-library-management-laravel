<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $guarded = [];

    public function users()
    {
        return $this->hasMany(User::class)->withTimestamps()->withPivot('deleted_at');
    }
}
