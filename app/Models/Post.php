<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Likable;
use Laravel\Scout\Searchable;

class Post extends Model
{
    use HasFactory;
    use Likable;
    use Searchable;

    public $fillable = [
        'user_id',
        'body',
        'title'
    ];

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        $array = $this->toArray();

        // Customize the data array...
        unset($array['updated_at']);

        return $array;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class)->withTimestamps();
    }
}
