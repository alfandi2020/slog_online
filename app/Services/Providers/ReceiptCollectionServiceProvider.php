<?php

namespace App\Services\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\ReceiptCollection;

class ReceiptCollectionServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        // $this->app->singleton('receiptCollection', function($app) {
        //     return new ReceiptCollection();
        // });
        $this->app->alias(ReceiptCollection::class, 'receiptCollection');
    }
}
