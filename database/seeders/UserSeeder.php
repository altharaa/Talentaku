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
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        DB::table('user_roles')->delete();
        
        DB::table('users')->delete();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::table('users')->insert([
            [
                'id' => 1,
                'name' =>'Anita Fauzah',
                'email' => 'ita@gmail.com',
                'identification_number' => '0106202202050474',
                'address' => 'Mlati Kidul RT 01/RW 01 No. 2B, Kudus',
                'place_of_birth' => 'Jombang',
                'birth_date' => '1974-04-05',
                'joining_year' => '2022-06-01',
                'password' => bcrypt('ita1234'),    
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 2,
                'name' =>"Siti Muzaro'ah",
                'email' => 'moza@gmail.com',
                'identification_number' => '0106202202260698',
                'address' => ' ds. Talun RT 01/RW 04',
                'place_of_birth' => 'Pati',
                'birth_date' => '1998-06-26',
                'joining_year' => '2022-06-01',
                'password' => bcrypt('moza1234'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 3,
                'name' =>"Rani Puspitasari",
                'email' => 'rani@gmail.com',
                'identification_number' => '0106202202120996',
                'address' => 'Jl. Ganesha Selatan, Pasuruhan lor RT 02/ RW 10, Jati, kudus',
                'place_of_birth' => 'Kudus',
                'birth_date' => '1996-09-12',
                'joining_year' => '2022-06-01',
                'password' => bcrypt('rani1234'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(), 
            ],
            [
                'id' => 4,
                'name' =>"Nurul Hikmah",
                'email' => 'nurul@gmail.com',
                'identification_number' => '0106202202080797',
                'address' => 'Ds. Jepang Pakis RT 01/Rw 01, Kudus',
                'place_of_birth' => 'Kudus',
                'birth_date' => '1997-07-08',
                'joining_year' => '2022-06-01',
                'password' => bcrypt('nurul1234'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 5,
                'name' =>"Sekar Anisa",
                'email' => 'sekar@gmail.com',
                'identification_number' => '0102202402170300',
                'address' => 'Kauman RT 09/RW 01, Batang',
                'place_of_birth' => 'Batang',
                'birth_date' => '2000-03-17',
                'joining_year' => '2022-06-01',
                'password' => bcrypt('sekar1234'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 6,
                'name' =>"Athi' Mufarrihah",
                'email' => 'athi@gmail.com',
                'identification_number' => '0102202402070684',
                'address' => 'Samirejo',
                'place_of_birth' => 'Kudus',
                'birth_date' => '1984-06-07',
                'joining_year' => '2022-06-01',
                'password' => bcrypt('athi1234'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 7,
                'name' =>"M Dimas Prayoga",
                'email' => 'dimas@gmail.com',
                'identification_number' => '0101',
                'address' => 'Tenggeles RT 02/RW 04, Kec. Mejobo, Kudus',
                'place_of_birth' => 'Kudus',
                'birth_date' => '2014-01-01',
                'joining_year' => '2023-06-18',
                'password' => bcrypt('dimas1234'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ]);
    }
}
