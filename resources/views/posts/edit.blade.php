{{--@extendsというディレクティブを使ってベースとなる親ビューを指定--}}
{{--フォルダ名.ファイル名（.blade.phpは不要）と記述--}}
@extends('layouts.app')

{{--@sectionというディレクティブを使って「親ビューの@yieldを何で置き換えるか」を指定--}}
{{--@section('@yieldの引数に指定した名前', '置き換える値')--}}
@section('title', '投稿編集')

{{--@section('@yieldの引数に指定した名前')
    置き換えるコード
    @endsection--}}
@section('content')
   @if ($errors->any())
       <div class="alert alert-danger">
           <ul>
               @foreach ($errors->all() as $error)
                   <li>{{ $error }}</li>
               @endforeach
           </ul>
       </div>
   @endif

   <div class="mb-2">
       <a href="{{ route('posts.index') }}" class="text-decoration-none">&lt; 戻る</a>
   </div>

   <form action="{{ route('posts.update', $post) }}" method="POST">
       @csrf
       @method('PATCH')
       <div class="form-group mb-3">
           <label for="title">タイトル</label>{{--oldヘルパ関数でエラー時に入力内容が保持される&第2引数で直前の入力値が存在しない場合の初期値（エラー時以外の通常の初期値）を設定--}}
           <input type="text" class="form-control" id="title" name="title" value="{{ old('title', $post->title) }}">
       </div>
       <div class="form-group mb-3">
           <label for="content">本文</label>{{--oldヘルパ関数でエラー時に入力内容が保持される&第2引数で直前の入力値が存在しない場合の初期値（エラー時以外の通常の初期値）を設定--}}
           <textarea class="form-control" id="content" name="content">{{ old('content', $post->content) }}</textarea>
       </div>
       <button type="submit" class="btn btn-outline-primary">更新</button>
   </form>
@endsection
