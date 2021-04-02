<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class EloquentTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_user_can_create_a_post()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);
        $this->assertDatabaseCount('posts', 1);
        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseHas('posts', ['user_id' => $user->id]);
    }

    public function test_user_posts_deleted_if_user_is_deleted()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);
        $anotherUserPost = Post::factory()->create();
        $this->assertDatabaseCount('posts', 2);

        $user->delete();
        $this->assertDeleted($user);
        $this->assertDeleted($post);

        $this->assertDatabaseCount('posts', 1);
        $this->assertDatabaseHas('posts', ['id' => $anotherUserPost->id]);
    }
}
