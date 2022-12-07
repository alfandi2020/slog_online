<?php

namespace Tests\Feature\Customers;

use Tests\BrowserKitTestCase;
use App\Entities\Networks\Network;
use App\Entities\Customers\Customer;
use App\Entities\References\Reference;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ManageCustomersTest extends BrowserKitTestCase
{
    use DatabaseTransactions;

    /** @test */
    public function accouting_can_add_new_customer()
    {
        $accountingUser = $this->loginAsAccounting();
        $comodity = factory(Reference::class, 'comodity')->create();
        $network = $accountingUser->network;
        $this->visit(route('customers.create'));

        $this->submitForm(trans('customer.create'), [
            'comodity_id' => $comodity->id,
            'network_id'  => $network->id,
            'code'        => 'cust1',
            'name'        => 'Nama Perusahaan Customer 1',
            'npwp'        => '8888 8888 8888 8888',
            'is_taxed'    => 1,
            'pic[name]'   => 'Nama PIC Customer 1',
            'pic[phone]'  => '081234567890',
            'pic[email]'  => 'pic@example.org',
            'start_date'  => '2017-01-01',
            'address[1]'  => 'Jln. Cibeurerum, Komp. Paledang, No. 20',
            'address[2]'  => 'Bandung',
            'address[3]'  => 'Jawa Barat',
            'category_id' => 3,
        ]);

        $this->seePageIs(route('customers.index'));
        $this->see(trans('customer.created'));

        $this->seeInDatabase('customers', [
            'comodity_id' => $comodity->id,
            'network_id'  => $network->id,
            'account_no'  => substr($network->code, 0, 4).'0001',
            'code'        => 'cust1',
            'name'        => 'Nama Perusahaan Customer 1',
            'npwp'        => '8888 8888 8888 8888',
            'is_taxed'    => 1,
            'pic'         => '{"name":"Nama PIC Customer 1","phone":"081234567890","email":"pic@example.org"}',
            'start_date'  => '2017-01-01',
            'address'     => '{"1":"Jln. Cibeurerum, Komp. Paledang, No. 20","2":"Bandung","3":"Jawa Barat"}',
            'category_id' => 3,
        ]);
    }

    /** @test */
    public function accouting_can_edit_a_customer_data()
    {
        $accountingUser = $this->loginAsAccounting();
        $customer = factory(Customer::class)->create(['network_id' => $accountingUser->network_id]);
        $this->visit(route('customers.edit', $customer->id));

        $this->submitForm(trans('customer.update'), [
            'comodity_id'    => 3, // Seeded Expedisi
            'code'           => 'cust1',
            'name'           => 'Nama Perusahaan Customer 1',
            'npwp'           => '8888 8888 8888 8888',
            'is_taxed'       => 1,
            'pic[name]'      => 'Nama PIC Customer 1',
            'pic[phone]'     => '081234567890',
            'pic[email]'     => 'pic@example.org',
            'pic[name_acc]'  => 'Nama PIC acc Customer 1',
            'pic[phone_acc]' => '081234567890',
            'pic[email_acc]' => 'pic_acc@example.org',
            'pic[name_prc]'  => 'Nama PIC prc Customer 1',
            'pic[phone_prc]' => '081234567890',
            'pic[email_prc]' => 'pic_prc@example.org',
            'start_date'     => '2017-01-01',
            'address[1]'     => 'Jln. Cibeurerum, Komp. Paledang, No. 20',
            'address[2]'     => 'Bandung',
            'address[3]'     => 'Jawa Barat',
            'category_id'    => 3,
        ]);

        $this->seePageIs(route('customers.edit', $customer->id));
        $this->see(trans('customer.updated'));

        $this->seeInDatabase('customers', [
            'comodity_id' => 3, // Seeded Expedisi
            'code'        => 'cust1',
            'name'        => 'Nama Perusahaan Customer 1',
            'npwp'        => '8888 8888 8888 8888',
            'is_taxed'    => 1,
            'pic'         => '{"name":"Nama PIC Customer 1","phone":"081234567890","email":"pic@example.org","name_acc":"Nama PIC acc Customer 1","phone_acc":"081234567890","email_acc":"pic_acc@example.org","name_prc":"Nama PIC prc Customer 1","phone_prc":"081234567890","email_prc":"pic_prc@example.org"}',
            'start_date'  => '2017-01-01',
            'address'     => '{"1":"Jln. Cibeurerum, Komp. Paledang, No. 20","2":"Bandung","3":"Jawa Barat"}',
            'category_id' => 3,
        ]);
    }

    /** @test */
    public function admin_can_see_a_customer_data()
    {
        $accountingUser = $this->loginAsAccounting();
        $customer = factory(Customer::class)->create(['network_id' => $accountingUser->network_id]);
        $this->visit(route('customers.show', $customer->id));
    }

    /** @test */
    public function admin_can_delete_a_customer()
    {
        $accountingUser = $this->loginAsAccounting();
        $customer = factory(Customer::class)->create(['network_id' => $accountingUser->network_id]);
        $this->visit(route('customers.delete', $customer->id));
        $this->press(trans('customer.delete'));
        $this->see(trans('customer.deleted'));

        $this->dontSeeInDatabase('customers', [
            'id' => $customer->id,
        ]);
    }

    /** @test */
    public function admin_can_suspend_a_customer()
    {
        $accountingUser = $this->loginAsAccounting();
        $customer = factory(Customer::class)->create(['network_id' => $accountingUser->network_id]);
        $this->visit(route('customers.edit', $customer->id));

        $this->submitForm(trans('customer.update'), [
            'is_active' => 0,
        ]);

        $this->seePageIs(route('customers.edit', $customer->id));
        $this->see(trans('customer.updated'));

        $this->seeInDatabase('customers', [
            'id'        => $customer->id,
            'is_active' => 0,
        ]);
    }
}
