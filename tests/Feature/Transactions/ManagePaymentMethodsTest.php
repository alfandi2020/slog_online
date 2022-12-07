<?php

namespace Tests\Feature\Transactions;

use App\Entities\Transactions\PaymentMethod;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\BrowserKitTestCase;

class ManagePaymentMethodsTest extends BrowserKitTestCase
{
    use DatabaseTransactions;

    /** @test */
    public function user_can_see_payment_method_list_in_payment_method_index_page()
    {
        $paymentMethod1 = factory(PaymentMethod::class)->create(['name' => 'Testing name', 'description' => 'Testing 123']);
        $paymentMethod2 = factory(PaymentMethod::class)->create(['name' => 'Testing name', 'description' => 'Testing 456']);

        $this->loginAsAdmin();
        $this->visit(route('payment-methods.index'));
        $this->see($paymentMethod1->name);
        $this->see($paymentMethod2->name);
    }

    /** @test */
    public function user_can_create_a_payment_method()
    {
        $this->loginAsAdmin();
        $this->visit(route('payment-methods.index'));

        $this->click(trans('payment_method.create'));
        $this->seePageIs(route('payment-methods.index', ['action' => 'create']));

        $this->type('Payment Method 1', 'name');
        $this->type('Payment Method 1 description', 'description');
        $this->press(trans('payment_method.create'));

        $this->seePageIs(route('payment-methods.index'));

        $this->seeInDatabase('payment_methods', [
            'name' => 'Payment Method 1',
            'description' => 'Payment Method 1 description',
            'is_active' => 1,
        ]);
    }

    /** @test */
    public function user_can_edit_a_payment_method_within_search_query()
    {
        $this->loginAsAdmin();
        $paymentMethod = factory(PaymentMethod::class)->create(['name' => 'Testing 123']);

        $this->visit(route('payment-methods.index', ['q' => '123']));
        $this->click('edit-payment_method-'.$paymentMethod->id);
        $this->seePageIs(route('payment-methods.index', ['action' => 'edit', 'id' => $paymentMethod->id, 'q' => '123']));

        $this->type('Payment Method 1', 'name');
        $this->type('Payment Method 1 description', 'description');
        $this->select(0, 'is_active');
        $this->press(trans('payment_method.update'));

        $this->seePageIs(route('payment-methods.index', ['q' => '123']));

        $this->seeInDatabase('payment_methods', [
            'name' => 'Payment Method 1',
            'description' => 'Payment Method 1 description',
            'is_active' => 0,
        ]);
    }

    /** @test */
    public function user_can_delete_a_payment_method()
    {
        $this->loginAsAdmin();
        $paymentMethod = factory(PaymentMethod::class)->create();

        $this->visit(route('payment-methods.index', [$paymentMethod->id]));
        $this->click('del-payment_method-'.$paymentMethod->id);
        $this->seePageIs(route('payment-methods.index', ['action' => 'delete', 'id' => $paymentMethod->id]));

        $this->seeInDatabase('payment_methods', [
            'id' => $paymentMethod->id,
        ]);

        $this->press(trans('app.delete_confirm_button'));

        $this->dontSeeInDatabase('payment_methods', [
            'id' => $paymentMethod->id,
        ]);
    }
}
