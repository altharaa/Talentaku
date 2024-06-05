<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InformationSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        DB::table('information')->delete();
        DB::table('information_list_desc')->delete();
        DB::table('list_desc')->delete();

        $listDescData = [
            [
                'title' => 'Visi',
                'desc' => 'Pendidikan Untuk semua : Eksploratif, Ekspresif, Mandiri dan Berkarakter',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'title' => 'Misi',
                'desc' => "Memberikan layanan kegiatan bermain sesuai minat untuk menggali dan mengembangkan potensi anak\nMembantu semua anak untuk mengembangkan diri dalam memecahkan masalah sehari-hari sesuai tahap perkembangannya\nMemberikan pengasuhan dan pembelajaran sesuai kondisi, potensi, dan budaya setempat",
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'title' => 'Rumah Belajar Anak',
                'desc' => 'Bimbingan belajar anak berkebutuhan khusus (autism, add/adhd, down syndrome, tunarungu, tunanetra, slow learning, speechdelay, disleksia). Per kelas 1 siswa - 1 Guru',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'title' => 'Point Pembelajaran',
                'desc' => 'Edukasi, activity daily living, motorik, wicara, dan okupasi.',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'title' => 'Program',
                'desc' => "Reguler\nFull-day\nOnline",
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'title' => 'Kelompok Bermain',
                'desc' => 'Pendidikan Anak Usia Dini dengan rentang usia 2 - 6 tahun. Anak diajak untuk mengikuti pendidikan pra sekolah yang menyenangkan. Menerima ABK',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'title' => 'Sekolah Dasar',
                'desc' => 'Memberikan kesempatan kepada ABK untuk belajar bersama-sama dengan anak pada umumnya di kelas yang sama. Usia anak minimal 7 tahun',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'title' => 'Program Unggulan KB & SD',
                'desc' => "Kelas terkondisi (3-5 anak)\nKurikulum reguler & inklusi\nAsesmen kemampuan & perkembangan\nTerapi dampingan individu\nGuru agama islam / kristen",
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'title' => 'Fasilitas',
                'desc' => "Ruang kelas nyaman & bersih\nToilet & kamar mandi\nDapur",
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        DB::table('list_desc')->insert($listDescData);

        $informationData = [
            [
                'title' => 'Whatsapp',
                'desc' => '0858-7504-8372 Bu Nurul',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'title' => 'Email',
                'desc' => 'yayasanpendidikantumbuhkembang@gmail.com',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'title' => 'Instagram',
                'desc' => '@kbinklusitalenta\n@sdinklusitalenta\n@rumahbelajar.anak',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'title' => 'Alamat',
                'desc' => 'Jl. HOS Cokroaminoto, Mlati Lor, Gg. Kauman No.187(RT 02 / RW 11) Kudus, Jawa Tengah 59319',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'title' => 'Visi & Misi',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'title' => 'Rumah Belajar Anak',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'title' => 'KB & SD Inklusi Talenta',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            
        ];

        foreach ($informationData as $program) {
            DB::table('information')->insert($program);
        }

        $infoListDescData = [
            [
                'information_id' => 5,
                'list_desc_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'information_id' => 5,
                'list_desc_id' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'information_id' => 6,
                'list_desc_id' => 3,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'information_id' => 6,
                'list_desc_id' => 4,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'information_id' => 6,
                'list_desc_id' => 5,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'information_id' => 7,
                'list_desc_id' => 6,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'information_id' => 7,
                'list_desc_id' => 7,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'information_id' => 7,
                'list_desc_id' => 8,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'information_id' => 7,
                'list_desc_id' => 9,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        DB::table('information_list_desc')->insert($infoListDescData); 
    }
}
