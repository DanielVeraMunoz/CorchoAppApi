<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Community extends Model
{
    protected $fillable = [
        'name',
        'address',
        'postal_code'
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
