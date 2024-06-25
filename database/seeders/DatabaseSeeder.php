<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\Icon;
use App\Models\Message;
use App\Models\Participant;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        /* User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]); */
        Participant::factory(100)->create();
        Message::factory(100)->create();
        Contact::factory(100)->create();
        Icon::factory(100)->create();
    }
}
