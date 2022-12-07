<?php

namespace Tests\Feature\Invoices\Cod;

use App\Entities\Invoices\Cod as CodInvoice;
use App\Entities\Receipts\Receipt;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\BrowserKitTestCase;

abstract class CodInvoicesTestCase extends BrowserKitTestCase
{
    use DatabaseTransactions;

    protected function createCodInvoiceWithReceipts($numberOfReceipts = 1, $overrides = [])
    {
        $overrides = array_merge([
            'network_id' => 1, // Seeded BAM Kalsel
        ], $overrides);

        $invoice = factory(CodInvoice::class)->create($overrides);

        $invoice->assignReceipt(
            factory(Receipt::class, $numberOfReceipts)->states('delivered_cod')->make()
        );

        return $invoice;
    }
}
