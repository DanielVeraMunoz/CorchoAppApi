<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
          // Admin
          User::create([
              'name' => 'Admin',
              'email' => 'admin@corcho.com',
              'password' => bcrypt('password'),
              'community_id' => 1,
              'floor' => '1',
              'door' => 'A',
              'role' => 'admin',
          ]);

          // Usuario demo
          User::create([
              'name' => 'Demo User',
              'email' => 'demo@corcho.com',
              'password' => bcrypt('password'),
              'community_id' => 1,
              'floor' => '2',
              'door' => 'B',
              'role' => 'user',
          ]);
      }
}
