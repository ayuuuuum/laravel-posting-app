{{--@extendsというディレクティブを使ってベースとなる親ビューを指定--}}
{{--フォルダ名.ファイル名（.blade.phpは不要）と記述--}}
@extends('layouts.app')

{{--@sectionというディレクティブを使って「親ビューの@yieldを何で置き換えるか」を指定--}}
{{--@section('@yieldの引数に指定した名前', '置き換える値')--}}
@section('title', '投稿詳細')

{{--@section('@yieldの引数に指定した名前')
    置き換えるコード
    @endsection--}}
@section('content')
    {{--データ更新後メッセージを表示--}}
   @if (session('flash_message'))
       <p class="text-success">{{ session('flash_message') }}</p>
   @endif

   <div class="mb-2">
       <a href="{{ route('posts.index') }}" class="text-decoration-none">&lt; 戻る</a>
   </div>

   <article>
       <div class="card mb-3">
           <div class="card-body">
               <h2 class="card-title fs-5">{{ $post->title }}</h2>
               <p class="card-text">{{ $post->content }}</p><br>
               <p>{{ $post->updated_at }}</p>

                {{--投稿主のユーザーIDと現在ログイン中のユーザーのIDを比較し、一致する場合にのみ「編集」、「削除」ボタンを表示--}}
               @if ($post->user_id === Auth::id())
                   <div class="d-flex">
                       <a href="{{ route('posts.edit', $post) }}" class="btn btn-outline-primary d-block me-1">編集</a>

                       {{--form要素にonsubmit属性を設定し、JavaScriptのconfirm()メソッドを使ってフォームを送信する前に「本当に削除してもよろしいですか？」という確認ダイアログを表示--}}
                       <form action="{{ route('posts.destroy', $post) }}" method="POST" onsubmit="return confirm('本当に削除してもよろしいですか？');">
                           @csrf
                           @method('DELETE')
                           <button type="submit" class="btn btn-outline-danger">削除</button>
                       </form>
                   </div>
               @endif
           </div>
       </div>
   </article>
@endsection

