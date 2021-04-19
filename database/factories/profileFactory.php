<?php

namespace Database\Factories;

use App\Models\Profile;
use Illuminate\Database\Eloquent\Factories\Factory;

class profileFactory extends Factory
{
    protected $model = Profile::class;

    public function definition(): array
    {
        return [
            'balance' => rand(0, 10),
        ];
    }
}
