<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MyFavoriteSubject>
 */
class MyFavoriteSubjectFactory extends Factory
{
    protected $emotions = [
        'happy' => [
            'titles' => ['Joyful', 'Ecstatic', 'Cheerful', 'Delighted', 'Elated', 'Gleeful', 'Jubilant', 'Blissful'],
            'emojis' => ['😊', '😄', '😃', '🥳', '😁', '🤗', '✨', '🎉'],
            'colors' => ['#FFD700', '#FFA500', '#FFFF00', '#FFE4B5', '#F0E68C'],
        ],
        'sad' => [
            'titles' => ['Melancholic', 'Sorrowful', 'Gloomy', 'Heartbroken', 'Depressed', 'Miserable', 'Dejected'],
            'emojis' => ['😢', '😭', '😔', '😞', '💔', '😥', '😪'],
            'colors' => ['#4682B4', '#191970', '#000080', '#483D8B', '#6495ED'],
        ],
        'angry' => [
            'titles' => ['Furious', 'Enraged', 'Irritated', 'Livid', 'Outraged', 'Hostile', 'Wrathful'],
            'emojis' => ['😠', '😡', '🤬', '😤', '💢', '🔥', '👿'],
            'colors' => ['#FF0000', '#DC143C', '#8B0000', '#B22222', '#CD5C5C'],
        ],
        'fear' => [
            'titles' => ['Terrified', 'Anxious', 'Worried', 'Panicked', 'Petrified', 'Nervous', 'Alarmed'],
            'emojis' => ['😨', '😰', '😱', '🫨', '😟', '😦', '🫣'],
            'colors' => ['#800080', '#4B0082', '#8B008B', '#9370DB', '#663399'],
        ],
        'surprised' => [
            'titles' => ['Astonished', 'Amazed', 'Shocked', 'Stunned', 'Flabbergasted', 'Bewildered'],
            'emojis' => ['😲', '😮', '🤯', '😳', '🫢', '😵', '🙀'],
            'colors' => ['#FF69B4', '#FF1493', '#C71585', '#DB7093', '#FFB6C1'],
        ],
        'love' => [
            'titles' => ['Enamored', 'Passionate', 'Affectionate', 'Devoted', 'Smitten', 'Infatuated'],
            'emojis' => ['❤️', '💕', '💖', '😍', '🥰', '💝', '💗'],
            'colors' => ['#FF1493', '#FF69B4', '#C71585', '#DC143C', '#FFC0CB'],
        ],
    ];

    protected $descriptions = [
        'happy' => [
            'Your heart feels light as a feather, and everything seems to sparkle with possibility.',
            'A warm glow spreads through your chest, making you want to dance and sing.',
            'The world looks brighter, colors more vivid, and you can\'t stop smiling.',
            'Energy bubbles up from within, making you feel like you could conquer anything.',
        ],
        'sad' => [
            'A heavy weight settles in your chest, making each breath feel like an effort.',
            'The world loses its color, everything appearing in shades of gray.',
            'Tears threaten to spill over as memories wash over you in waves.',
            'An emptiness echoes inside, like a hollow space where joy used to live.',
        ],
        'angry' => [
            'Heat rises to your face as your muscles tense, ready for action.',
            'Your jaw clenches and fists ball up as frustration boils over.',
            'A fire burns in your belly, demanding to be released in a primal scream.',
            'Every nerve feels raw and exposed, ready to snap at the slightest provocation.',
        ],
        'fear' => [
            'Your heart races as cold sweat breaks out across your skin.',
            'Every shadow seems to hide danger, and your body prepares to flee.',
            'A knot forms in your stomach as your mind races through worst-case scenarios.',
            'Your breath comes in short gasps as the world feels suddenly threatening.',
        ],
        'surprised' => [
            'Your eyes widen as your brain struggles to process the unexpected.',
            'Time seems to stop for a moment as reality shifts before you.',
            'A gasp escapes your lips as your mind reels from the revelation.',
            'Your heart skips a beat as the world turns upside down in an instant.',
        ],
        'love' => [
            'Butterflies dance in your stomach whenever they cross your mind.',
            'Your heart swells with warmth, threatening to burst with affection.',
            'The world seems more beautiful when seen through love-tinted glasses.',
            'Every cell in your body hums with connection and belonging.',
        ],
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $category = $this->faker->randomElement(array_keys($this->emotions));
        $categoryData = $this->emotions[$category];

        return [
            'title' => $this->faker->randomElement($categoryData['titles']),
            'description' => $this->faker->randomElement($this->descriptions[$category]),
            'emoji' => $this->faker->randomElement($categoryData['emojis']),
            'intensity' => $this->faker->numberBetween(1, 10),
            'color' => $this->faker->randomElement($categoryData['colors']),
            'category' => $category,
        ];
    }

    /**
     * Indicate that the emotion is intense.
     */
    public function intense(): static
    {
        return $this->state(fn (array $attributes) => [
            'intensity' => $this->faker->numberBetween(8, 10),
        ]);
    }

    /**
     * Indicate that the emotion is mild.
     */
    public function mild(): static
    {
        return $this->state(fn (array $attributes) => [
            'intensity' => $this->faker->numberBetween(1, 3),
        ]);
    }
}
