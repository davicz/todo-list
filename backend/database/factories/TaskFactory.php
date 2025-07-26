<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // Gera um título de tarefa com 3 a 5 palavras aleatórias
            'title' => fake()->sentence(rand(3, 5)),
            // Gera uma descrição com 1 a 3 parágrafos aleatórios
            'description' => fake()->paragraph(rand(1, 3)),
            // Gera uma data de vencimento aleatória no próximo mês
            'due_date' => fake()->dateTimeBetween('now', '+1 month'),
            // Define a tarefa como não concluída por padrão
            'completed' => false,
        ];
    }
}
