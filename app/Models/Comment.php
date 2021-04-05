<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    public function like($user = null)
    {
        $user = $user ?: auth()->user();
        return $this->likes()->attach($user);
    }

    public function likes()
    {
        return $this->morphToMany(User::class, 'likable')->withTimestamps();
    }
}
