<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;

class UserPreference extends Model
{
    use HasFactory;

    protected $casts = [
        'liked' => 'boolean',
    ];

    protected $fillable = [
        'user_id',
        'car_id',
        'liked',
    ];

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'user_id', 'id');
    }

    public function car(): HasOne
    {
        return $this->hasOne(Car::class, 'car_id', 'id');
    }
}
