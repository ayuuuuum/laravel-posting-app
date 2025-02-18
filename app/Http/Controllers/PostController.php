<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Post;
use App\Http\Requests\PostRequest;

class PostController extends Controller
{
    // 一覧ページ 
    public function index() 
    { 
    $posts = Auth::user()->posts()->orderBy('created_at', 'asc')->get();

    return view('posts.index', compact('posts')); 
    }

    // 詳細ページ
    public function show(Post $post)
    {
        $updated_at = Auth::user()->posts()->get([ 'updated_at' ]);

        return view('posts.show', compact('post', 'updated_at'));
    }

    //作成ページ
    public function create() 
    {
        return view('posts.create');
    }

    // 作成機能
    public function store(PostRequest $request)
    {
        // バリデーションを設定する
        $request->validate([
            'title' => 'required|string|max:40',
            'content' => 'required|max:200'
        ]);

        $post = new Post();
        $post->title = $request->input('title');
        $post->content = $request->input('content');
        $post->user_id = Auth::id();
        $post->save();

        return redirect()->route('posts.index')->with('flash_message', '投稿が完了しました。');
    }

    // 編集ページ
    public function edit(Post $post)
    {
        //他人の投稿にアクセスできないよう投稿ユーザーidと現在ログイン中のidが異なる場合リダイレクトする
        if ($post->user_id !== Auth::id()) {
            return redirect()->route('posts.index')->with('error_message', '不正なアクセスです。');
        }

        return view('posts.edit', compact('post'));
    }

    // 更新機能 フォームリクエストの型宣言を行いバリデーションを行う、「どのデータを更新するか」という情報を得るためPostモデルの型宣言を行いインスタンスを受け取る
    public function update(PostRequest $request, Post $post)
    {
        if ($post->user_id !== Auth::id()) {
            return redirect()->route('posts.index')->with('error_message', '不正なアクセスです。');
        }

        $post->title = $request->input('title');
        $post->content = $request->input('content');
        $post->save();

        return redirect()->route('posts.show', $post)->with('flash_message', '投稿を編集しました。');
    }

    // 削除機能
    public function destroy(Post $post) {
        //他人の投稿を削除できないよう投稿主IDと現在ログイン中のユーザーIDを比較し、異なる場合は投稿一覧ページにリダイレクト
        if ($post->user_id !== Auth::id()) {
            return redirect()->route('posts.index')->with('error_message', '不正なアクセスです。');
        }

        //受け取ったモデルのインスタンスに対してdelete()メソッドを実行しデータを削除する
        $post->delete();

        return redirect()->route('posts.index')->with('flash_message', '投稿を削除しました。');
    }
}
