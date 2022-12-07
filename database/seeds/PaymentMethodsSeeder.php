<?php

use Illuminate\Database\Seeder;

class PaymentMethodsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $peymentMethods = [
            [
                'name' => 'Tunai',
                'description' => 'Transaksi dibayar tunai.',
            ], [
                'name' => 'Bank ABC',
                'description' => "Rek. 123 123 1234567\nPT ABC",
            ],
        ];
        DB::table('payment_methods')->insert($peymentMethods);
    }
}
