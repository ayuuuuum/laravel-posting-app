<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Post extends Model
{
    use HasFactory;

    // 1つの投稿は1人のユーザーに属する
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
