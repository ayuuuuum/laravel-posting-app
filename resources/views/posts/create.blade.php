{{--@extendsというディレクティブを使ってベースとなる親ビューを指定--}}
{{--フォルダ名.ファイル名（.blade.phpは不要）と記述--}}
@extends('layouts.app')

{{--@sectionというディレクティブを使って「親ビューの@yieldを何で置き換えるか」を指定--}}
{{--@section('@yieldの引数に指定した名前', '置き換える値')--}}
@section('title', '新規投稿')

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

   <form action="{{ route('posts.store') }}" method="POST">
       @csrf
       <div class="form-group mb-3">
           <label for="title">タイトル</label>{{--old()ヘルパ関数で、エラー時直前のデータを保持--}}
           <input type="text" class="form-control" id="title" name="title" value="{{ old('title') }}">
       </div>
       <div class="form-group mb-3">
           <label for="content">本文</label>{{--old()ヘルパ関数で、エラー時直前のデータを保持--}}
           <textarea class="form-control" id="content" name="content">{{ old('content') }}</textarea>
       </div>
       <button type="submit" class="btn btn-outline-primary">投稿</button>
   </form>
@endsection
