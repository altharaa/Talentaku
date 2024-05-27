<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('user_role')->delete();

        DB::table('user_role')->insert([
            [
                'id' => 1,
                'user_id' => 1,
                'role_id' => 2
            ],
            [
                'id' => 2,
                'user_id' => 2,
                'role_id' => 2
            ],
            [
                'id' => 3,
                'user_id' => 3,
                'role_id' => 1
            ],
            [
                'id' => 4,
                'user_id' => 3,
                'role_id' => 2
            ],
            [
                'id' => 5,
                'user_id' => 4,
                'role_id' => 2
            ],
            [
                'id' => 6,
                'user_id' => 5,
                'role_id' => 1
            ],
            [
                'id' => 7,
                'user_id' => 5,
                'role_id' => 2
            ],
            [
                'id' => 8,
                'user_id' => 6,
                'role_id' => 1
            ],
            [
                'id' => 9,
                'user_id' => 6,
                'role_id' => 2
            ]
        ]);
    }
}
