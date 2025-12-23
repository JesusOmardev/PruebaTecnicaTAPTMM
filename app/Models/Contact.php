<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $fillable = ['name', 'email', 'address'];

    public function phones()
    {
        return $this->hasMany(Phone::class);
    }
}