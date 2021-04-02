<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class EloquentTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_a_post(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);
        $this->assertDatabaseCount('posts', 1);
        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseHas('posts', ['user_id' => $user->id]);
    }

    public function test_user_posts_deleted_if_user_is_deleted(): void
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

    public function test_post_can_have_tags(): void
    {
        $tag = Tag::factory()->create();
        $post = Post::factory()->create();

        $post->tags()->attach($tag);

        $this->assertDatabaseHas('post_tag', ['tag_id' => $tag->id]);
    }

    public function test_tag_can_be_removed_from_post(): void
    {
        $firstTag = Tag::factory()->create();
        $secondTag = Tag::factory()->create();
        $post = Post::factory()->create();

        $post->tags()->attach([$firstTag->id, $secondTag->id]);
        $this->assertDatabaseHas('post_tag', ['tag_id' => $firstTag->id]);

        $post->tags()->detach($firstTag);
        $this->assertDatabaseMissing('post_tag', ['tag_id' => $firstTag->id]);
        $this->assertDatabaseHas('post_tag', ['tag_id' => $secondTag->id]);
        
    }
}
