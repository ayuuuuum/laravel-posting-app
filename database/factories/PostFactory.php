<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            //'user_id' => 1, // usersテーブルにidカラムの値が1のユーザーが存在することが前提
            'user_id' => 18, // usersテーブルにidカラムの値が18のユーザーが存在することが前提
            'title' => fake()->realText(20, 5),
            'content' => fake()->realText(200, 5)
        ];
    }
}
