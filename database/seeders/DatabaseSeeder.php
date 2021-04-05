<?php

namespace Database\Seeders;

use App\Models\Affiliation;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Affiliation::factory()
            ->count(2)
            ->state(new Sequence(
                ['name' => 'left'],
                ['name' => 'right'],
            ))->create();

        User::factory()->create([
            'affiliation_id' => Affiliation::find(1)->id,
            'name' => 'Antonis Gkoutzamanis',
            'email' => 'antonis.gkoutzamanis@pfizer.com',
            'password' => bcrypt('12345678'),
        ]);
        User::factory()->create([
            'affiliation_id' => Affiliation::find(2)->id,
            'name' => 'Katerina Birou',
            'email' => 'katerina.birou@pfizer.com',
            'password' => bcrypt('12345678'),
        ]);

        Post::factory()
            ->count(8)
            ->state(new Sequence(
                fn () => ['user_id' => User::all()->random()],
//                ['user_id' => User::find(1)->id],
//                ['user_id' => User::find(2)->id]
            ))->create();
    }
}
