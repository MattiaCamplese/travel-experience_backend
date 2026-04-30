<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    public function definition(): array
    {
        $comments = [
            'Che posto meraviglioso, vorrei tornarci!',
            'Bellissimo racconto, grazie per la condivisione.',
            'Ho visitato anche io questo posto, è fantastico!',
            'Ottimi consigli, lo segno per il prossimo viaggio.',
            'Le foto sono stupende, com\'è il cibo lì?',
            'Mi hai fatto venire voglia di partire subito!',
            'Quanti giorni ci sei rimasto?',
            'Il periodo migliore per visitare questo posto?',
            'Esperienza unica, assolutamente da fare.',
            'Anche io l\'ho visitato l\'anno scorso, bellissimo!',
        ];

        return [
            'comment' => fake()->randomElement($comments),
        ];
    }
}
