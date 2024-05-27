<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->delete();

        DB::table('users')->insert([
            [
                'id' => 1,
                'name' =>'Anita Fauzah',
                'email' => 'ita@gmail.com',
                'address' => 'Mlati Kidul RT 01/RW 01 No. 2B, Kudus',
                'birth_date' => '1974-04-05',
                'password' => bcrypt('ita1234'),    
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 2,
                'name' =>"Siti Muzaro'ah",
                'email' => 'moza@gmail.com',
                'address' => ' ds. Talun RT 01/RW 04',
                'birth_date' => '1998-06-26',
                'password' => bcrypt('moza1234'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 3,
                'name' =>"Rani Puspitasari",
                'email' => 'rani@gmail.com',
                'address' => 'Jl. Ganesha Selatan, Pasuruhan lor RT 02/ RW 10, Jati, kudus',
                'birth_date' => '1996-09-12',
                'password' => bcrypt('rani1234'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(), 
            ],
            [
                'id' => 4,
                'name' =>"Nurul Hikmah",
                'email' => 'nurul@gmail.com',
                'address' => 'Ds. Jepang Pakis RT 01/Rw 01, Kudus',
                'birth_date' => '1997-07-08',
                'password' => bcrypt('nurul1234'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 5,
                'name' =>"Sekar Anisa",
                'email' => 'sekar@gmail.com',
                'address' => 'Kauman RT 09/RW 01, Batang',
                'birth_date' => '2000-03-17',
                'password' => bcrypt('sekar1234'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 6,
                'name' =>"Athi' Mufarrihah",
                'email' => 'athi@gmail.com',
                'address' => 'Samirejo',
                'birth_date' => '1984-06-07',
                'password' => bcrypt('athi1234'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ]);
    }
}
