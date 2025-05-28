<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Subject;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Seed emotions
        // Create some of each emotion category
        $categories = ['happy', 'sad', 'angry', 'fear', 'surprised', 'love'];
        
        foreach ($categories as $category) {
            // Create 5 emotions per category
            Subject::factory(5)->create();
        }
        
        // Create some intense emotions
        Subject::factory(10)->intense()->create();
        
        // Create some mild emotions
        Subject::factory(10)->mild()->create();
        
        // Create some random emotions
        Subject::factory(20)->create();
    }
}
