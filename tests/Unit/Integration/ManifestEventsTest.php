<?php

namespace Tests\Unit\Integration;

use Notification;
use Tests\TestCase;
use App\Entities\Users\User;
use App\Entities\Networks\Network;
use App\Entities\Receipts\Receipt;
use App\Entities\Manifests\Manifest;
use App\Entities\Manifests\Problem as ProblemManifest;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ManifestEventsTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function handover_manifest_sent_and_gives_warehouse_user_notification()
    {
        Notification::fake();

        $networkId = 1;
        $creatorId = 3; // Sales Counter
        $receipt = factory(Receipt::class)->create(['creator_id' => $creatorId, 'network_id' => $networkId]);
        $manifest = factory(Manifest::class, 'handover')->make([
            'creator_id'      => $creatorId,
            'orig_network_id' => $networkId,
            'dest_network_id' => $networkId,
        ]);
        $manifest->addReceipt($receipt);
        $manifest->send();

        $warehouse = User::find(4); // Warehouse

        Notification::assertSentTo(
            $warehouse,
            'App\Notifications\Manifests\HandoverSent',
            function ($notification, $channels) use ($manifest) {
                return $notification->manifest->id === $manifest->id;
            }
        );

        // $this->assertDatabaseHas('notifications',[
        //     'notifiable_id' => $warehouse->id,
        //     'notifiable_type' => 'App\Entities\Users\User',
        //     'type' => 'App\Notifications\Manifests\HandoverSent',
        //     'read_at' => null,
        // ]);
    }

    /** @test */
    public function handover_manifest_received_and_gives_sales_counter_user_notification()
    {
        Notification::fake();

        $networkId = 1;
        $creatorId = 3; // Sales Counter
        $salesCounter = User::find(3);
        $receipt = factory(Receipt::class)->create(['creator_id' => $creatorId, 'network_id' => $networkId]);
        $manifest = factory(Manifest::class, 'handover')->make([
            'creator_id'      => $creatorId,
            'orig_network_id' => $networkId,
            'dest_network_id' => $networkId,
        ]);
        $manifest->addReceipt($receipt);
        $manifest->send();
        $manifest->receive();

        Notification::assertSentTo(
            $salesCounter,
            'App\Notifications\Manifests\HandoverReceived',
            function ($notification, $channels) use ($creatorId) {
                return $notification->manifest->creator_id === $creatorId;
            }
        );

        // $this->assertDatabaseHas('notifications',[
        //     'notifiable_id' => $salesCounter->id,
        //     'notifiable_type' => 'App\Entities\Users\User',
        //     'type' => 'App\Notifications\Manifests\HandoverReceived',
        //     'read_at' => null,
        // ]);
    }

    /** @test */
    public function delivery_manifest_sent_and_gives_destinaton_network_warehouse_and_sales_counter_user_notification()
    {
        Notification::fake();

        $networkId = 1;
        $creatorId = 4; // Warehouse
        $destNetwork = factory(Network::class)->states('province')->create(['origin_city_id' => 6271, 'code' => '62000000']);
        $receipt = factory(Receipt::class)->create(['creator_id' => $creatorId, 'network_id' => $networkId]);
        $manifest = factory(Manifest::class, 'delivery')->make([
            'creator_id'      => $creatorId,
            'orig_network_id' => $networkId,
            'dest_network_id' => $destNetwork->id,
        ]);
        $manifest->addReceipt($receipt);

        $salesCounter = factory(User::class)->states('sales_counter')->create(['network_id' => $destNetwork->id]); // Sales Counter
        $warehouse = factory(User::class)->states('warehouse')->create(['network_id' => $destNetwork->id]); // Warehouse

        $manifest->send();

        Notification::assertSentTo(
            $warehouse,
            'App\Notifications\Manifests\DeliverySent',
            function ($notification, $channels) use ($manifest) {
                return $notification->manifest->id === $manifest->id;
            }
        );

        Notification::assertSentTo(
            $salesCounter,
            'App\Notifications\Manifests\DeliverySent',
            function ($notification, $channels) use ($manifest) {
                return $notification->manifest->id === $manifest->id;
            }
        );

        // $this->assertDatabaseHas('notifications',[
        //     'notifiable_id' => $warehouse->id,
        //     'notifiable_type' => 'App\Entities\Users\User',
        //     'type' => 'App\Notifications\Manifests\DeliverySent',
        //     'read_at' => null,
        // ]);

        // $this->assertDatabaseHas('notifications',[
        //     'notifiable_id' => $salesCounter->id,
        //     'notifiable_type' => 'App\Entities\Users\User',
        //     'type' => 'App\Notifications\Manifests\DeliverySent',
        //     'read_at' => null,
        // ]);
    }

    /** @test */
    public function delivery_manifest_received_and_gives_warehouse_user_notification()
    {
        Notification::fake();

        $networkId = 1;
        $creatorId = 4; // Warehouse
        $warehouse = User::find(4);
        $destNetwork = factory(Network::class)->states('province')->create(['origin_city_id' => 6271, 'code' => '62000000']);
        $receipt = factory(Receipt::class)->create(['creator_id' => $creatorId, 'network_id' => $networkId]);
        $manifest = factory(Manifest::class, 'delivery')->make([
            'creator_id'      => $creatorId,
            'orig_network_id' => $networkId,
            'dest_network_id' => $destNetwork->id,
        ]);
        $manifest->addReceipt($receipt);
        $manifest->send();
        $manifest->receive();

        Notification::assertSentTo(
            $warehouse,
            'App\Notifications\Manifests\DeliveryReceived',
            function ($notification, $channels) use ($creatorId) {
                return $notification->manifest->creator_id === $creatorId;
            }
        );

        // $this->assertDatabaseHas('notifications',[
        //     'notifiable_id' => $warehouse->id,
        //     'notifiable_type' => 'App\Entities\Users\User',
        //     'type' => 'App\Notifications\Manifests\DeliveryReceived',
        //     'read_at' => null,
        // ]);
    }

    /** @test */
    public function return_manifest_sent_and_gives_destinaton_network_customer_service_user_notification()
    {
        Notification::fake();

        $networkId = 1;
        $creatorId = 5; // Warehouse
        $destNetwork = factory(Network::class)->states('province')->create(['origin_city_id' => 6271, 'code' => '62000000']);
        $receipt = factory(Receipt::class)->create(['creator_id' => $creatorId, 'network_id' => $destNetwork->id, 'status_code' => 'dl']);
        $manifest = factory(Manifest::class, 'return')->make([
            'creator_id'      => $creatorId,
            'orig_network_id' => $networkId,
            'dest_network_id' => $destNetwork->id,
        ]);
        $manifest->addReceipt($receipt);

        $customerServices = factory(User::class, 2)->states('customer_service')->create(['network_id' => $destNetwork->id]); // Customer Service

        $manifest->send();

        Notification::assertSentTo(
            $customerServices[0],
            'App\Notifications\Manifests\ReturnSent',
            function ($notification, $channels) use ($manifest) {
                return $notification->manifest->id === $manifest->id;
            }
        );

        Notification::assertSentTo(
            $customerServices[0],
            'App\Notifications\Manifests\ReturnSent',
            function ($notification, $channels) use ($manifest) {
                return $notification->manifest->id === $manifest->id;
            }
        );

        // $this->assertDatabaseHas('notifications',[
        //     'notifiable_id' => $customerServices[0]->id,
        //     'notifiable_type' => 'App\Entities\Users\User',
        //     'type' => 'App\Notifications\Manifests\ReturnSent',
        //     'read_at' => null,
        // ]);

        // $this->assertDatabaseHas('notifications',[
        //     'notifiable_id' => $customerServices[0]->id,
        //     'notifiable_type' => 'App\Entities\Users\User',
        //     'type' => 'App\Notifications\Manifests\ReturnSent',
        //     'read_at' => null,
        // ]);
    }

    /** @test */
    public function return_manifest_received_and_gives_customer_service_user_notification()
    {
        Notification::fake();

        $networkId = 1;
        $creatorId = 4; // Warehouse
        $warehouse = User::find(4);
        $destNetwork = factory(Network::class)->states('province')->create(['origin_city_id' => 6271, 'code' => '62000000']);
        $receipt = factory(Receipt::class)->create(['creator_id' => $creatorId, 'network_id' => $destNetwork->id, 'status_code' => 'dl']);
        $manifest = factory(Manifest::class, 'return')->make([
            'creator_id'      => $creatorId,
            'orig_network_id' => $networkId,
            'dest_network_id' => $destNetwork->id,
        ]);
        $manifest->addReceipt($receipt);
        $manifest->send();
        $manifest->receive();

        Notification::assertSentTo(
            $warehouse,
            'App\Notifications\Manifests\ReturnReceived',
            function ($notification, $channels) use ($creatorId) {
                return $notification->manifest->creator_id === $creatorId;
            }
        );

        // $this->assertDatabaseHas('notifications',[
        //     'notifiable_id' => $warehouse->id,
        //     'notifiable_type' => 'App\Entities\Users\User',
        //     'type' => 'App\Notifications\Manifests\ReturnReceived',
        //     'read_at' => null,
        // ]);
    }

    /** @test */
    public function accounting_manifest_sent_and_gives_accounting_user_notification()
    {
        Notification::fake();

        $networkId = 1;
        $creatorId = 5; // Customer Service
        $receipt = factory(Receipt::class)->create(['creator_id' => $creatorId, 'network_id' => $networkId, 'status_code' => 'rt']);
        $manifest = factory(Manifest::class, 'accounting')->make([
            'creator_id'      => $creatorId,
            'orig_network_id' => $networkId,
            'dest_network_id' => $networkId,
        ]);
        $manifest->addReceipt($receipt);
        $accounting = User::find(2);
        $manifest->send();

        $accounting = User::find(2); // Warehouse

        Notification::assertSentTo(
            $accounting,
            'App\Notifications\Manifests\AccountingSent',
            function ($notification, $channels) use ($manifest) {
                return $notification->manifest->id === $manifest->id;
            }
        );

        // $this->assertDatabaseHas('notifications',[
        //     'notifiable_id' => $accounting->id,
        //     'notifiable_type' => 'App\Entities\Users\User',
        //     'type' => 'App\Notifications\Manifests\AccountingSent',
        //     'read_at' => null,
        // ]);
    }

    /** @test */
    public function accounting_manifest_received_and_gives_customer_service_user_notification()
    {
        Notification::fake();

        $networkId = 1;
        $creatorId = 5; // Sales Counter
        $customerService = User::find(5);
        $receipt = factory(Receipt::class)->create(['creator_id' => $creatorId, 'network_id' => $networkId, 'status_code' => 'rt']);
        $manifest = factory(Manifest::class, 'accounting')->make([
            'creator_id'      => $creatorId,
            'orig_network_id' => $networkId,
            'dest_network_id' => $networkId,
        ]);
        $manifest->addReceipt($receipt);
        $manifest->send();
        $manifest->receive();

        Notification::assertSentTo(
            $customerService,
            'App\Notifications\Manifests\AccountingReceived',
            function ($notification, $channels) use ($creatorId) {
                return $notification->manifest->creator_id === $creatorId;
            }
        );

        // $this->assertDatabaseHas('notifications',[
        //     'notifiable_id' => $customerService->id,
        //     'notifiable_type' => 'App\Entities\Users\User',
        //     'type' => 'App\Notifications\Manifests\AccountingReceived',
        //     'read_at' => null,
        // ]);
    }

    /** @test */
    public function receipt_problem_manifest_sent_and_gives_handler_user_notification()
    {
        Notification::fake();

        $creatorId = 2; // Accounting
        $receipt = factory(Receipt::class)->create(['status_code' => 'no']);
        // Manifest to CS User
        $manifest = factory(ProblemManifest::class)->make(['handler_id' => 5]);
        $manifest->addReceipt($receipt);
        $manifest->send();

        $customerService = User::find(5); // Customer Service

        Notification::assertSentTo(
            $customerService,
            'App\Notifications\Manifests\ProblemSent',
            function ($notification, $channels) use ($manifest) {
                return $notification->manifest->id === $manifest->id;
            }
        );

        // $this->assertDatabaseHas('notifications',[
        //     'notifiable_id' => $customerService->id,
        //     'notifiable_type' => 'App\Entities\Users\User',
        //     'type' => 'App\Notifications\Manifests\ProblemSent',
        //     'read_at' => null,
        // ]);
    }

    /** @test */
    public function receipt_problem_manifest_received_and_gives_creator_user_notification()
    {
        Notification::fake();

        $creatorId = 2; // Accounting
        $receipt = factory(Receipt::class)->create(['status_code' => 'no']);
        // Manifest to CS User
        $manifest = factory(ProblemManifest::class)->make(['handler_id' => 5]);
        $manifest->addReceipt($receipt);
        $manifest->send();
        $this->actingAs(User::find(5)); // CS User
        $manifest->receive();

        $accounting = User::find(2);

        Notification::assertSentTo(
            $accounting,
            'App\Notifications\Manifests\ProblemReceived',
            function ($notification, $channels) use ($creatorId) {
                return $notification->manifest->creator_id === $creatorId;
            }
        );

        // $this->assertDatabaseHas('notifications',[
        //     'notifiable_id' => $accounting->id,
        //     'notifiable_type' => 'App\Entities\Users\User',
        //     'type' => 'App\Notifications\Manifests\ProblemReceived',
        //     'read_at' => null,
        // ]);
    }

}
