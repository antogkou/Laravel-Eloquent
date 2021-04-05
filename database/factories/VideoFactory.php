<?php

namespace Database\Factories;

use App\Models\Video;
use Illuminate\Database\Eloquent\Factories\Factory;

class VideoFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Video::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'watchable_type' => $this->faker->title,
            'watchable_id' => $this->faker->randomDigit,
            'title' => $this->faker->jobTitle,
            'description' => $this->faker->sentence(5),
            'url' => $this->faker->url
        ];
    }
}
