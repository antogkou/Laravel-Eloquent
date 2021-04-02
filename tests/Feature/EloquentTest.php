<?php

namespace Tests\Feature;

use App\Models\Affiliation;
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

    public function test_can_see_tags_on_posts()
    {
        $post = Post::factory()
            ->hasAttached(Tag::factory()->count(3))
            ->create();
        $this->assertDatabaseHas('post_tag', ['post_id' => $post->id]);
        $this->assertDatabaseCount('post_tag', 3);
    }

    public function test_when_post_gets_deleted_pivot_cascades()
    {
        $post = Post::factory()
            ->hasAttached(Tag::factory()->count(3))
            ->create();
        $this->assertDatabaseCount('post_tag', 3);

        $post->delete();
        $this->assertDatabaseCount('post_tag', 0);
    }

    public function test_get_users_with_left_affiliation()
    {
        $leftAffiliation = Affiliation::factory()->create(['name' => 'left']);
        $this->assertDatabaseHas('affiliations', ['name' => 'left']);
        $this->assertDatabaseCount('affiliations', 1);

        $user = User::factory()->create(['name' => 'Antonis', 'affiliation_id' => 1]);
        $this->assertDatabaseCount('users', 1);

        $post = Post::factory()->count(3)->create(['user_id' => $user->id]);
        $this->assertDatabaseCount('posts', 3);

        $leftUsers = Affiliation::whereName('left')->first();
        $this->assertNotEmpty($leftUsers);
    }
}
