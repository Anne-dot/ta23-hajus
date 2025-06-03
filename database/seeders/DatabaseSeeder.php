<?php

namespace Database\Seeders;

use App\Models\MyFavoriteSubject;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(10)->create();

        User::firstOrCreate(
            ['email' => 'test@test.test'],
            [
                'name' => 'test@test.test',
                'password' => Hash::make('test@test.test'),
                'is_admin' => false,
            ]
        );

        User::firstOrCreate(
            ['email' => 'admin@test.test'],
            [
                'name' => 'admin@test.test',
                'password' => Hash::make('admin@test.test'),
                'is_admin' => true,
            ]
        );

        $users = User::all();

        for ($i = 0; $i < 30; $i++) {
            Post::factory()->create([
                'user_id' => $users->random()->id,
            ]);
        }

        // Seed emotions
        // Create some of each emotion category
        $categories = ['happy', 'sad', 'angry', 'fear', 'surprised', 'love'];

        foreach ($categories as $category) {
            // Create 5 emotions per category
            MyFavoriteSubject::factory(5)->create();
        }

        // Create some intense emotions
        MyFavoriteSubject::factory(10)->intense()->create();

        // Create some mild emotions
        MyFavoriteSubject::factory(10)->mild()->create();

        // Create some random emotions
        MyFavoriteSubject::factory(20)->create();

        // Seed products database with random products
        $this->call(ProductSeeder::class);
    }
}
