<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\Manifests\HandoverSent' => [
            'App\Listeners\ManifestListener@handoverSent',
        ],
        'App\Events\Manifests\HandoverReceived' => [
            'App\Listeners\ManifestListener@handoverReceived',
        ],
        'App\Events\Manifests\DeliverySent' => [
            'App\Listeners\ManifestListener@deliverySent',
        ],
        'App\Events\Manifests\DeliveryReceived' => [
            'App\Listeners\ManifestListener@deliveryReceived',
        ],
        'App\Events\Manifests\ReturnSent' => [
            'App\Listeners\ManifestListener@returnSent',
        ],
        'App\Events\Manifests\ReturnReceived' => [
            'App\Listeners\ManifestListener@returnReceived',
        ],
        'App\Events\Manifests\AccountingSent' => [
            'App\Listeners\ManifestListener@accountingSent',
        ],
        'App\Events\Manifests\AccountingReceived' => [
            'App\Listeners\ManifestListener@accountingReceived',
        ],
        'App\Events\Manifests\ProblemSent' => [
            'App\Listeners\ManifestListener@problemSent',
        ],
        'App\Events\Manifests\ProblemReceived' => [
            'App\Listeners\ManifestListener@problemReceived',
        ],
        'App\Events\Invoices\CashInvoiceSent' => [
            'App\Listeners\InvoiceListener@cashInvoiceSent',
        ],
        'App\Events\Invoices\CashInvoiceReceived' => [
            'App\Listeners\InvoiceListener@cashInvoiceReceived',
        ],
        'App\Events\Invoices\CodInvoiceSent' => [
            'App\Listeners\InvoiceListener@codInvoiceSent',
        ],
        'App\Events\Invoices\CodInvoiceReceived' => [
            'App\Listeners\InvoiceListener@codInvoiceReceived',
        ],
        'App\Events\Rates\Created' => [
            'App\Listeners\Rates\AutoCreateDistrictRate',
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
