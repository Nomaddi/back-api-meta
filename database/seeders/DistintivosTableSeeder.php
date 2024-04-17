<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DistintivosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $nombres = [
            'MO VILLACICENCIO',
            'MO CASTILLA',
            'MO GUAMAL',
            'MO ACACIAS',
            'BYS VILLAVICENCIO',
            'BYS CASTILLA',
            'BYS GUAMAL',
            'BYS ACACIAS'
        ];

        foreach ($nombres as $nombre) {
            DB::table('distintivos')->insert([
                'nombre' => $nombre,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}
