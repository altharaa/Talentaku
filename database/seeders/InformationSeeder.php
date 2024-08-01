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
        DB::table('info_lists')->delete();
        DB::table('list_desc')->delete();
        DB::table('pivot_lists')->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $informationData = [
            [
                'id' => 1,
                'title' => 'Whatsapp',
                'desc' => '0858-7504-8372 Bu Nurul',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 2,
                'title' => 'Email',
                'desc' => 'yayasanpendidikantumbuhkembang@gmail.com',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 3,
                'title' => 'Instagram',
                'desc' => '@kbinklusitalenta\n@sdinklusitalenta\n@rumahbelajar.anak',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 4,
                'title' => 'Alamat',
                'desc' => 'Jl. HOS Cokroaminoto, Mlati Lor, Gg. Kauman No.187(RT 02 / RW 11) Kudus, Jawa Tengah 59319',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        DB::table('information')->insert($informationData);

        $listDescData = [
            [
                'id' => 1,
                'title' => 'Visi',
                'desc' => 'Pendidikan Untuk semua : Eksploratif, Ekspresif, Mandiri dan Berkarakter',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 2,
                'title' => 'Misi',
                'desc' => "Memberikan layanan kegiatan bermain sesuai minat untuk menggali dan mengembangkan potensi anak\nMembantu semua anak untuk mengembangkan diri dalam memecahkan masalah sehari-hari sesuai tahap perkembangannya\nMemberikan pengasuhan dan pembelajaran sesuai kondisi, potensi, dan budaya setempat",
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 3,
                'title' => 'Rumah Belajar Anak',
                'desc' => 'Bimbingan belajar anak berkebutuhan khusus (autism, add/adhd, down syndrome, tunarungu, tunanetra, slow learning, speechdelay, disleksia). Per kelas 1 siswa - 1 Guru',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 4,
                'title' => 'Point Pembelajaran',
                'desc' => 'Edukasi, activity daily living, motorik, wicara, dan okupasi.',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 5,
                'title' => 'Program',
                'desc' => "Reguler\nFull-day\nOnline",
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 6,
                'title' => 'Kelompok Bermain',
                'desc' => 'Pendidikan Anak Usia Dini dengan rentang usia 2 - 6 tahun. Anak diajak untuk mengikuti pendidikan pra sekolah yang menyenangkan. Menerima ABK',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 7,
                'title' => 'Sekolah Dasar',
                'desc' => 'Memberikan kesempatan kepada ABK untuk belajar bersama-sama dengan anak pada umumnya di kelas yang sama. Usia anak minimal 7 tahun',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 8,
                'title' => 'Program Unggulan KB & SD',
                'desc' => "Kelas terkondisi (3-5 anak)\nKurikulum reguler & inklusi\nAsesmen kemampuan & perkembangan\nTerapi dampingan individu\nGuru agama islam / kristen",
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [

                'id' => 9,
                'title' => 'Fasilitas',
                'desc' => "Ruang kelas nyaman & bersih\nToilet & kamar mandi\nDapur",
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        DB::table('list_desc')->insert($listDescData);

        $infoListData = [
            ['id'=> 1,'title' => 'Visi & Misi', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id'=> 2,'title' => 'Rumah Belajar Anak', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id'=> 3,'title' => 'KB & SD Inklusi Talenta', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
        ];

        DB::table('info_lists')->insert($infoListData);

        $pivotListData = [
            ['information_id' => 1, 'list_desc_id' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['information_id' => 1, 'list_desc_id' => 2, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['information_id' => 2, 'list_desc_id' => 3, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['information_id' => 2, 'list_desc_id' => 4, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['information_id' => 2, 'list_desc_id' => 5, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['information_id' => 3, 'list_desc_id' => 6, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['information_id' => 3, 'list_desc_id' => 7, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['information_id' => 3, 'list_desc_id' => 8, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['information_id' => 3, 'list_desc_id' => 9, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
        ];

        DB::table('pivot_lists')->insert($pivotListData);
    }
}
