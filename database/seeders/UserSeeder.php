<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ita = User::create ([
            'name' =>'Anita Fauzah',
            'email' => 'anita@gmail.com',
            'birth_date' => '1974-04-05',
            'address' => 'Mlati Kidul RT 01/RW 01 No. 2B, Kudus',
            'password' => bcrypt('ita1234')
        ]);
        $ita->assignRole('teacher');

        $moza = User::create ([
            'name' =>"Siti Muzaro'ah",
            'email' => 'moza@gmail.com',
            'birth_date' => '1998-06-26',
            'address' => ' ds. Talun RT 01/RW 04',
            'password' => bcrypt('moza1234')
        ]);
        $moza->assignRole('teacher');

        $rani = User::create ([
            'name' =>"Rani Puspitasari",
            'email' => 'rani@gamil.com',
            'birth_date' => '1996-09-12',
            'address' => 'Jl. Ganesha Selatan, Pasuruhan lor RT 02/ RW 10, Jati, kudus',
            'password' => bcrypt('rani1234')
        ]);
        $rani->assignRole('teacher');

        $nurul = User::create ([
            'name' =>"Nurul Hikmah",
            'email' => 'nurul@gmail.com',
            'birth_date' => '1997-07-08',
            'address' => 'Ds. Jepang Pakis RT 01/Rw 01, Kudus',
            'password' => bcrypt('nurul1234')
        ]);
        $nurul->assignRole('teacher');

        $sekar = User::create ([
            'name' =>"Sekar Anisa",
            'email' => 'sekaranisa@gmail.com',
            'birth_date' => '2000-03-17',
            'address' => 'Kauman RT 09/RW 01, Batang',
            'password' => bcrypt('sekar1234')
        ]);
        $sekar->assignRole('teacher');

        $athi = User::create ([
            'name' =>"Athi' Mufarrihah",
            'email' => 'athi@gmai.com',
            'birth_date' => '1984-06-07',
            'address' => 'Samirejo',
            'password' => bcrypt('athi1234')
        ]);
        $athi->assignRole('teacher');

        
    }
}
