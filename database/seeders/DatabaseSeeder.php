<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Proposal;
use App\Models\ProposalComment;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Make 10 proposals, this makes 10 users as well
        Proposal::factory(500)->create();
        ProposalComment::factory(2000)->create();

        // Advise the user to run php artisan make:filament-user with a print
        // statement
        $this->command->info('Migrations and initial seed complete, making test user...');

        // Make an admin user
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'is_reviewer' => false,
            'is_admin' => true,
        ]);

        // Make a reviewer user
        User::factory()->create([
            'name' => 'Reviewer User',
            'email' => 'reviewer@example.com',
            'password' => bcrypt('password'),
            'is_reviewer' => true,
            'is_admin' => false,
        ]);

        // Make a regular user
        User::factory()->create([
            'name' => 'Regular User',
            'email' => 'user@example.com',
            'password' => bcrypt('password'),
            'is_reviewer' => false,
            'is_admin' => false,
        ]);

        $this->command->info('Users created! Log in with:');
        $this->command->info('Admin: admin@example.com | password');
        $this->command->info('Reviewer: reviewer@example.com | password');
        $this->command->info('User: user@example.com | password');
    }
}
