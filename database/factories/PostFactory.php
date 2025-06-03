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

        //     Schema::create('posts', function (Blueprint $table) {
        //         $table->id();
        //         $table->foreignId('user_id')->constrained()->after('id');
        //         $table->string('title');
        //         $table->text('description');
        //         $table->timestamps();
        //     });
        // }

        return [
            'title' => fake()->sentence(),
            'description' => fake()->paragraphs(3, true),
        ];
    }
}
