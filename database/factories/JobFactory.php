<?php

namespace Database\Factories;

use App\Models\Job;
use Illuminate\Database\Eloquent\Factories\Factory;

class JobFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Job::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
            'status' => 'active',
            'address' => $this->faker->address,
            'salary' => $this->faker->randomFloat(2),
            'company_id' => $this->faker->randomNumber(2),
            'user_id' => $this->faker->randomNumber(2),
        ];
    }
}
