<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::truncate();

        User::create([
            'name'  => 'Prueba',
            'email' => 'prueba@test.com',
            'password'  => bcrypt('secret'),
            'remember_token'    => Str::random()
        ]);
    }
}
