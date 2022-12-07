<?php

use Illuminate\Database\Seeder;

class PackageTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $packTypes = [
            ['cat' => 'pack_type', 'name' => 'Paket'],
            ['cat' => 'pack_type', 'name' => 'Dokumen'],
        ];

        DB::table('site_references')->insert($packTypes);
    }
}
