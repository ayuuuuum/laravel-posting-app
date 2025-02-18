<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Post;

class PostTest extends TestCase
{
    use RefreshDatabase;

    // 未ログインのユーザーは投稿一覧ページにアクセスできない
    public function test_guest_cannot_access_posts_index()
    {
        $response = $this->get(route('posts.index'));

        $response->assertRedirect(route('login'));
    }

    // ログイン済みのユーザーは投稿一覧ページにアクセスできる
    public function test_user_can_access_posts_index()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('posts.index'));

        $response->assertStatus(200);
        $response->assertSee($post->title);
    }

    // 未ログインのユーザーは投稿詳細ページにアクセスできない
    public function test_guest_cannot_access_posts_show()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        $response = $this->get(route('posts.show', $post));

        $response->assertRedirect(route('login'));
    }

    // ログイン済みのユーザーは投稿詳細ページにアクセスできる
    public function test_user_can_access_posts_show()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('posts.show', $post));

        $response->assertStatus(200);
        $response->assertSee($post->title);
    }

    // 未ログインのユーザーは新規投稿ページにアクセスできない
    public function test_guest_cannot_access_posts_create()
    {
        $response = $this->get(route('posts.create'));

        $response->assertRedirect(route('login'));
    }

    // ログイン済みのユーザーは新規投稿ページにアクセスできる
    public function test_user_can_access_posts_create()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('posts.create'));

        $response->assertStatus(200);
    }

    // 未ログインのユーザーは投稿を作成できない
    public function test_guest_cannot_access_posts_store()
    {
        $post = [
            'title' => 'プログラミング学習1日目',
            'content' => '今日からプログラミング学習開始！頑張るぞ！',
            //'_token' => csrf_token(),  419エラー対処 csrfトークンを追加⇒nullになった
        ];

        //$response = $this->post(route('posts.store'),$post,  ['_token' => csrf_token()]); //csrfエラー対処
        //$response = $this->withoutMiddleware()->post(route('posts.store'), $post);  @csrfエラー発生
        $response = $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class)->post(route('posts.store'), $post); //テスト時だけcsrfトークンを無効化
        
        $this->assertDatabaseMissing('posts', $post);
        $response->assertRedirect(route('login'));
    }

    // ログイン済みのユーザーは投稿を作成できる
    public function test_user_can_access_posts_store()
    {
        $user = User::factory()->create();

        $post = [
            'title' => 'プログラミング学習1日目',
            'content' => '今日からプログラミング学習開始！頑張るぞ！'
        ];

        //$response = $this->actingAs($user)->post(route('posts.store'), $post); @csrfエラー発生
        $response = $this->actingAs($user)->withoutMiddleware()->post(route('posts.store'), $post);

        // 詳細なエラーメッセージを確認
        //dd($response->content()); // ← これでレスポンスの中身を確認！


        $this->assertDatabaseHas('posts', $post);
        $response->assertRedirect(route('posts.index'));
    }

    // 未ログインのユーザーは投稿編集ページにアクセスできない
    public function test_guest_cannot_access_posts_edit()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        $response = $this->get(route('posts.edit', $post));

        $response->assertRedirect(route('login'));
    }

    // ログイン済みのユーザーは他人の投稿編集ページにアクセスできない
    public function test_user_cannot_access_others_posts_edit()
    {
        $user = User::factory()->create();
        $other_user = User::factory()->create();

        //「他人の投稿編集ページ」を用意するため、変数$other_userと変数$others_postを定義
        $others_post = Post::factory()->create(['user_id' => $other_user->id]);

        $response = $this->actingAs($user)->get(route('posts.edit', $others_post));

        $response->assertRedirect(route('posts.index'));
    }

    // ログイン済みのユーザーは自身の投稿編集ページにアクセスできる
    public function test_user_can_access_own_posts_edit()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('posts.edit', $post));

        $response->assertStatus(200);
    }

    // 未ログインのユーザーは投稿を更新できない
    public function test_guest_cannot_update_post()
    {
        $user = User::factory()->create();
        $old_post = Post::factory()->create(['user_id' => $user->id]);

        $new_post = [
            'title' => 'プログラミング学習1日目',
            'content' => '今日からプログラミング学習開始！頑張るぞ！'
        ];

        //patch=新しいデータで置き換え　第2引数に編集後の投稿（変数$new_post）を渡すことで、PATCHリクエストと同時にそのデータを送信
        //$response = $this->patch(route('posts.update', $old_post), $new_post); 
        //テスト時だけcsrfトークンを無効化
        $response = $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class)->patch(route('posts.update', $old_post), $new_post); 

        $this->assertDatabaseMissing('posts', $new_post);
        $response->assertRedirect(route('login'));
    }

    // ログイン済みのユーザーは他人の投稿を更新できない
    public function test_user_cannot_update_others_post()
    {
        $user = User::factory()->create();
        $other_user = User::factory()->create();

        $others_old_post = Post::factory()->create(['user_id' => $other_user->id]);

        $new_post = [
            'title' => 'プログラミング学習1日目',
            'content' => '今日からプログラミング学習開始！頑張るぞ！'
        ];

        //テスト時だけcsrfトークンを無効化
        //$response = $this->actingAs($user)->patch(route('posts.update', $others_old_post), $new_post);
        $response = $this->actingAs($user)->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class)->patch(route('posts.update', $others_old_post), $new_post);

        $this->assertDatabaseMissing('posts', $new_post);
        $response->assertRedirect(route('posts.index'));
    }

    // ログイン済みのユーザーは自身の投稿を更新できる
    public function test_user_can_update_own_post()
    {
        $user = User::factory()->create();
        //編集前の投稿（変数$old_postまたは$others_old_post）と編集後の投稿（変数$new_post）を定義
        $old_post = Post::factory()->create(['user_id' => $user->id]);

        $new_post = [
            'title' => 'プログラミング学習1日目',
            'content' => '今日からプログラミング学習開始！頑張るぞ！'
        ];

        //テスト時だけcsrfトークンを無効化
        //$response = $this->actingAs($user)->patch(route('posts.update', $old_post), $new_post);
        $response = $this->actingAs($user)->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class)->patch(route('posts.update', $old_post), $new_post);

        $this->assertDatabaseHas('posts', $new_post);
        $response->assertRedirect(route('posts.show', $old_post));
    }

    // 未ログインのユーザーは投稿を削除できない
    public function test_guest_cannot_destroy_post()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        //テスト時だけcsrfトークンを無効化
        //$response = $this->delete(route('posts.destroy', $post));
        $response = $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class)->delete(route('posts.destroy', $post));

        $this->assertDatabaseHas('posts', ['id' => $post->id]);
        $response->assertRedirect(route('login'));
    }

    // ログイン済みのユーザーは他人の投稿を削除できない
    public function test_user_cannot_destroy_others_post()
    {
        $user = User::factory()->create();
        $other_user = User::factory()->create();
        $others_post = Post::factory()->create(['user_id' => $other_user->id]);

        //テスト時だけcsrfトークンを無効化
        //$response = $this->actingAs($user)->delete(route('posts.destroy', $others_post));
        $response = $this->actingAs($user)->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class)->delete(route('posts.destroy', $others_post));

        $this->assertDatabaseHas('posts', ['id' => $others_post->id]);
        $response->assertRedirect(route('posts.index'));
    }

    // ログイン済みのユーザーは自身の投稿を削除できる
    public function test_user_can_destroy_own_post()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        //テスト時だけcsrfトークンを無効化
        //$response = $this->actingAs($user)->delete(route('posts.destroy', $post));
        $response = $this->actingAs($user)->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class)->delete(route('posts.destroy', $post));

        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
        $response->assertRedirect(route('posts.index'));
    }
}
