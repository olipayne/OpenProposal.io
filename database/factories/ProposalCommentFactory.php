<?php

namespace Database\Factories;

use App\Models\Proposal;
use App\Models\ProposalComment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProposalCommentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ProposalComment::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            // Pick a random proposal ID from existing proposals
            'proposal_id' => Proposal::all()->random()->id,
            'user_id' => User::factory(),
            'comment' => $this->faker->text(),
        ];
    }
}
