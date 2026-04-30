<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    public function definition(): array
    {
        $destinations = [
            ['location' => 'Barcellona', 'country' => 'Spagna'],
            ['location' => 'Tokyo', 'country' => 'Giappone'],
            ['location' => 'New York', 'country' => 'USA'],
            ['location' => 'Parigi', 'country' => 'Francia'],
            ['location' => 'Santorini', 'country' => 'Grecia'],
            ['location' => 'Bali', 'country' => 'Indonesia'],
            ['location' => 'Lisbona', 'country' => 'Portogallo'],
            ['location' => 'Marrakech', 'country' => 'Marocco'],
            ['location' => 'Praga', 'country' => 'Repubblica Ceca'],
            ['location' => 'Kyoto', 'country' => 'Giappone'],
        ];

        $destination = fake()->randomElement($destinations);

        return [
            'title' => fake()->sentence(4, false),
            'location' => $destination['location'],
            'country' => $destination['country'],
            'img' => null,
            'description' => fake()->paragraph(3),
        ];
    }
}
