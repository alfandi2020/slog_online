<?php

namespace Tests\Feature\Receipts;

use App\Entities\Customers\Customer;
use App\Entities\Receipts\Receipt;
use App\Entities\Services\Rate;
use App\Services\ReceiptCollection;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\BrowserKitTestCase;

class GetCostsTest extends BrowserKitTestCase
{
    use DatabaseTransactions;

    /** @test */
    public function user_can_check_base_rate_costs()
    {
        $this->loginAsUser();
        $rate = factory(Rate::class, 'city_to_city')->create();
        $this->visit(route('pages.get-costs'));

        $this->submitForm(trans('service.get_costs'), [
            'orig_city_id' => $rate->orig_city_id,
            'dest_city_id' => $rate->dest_city_id,
            'charged_weight' => 2,
        ]);

        // $this->seePageIs(route('pages.get-costs'));
        $this->see($rate->service());
        $this->see(formatRp($rate->cost));
        $this->dontSee(trans('rate.not_found'));
    }

    /** @test */
    public function get_costs_page_doesnt_show_customer_special_rate()
    {
        $this->loginAsUser();
        $rate1 = factory(Rate::class, 'city_to_city')->create(['rate_kg' => 10000, 'rate_pc' => null]);
        $customer = factory(Customer::class)->create();
        $rate2 = factory(Rate::class, 'city_to_city')->create([
            'customer_id' => $customer->id,
            'service_id' => $rate1->service_id,
            'orig_city_id' => $rate1->orig_city_id,
            'rate_kg' => 8000,
        ]);

        $this->visit(route('pages.get-costs'));

        $this->submitForm(trans('service.get_costs'), [
            'orig_city_id' => $rate1->orig_city_id,
            'dest_city_id' => $rate1->dest_city_id,
            'charged_weight' => 2,
        ]);

        // $this->seePageIs(route('pages.get-costs'));
        $this->see($rate1->service());
        $this->see(formatRp(20000));
        $this->dontSee(formatRp(16000));
        $this->dontSee(trans('rate.not_found'));

        // $this->assertEquals($rate1->service(), $rate2->service());
    }
}
