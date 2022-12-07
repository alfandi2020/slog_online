<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(RegionsTableSeeder::class);
        $this->call(UsersTableSeeder::class);
        $this->call(PackageTypesSeeder::class);
        $this->call(CustomerComoditiesSeeder::class);
        $this->call(PaymentMethodsSeeder::class);
    }
}
