<?php

namespace Tests\Feature;

use App\Models\Affiliation;
use App\Models\Collection;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Series;
use App\Models\Tag;
use App\Models\User;
use App\Models\Video;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

    public function test_can_see_tags_on_posts(): void
    {
        $post = Post::factory()
            ->hasAttached(Tag::factory()->count(3))
            ->create();
        $this->assertDatabaseHas('post_tag', ['post_id' => $post->id]);
        $this->assertDatabaseCount('post_tag', 3);
    }

    public function test_when_post_gets_deleted_pivot_cascades(): void
    {
        $post = Post::factory()
            ->hasAttached(Tag::factory()->count(3))
            ->create();
        $this->assertDatabaseCount('post_tag', 3);

        $post->delete();
        $this->assertDatabaseCount('post_tag', 0);
    }

    public function test_get_users_with_left_affiliation(): void
    {
        $leftAffiliation = Affiliation::factory()->create(['name' => 'left']);
        $this->assertDatabaseHas('affiliations', ['name' => 'left']);
        $this->assertDatabaseCount('affiliations', 1);

        $user = User::factory()->create(['name' => 'Antonis', 'affiliation_id' => 1]);
        $this->assertDatabaseCount('users', 1);

        $post = Post::factory()->count(3)->create(['user_id' => $user->id]);
        $this->assertDatabaseCount('posts', 3);

        $leftAffiliation = Affiliation::whereName('left')->first();
        $users = User::where('affiliation_id', '1')->get();
        $this->assertNotEmpty($leftAffiliation);
    }

    public function test_get_posts_with_right_or_left_affiliation(): void
    {
        $leftAffiliation = Affiliation::factory()->create(['name' => 'left']);
        $rightAffiliation = Affiliation::factory()->create(['name' => 'right']);
        $this->assertDatabaseHas('affiliations', ['name' => 'left']);
        $this->assertDatabaseHas('affiliations', ['name' => 'right']);
        $this->assertDatabaseCount('affiliations', 2);

        $user = User::factory()->create(['name' => 'Antonis', 'affiliation_id' => 1]);
        $secondUser = User::factory()->create(['name' => 'Notis', 'affiliation_id' => 2]);
        $this->assertDatabaseCount('users', 2);

        $post = Post::factory()->count(3)->create(['user_id' => $user->id]);
        $secondUserPosts = Post::factory()->count(5)->create(['user_id' => $secondUser->id]);
        $this->assertDatabaseCount('posts', 8);

        $rightAffiliation = Affiliation::whereName('right')->first();
        $rightPosts = $rightAffiliation->posts;
        $this->assertNotEmpty($rightPosts);

        $leftAffiliation = Affiliation::whereName('right')->first();
        $leftPosts = $leftAffiliation->posts;
        $this->assertNotEmpty($leftPosts);
    }

    public function test_get_collection_videos(): void
    {
        $collectionVideos = Video::factory()->count(3)->for(
            Collection::factory(), 'parent'
        )->create();
        $this->assertDatabaseCount('videos', 3);

        $collection = Video::find(1);
        $collectionParent = $collection->parent;

        $this->assertNotEmpty($collectionParent);

        $this->assertDatabaseCount('videos', 3);
    }

    public function test_get_series_videos(): void
    {
        $seriesVideos = Video::factory()->count(3)->for(
            Series::factory(), 'parent'
        )->create();
        $this->assertDatabaseCount('videos', 3);

        $series = Video::find(1);
        $seriesParent = $series->parent;

        $this->assertNotEmpty($seriesParent);

        $this->assertDatabaseCount('videos', 3);
    }

    /** @test */
    public function a_post_can_be_liked()
    {
        $this->actingAs(User::factory()->create());

        $post = Post::factory()->create();

        $post->like();

        $this->assertCount(1, $post->likes);
        $this->assertTrue($post->likes->contains('id', auth()->id()));
    }

    /** @test */
    public function a_comment_can_be_liked()
    {
        $this->actingAs(User::factory()->create());

        $comment = Comment::factory()->create();
        $comment->like();

        $userLikes = auth()->user()->likedComments;
        $this->assertCount(1, $userLikes);

        $this->assertCount(1, $comment->likes);
        $this->assertTrue($comment->likes->contains('id', auth()->id()));
    }

    /** @test */
    public function get_users_likes()
    {
        // assert that we can retrieve user's likes on comments
        $this->actingAs(User::factory()->create());
        $comment = Comment::factory()->create();
        $comment->like();
        $commentLikes = auth()->user()->likedComments;
        $this->assertCount(1, $commentLikes);

        // assert that we can retrieve user's likes on posts
        $this->actingAs(User::factory()->create());
        $post = Post::factory()->create();
        $post->like();
        $postLikes = auth()->user()->likedPosts;
        $this->assertCount(1, $postLikes);
    }
}
