<?php

use Illuminate\Database\Seeder;

class CustomerComoditiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $packTypes = [
            ['cat' => 'comodity', 'name' => 'Expedisi'],
            ['cat' => 'comodity', 'name' => 'Farmasi'],
            ['cat' => 'comodity', 'name' => 'Elektronik'],
            ['cat' => 'comodity', 'name' => 'Alat Berat'],
            ['cat' => 'comodity', 'name' => 'Kosmetik'],
            ['cat' => 'comodity', 'name' => 'Variasi'],
            ['cat' => 'comodity', 'name' => 'Pembiayaan'],
            ['cat' => 'comodity', 'name' => 'Rental'],
            ['cat' => 'comodity', 'name' => 'Bank'],
            ['cat' => 'comodity', 'name' => 'Cargo'],
            ['cat' => 'comodity', 'name' => 'Tambang'],
            ['cat' => 'comodity', 'name' => 'Distributior'],
            ['cat' => 'comodity', 'name' => 'Stationary'],
            ['cat' => 'comodity', 'name' => 'Sparepart'],
            ['cat' => 'comodity', 'name' => 'Surveyor'],
        ];

        DB::table('site_references')->insert($packTypes);
    }
}
