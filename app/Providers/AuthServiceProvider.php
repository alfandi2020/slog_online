<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Entities\Services\Pickup'    => 'App\Policies\Services\PickupPolicy',
        'App\Entities\Manifests\Manifest' => 'App\Policies\ManifestPolicy',
        'App\Entities\Networks\Network'   => 'App\Policies\NetworkPolicy',
        'App\Entities\Customers\Customer' => 'App\Policies\CustomerPolicy',
        'App\Entities\Receipts\Receipt'   => 'App\Policies\ReceiptPolicy',
        'App\Entities\Services\Rate'      => 'App\Policies\RatePolicy',
        'App\Entities\Invoices\Invoice'   => 'App\Policies\InvoicePolicy',
        'App\Entities\Invoices\Cash'      => 'App\Policies\CashInvoicePolicy',
        'App\Entities\Invoices\Cod'       => 'App\Policies\CodInvoicePolicy',
        'App\Entities\Users\User'         => 'App\Policies\UserPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('create_receipt', function ($user) {
            if (!in_array($user->role_id, [1, 3, 4])) {
                return false;
            }

            return true;
        });

        Gate::define('create_customer_receipt', function ($user) {
            if (!in_array($user->role_id, [1, 3, 4])) {
                return false;
            }

            return true;
        });

        // Driver bisa POD mandiri
        Gate::define('pod_by_receipt', function ($user) {
            if ($user->role_id == 7) {
                return true;
            }
        });
    }
}
