<?php

use App\Entities\Networks\Network;
use App\Entities\Users\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $network = factory(Network::class)->create([
            'type_id' => 1,
            'code' => '63000000',
            'name' => 'BAM Kalimantan Selatan',
            'origin_city_id' => 6371,
            'origin_district_id' => null,
        ]);

        factory(User::class)->create([
            'name' => 'Admin BAM',
            'username' => 'admin_bam',
            'email' => 'admin_bam@mail.com',
            'role_id' => 1,
            'network_id' => $network->id,
        ]);

        factory(User::class)->create([
            'name' => 'Akunting Kalsel',
            'username' => 'akunting_kalsel',
            'email' => 'akunting_kalsel@mail.com',
            'role_id' => 2,
            'network_id' => $network->id,
        ]);

        factory(User::class)->create([
            'name' => 'Sales Counter BAM Kalsel',
            'username' => 'sales_counter_kalsel',
            'email' => 'sales_counter_kalsel@gmail.com',
            'role_id' => 3,
            'network_id' => $network->id,
        ]);

        factory(User::class)->create([
            'name' => 'Warehouse BAM Kalsel',
            'username' => 'warehouse_kalsel',
            'email' => 'warehouse_kalsel@gmail.com',
            'role_id' => 4,
            'network_id' => $network->id,
        ]);

        factory(User::class)->create([
            'name' => 'CS BAM Kalsel',
            'username' => 'cs_kalsel',
            'email' => 'cs_kalsel@gmail.com',
            'role_id' => 5,
            'network_id' => $network->id,
        ]);

        factory(User::class)->create([
            'name' => 'Kasir BAM Kalsel',
            'username' => 'cashier_kalsel',
            'email' => 'cashier_kalsel@gmail.com',
            'role_id' => 6,
            'network_id' => $network->id,
        ]);

        factory(User::class)->create([
            'name' => 'Kurir BAM Kalsel',
            'username' => 'courier_kalsel',
            'email' => 'courier_kalsel@gmail.com',
            'role_id' => 7,
            'network_id' => $network->id,
        ]);
    }
}
