<?php
namespace App\Traits;

use App\Models\User;

trait Likable
{
    public function like($user = null)
    {
        $user = $user ?: auth()->user();
        return $this->likes()->attach($user);
    }

    public function unlike($user = null)
    {
        $user = $user ?: auth()->user();
        return $this->likes()->detach($user);
    }

    public function likes()
    {
        return $this->morphToMany(User::class, 'likable')->withTimestamps();
    }
}
