<?php

namespace Database\Seeders;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Seeder;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::factory(10)->create();

        Ticket::factory(100)
            ->recycle($user)
            ->create();

        User::create([
            'email' => 'manager@manager.com',
            'password' => bcrypt('password'),
            'name' => 'The Manager',
            'is_admin' => true
        ]);

        
    }
}
