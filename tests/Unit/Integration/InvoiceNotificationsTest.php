<?php

namespace Tests\Unit\Integration;

use App\Entities\Invoices\Cash as CashInvoice;
use App\Entities\Invoices\Cod as CodInvoice;
use App\Entities\Receipts\Receipt;
use App\Entities\Users\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Notification;
use Tests\TestCase;

class InvoiceNotificationsTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function cash_invoice_sent_and_gives_cashier_users_notification()
    {
        Notification::fake();

        $receipt = factory(Receipt::class)->states('cash')->create();
        $invoice = factory(CashInvoice::class)->create();

        $invoice->assignReceipt($receipt);

        $invoice->send();

        $cashier = User::find(6); // Cashier

        Notification::assertSentTo(
            $cashier,
            'App\Notifications\Invoices\CashInvoiceSent',
            function ($notification, $channels) use ($invoice) {
                return $notification->invoice->id === $invoice->id;
            }
        );

        // $this->assertDatabaseHas('notifications',[
        //     'notifiable_id' => $cashier->id,
        //     'notifiable_type' => 'App\Entities\Users\User',
        //     'type' => 'App\Notifications\Invoices\CashInvoiceSent',
        //     'read_at' => null,
        // ]);
    }

    /** @test */
    public function cash_invoice_verfied_and_gives_sales_counter_user_notification()
    {
        Notification::fake();

        $receipt = factory(Receipt::class)->states('cash')->create();
        $invoice = factory(CashInvoice::class)->create();

        $invoice->assignReceipt($receipt);

        $invoice->send();
        $invoice->verify();

        $salesCounter = User::find(3); // Sales Counter

        Notification::assertSentTo(
            $salesCounter,
            'App\Notifications\Invoices\CashInvoiceReceived',
            function ($notification, $channels) use ($invoice) {
                return $notification->invoice->id === $invoice->id;
            }
        );

        // $this->assertDatabaseHas('notifications',[
        //     'notifiable_id' => $salesCounter->id,
        //     'notifiable_type' => 'App\Entities\Users\User',
        //     'type' => 'App\Notifications\Invoices\CashInvoiceReceived',
        //     'read_at' => null,
        // ]);
    }

    /** @test */
    public function cod_invoice_sent_and_gives_cashier_users_notification()
    {
        Notification::fake();

        $receipt = factory(Receipt::class)->states('delivered_cod')->create();
        $invoice = factory(CodInvoice::class)->create();

        $invoice->assignReceipt($receipt);

        $invoice->send();

        $cashier = User::find(6); // Cashier

        Notification::assertSentTo(
            $cashier,
            'App\Notifications\Invoices\CodInvoiceSent',
            function ($notification, $channels) use ($invoice) {
                return $notification->invoice->id === $invoice->id;
            }
        );

        // $this->assertDatabaseHas('notifications',[
        //     'notifiable_id' => $cashier->id,
        //     'notifiable_type' => 'App\Entities\Users\User',
        //     'type' => 'App\Notifications\Invoices\CodInvoiceSent',
        //     'read_at' => null,
        // ]);
    }

    /** @test */
    public function cod_invoice_verfied_and_gives_sales_counter_user_notification()
    {
        Notification::fake();

        $receipt = factory(Receipt::class)->states('delivered_cod')->create();
        $invoice = factory(CodInvoice::class)->create();

        $invoice->assignReceipt($receipt);

        $invoice->send();
        $invoice->verify();

        $customerService = User::find(5); // Customer Service

        Notification::assertSentTo(
            $customerService,
            'App\Notifications\Invoices\CodInvoiceReceived',
            function ($notification, $channels) use ($invoice) {
                return $notification->invoice->id === $invoice->id;
            }
        );

        // $this->assertDatabaseHas('notifications',[
        //     'notifiable_id' => $customerService->id,
        //     'notifiable_type' => 'App\Entities\Users\User',
        //     'type' => 'App\Notifications\Invoices\CodInvoiceReceived',
        //     'read_at' => null,
        // ]);
    }
}
