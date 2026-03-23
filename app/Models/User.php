<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

// #[Fillable(['name', 'email', 'password'])]
// #[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;
    use HasApiTokens;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected $fillable = [
        'name', 
        'email',
        'password',
        'floor',
        'door',
        'community_id',
        'role'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function community(){
        return $this->belongsTo(Community::class);
    }

    public function notes(){
        return $this->hasMany(Note::class);
    }

    public function comments(){
        return $this->hasMany(Comment::class);
    }

    public function givenThanks(){
        return $this->hasMany(Thank::class, 'giver_id');
    }

    public function receivedThanks(){
        return $this->hasMany(Thank::class, 'recipient_id');
    }

}

