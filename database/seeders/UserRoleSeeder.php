<?php

namespace Database\Seeders;

use Carbon\Carbon;
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
        DB::table('user_roles')->delete();

        DB::table('user_roles')->insert([
            [
                'id' => 1,
                'user_id' => 1,
                'role_id' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 2,
                'user_id' => 2,
                'role_id' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 3,
                'user_id' => 3,
                'role_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 4,
                'user_id' => 3,
                'role_id' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 5,
                'user_id' => 4,
                'role_id' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 6,
                'user_id' => 5,
                'role_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 7,
                'user_id' => 5,
                'role_id' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 8,
                'user_id' => 6,
                'role_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 9,
                'user_id' => 6,
                'role_id' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 10,
                'user_id' => 7,
                'role_id' => 3,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 11,
                'user_id' => 8,
                'role_id' => 3,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 12,
                'user_id' => 9,
                'role_id' => 4,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 13,
                'user_id' => 10,
                'role_id' => 4,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ]);
    }
}
