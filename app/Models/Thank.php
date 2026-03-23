<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Thank extends Model
{
    protected $fillable = [
        'note_id',
        'giver_id',
        'recipient_id'
    ];



    public function note(){
        return $this->belongsTo(Note::class);
    }

    public function giver(){
        return $this->belongsTo(User::class, 'giver_id');
    }

    public function recipient(){
        return $this->belongsTo(User::class, 'recipient_id');
    }
}
