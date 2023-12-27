<?php

namespace Database\Factories;

use App\Enums\Status;
use App\Models\Proposal;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProposalFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Proposal::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'applicant_info' => $this->faker->paragraphs(3, true),
            'publication_title' => ucwords($this->faker->words(5, true)),
            'proposed_authors' => $this->faker->randomElements([
                $this->faker->name(),
                $this->faker->name(),
                $this->faker->name(),
                $this->faker->name(),
                $this->faker->name(),
            ], $this->faker->numberBetween(1, 5)),
            'study_background' => $this->faker->paragraphs(6, true),
            'research_question' => $this->faker->paragraphs(6, true),
            'data_and_population' => $this->faker->paragraphs(6, true),
            'analysis_plan' => $this->faker->paragraphs(6, true),
            'start_analysis_date' => $this->faker->dateTimeBetween('+6 months', '+7 months'),
            'start_writing_date' => $this->faker->dateTimeBetween('+7 months', '+8 months'),
            'completion_date' => $this->faker->dateTimeBetween('+13 months', '+18 months'),
            'status' => $this->faker->randomElement(Status::class),
        ];
    }
}
