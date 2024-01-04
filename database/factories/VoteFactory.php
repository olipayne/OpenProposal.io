<?php

namespace Database\Factories;

use App\Models\Proposal;
use App\Models\User;
use App\Models\Vote;
use Illuminate\Database\Eloquent\Factories\Factory;

class VoteFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Vote::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'proposal_id' => Proposal::factory(),
            'user_id' => User::factory(),
            'vote' => $this->faker->numberBetween(-8, 8),
            'comment' => $this->faker->regexify('[A-Za-z0-9]{1000}'),
        ];
    }
}
