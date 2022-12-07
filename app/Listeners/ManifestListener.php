<?php

namespace App\Listeners;

use App\Entities\Users\User;
use App\Events\Manifests\AccountingReceived;
use App\Events\Manifests\AccountingSent;
use App\Events\Manifests\ProblemReceived;
use App\Events\Manifests\ProblemSent;
use App\Events\Manifests\DeliveryReceived;
use App\Events\Manifests\DeliverySent;
use App\Events\Manifests\HandoverReceived;
use App\Events\Manifests\HandoverSent;
use App\Events\Manifests\ReturnReceived;
use App\Events\Manifests\ReturnSent;
use App\Notifications\Manifests\AccountingSent as AccountingSentNotif;
use App\Notifications\Manifests\AccountingReceived as AccountingReceivedNotif;
use App\Notifications\Manifests\ProblemSent as ProblemSentNotif;
use App\Notifications\Manifests\ProblemReceived as ProblemReceivedNotif;
use App\Notifications\Manifests\HandoverSent as HandoverSentNotif;
use App\Notifications\Manifests\HandoverReceived as HandoverReceivedNotif;
use App\Notifications\Manifests\DeliverySent as DeliverySentNotif;
use App\Notifications\Manifests\DeliveryReceived as DeliveryReceivedNotif;
use App\Notifications\Manifests\ReturnSent as ReturnSentNotif;
use App\Notifications\Manifests\ReturnReceived as ReturnReceivedNotif;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Notification;

class ManifestListener
{
    public function handoverSent(HandoverSent $event)
    {
        $manifest = $event->manifest;
        $users = User::where(function($query) use ($manifest) {
            $query->where('network_id', $manifest->dest_network_id);
            $query->where('role_id', 4);
        })
        ->get();

        Notification::send($users, new HandoverSentNotif($manifest));
    }

    public function handoverReceived(HandoverReceived $event)
    {
        $manifest = $event->manifest;
        $user = $manifest->creator;

        $user->notify(new HandoverReceivedNotif($manifest));
    }

    public function deliverySent(DeliverySent $event)
    {
        $manifest = $event->manifest;
        $users = User::where(function($query) use ($manifest) {
            $query->where('network_id', $manifest->dest_network_id);
            $query->whereIn('role_id', [3,4]);
        })
        ->get();

        Notification::send($users, new DeliverySentNotif($manifest));
    }

    public function deliveryReceived(DeliveryReceived $event)
    {
        $manifest = $event->manifest;
        $user = $manifest->creator;

        $user->notify(new DeliveryReceivedNotif($manifest));
    }

    public function returnSent(ReturnSent $event)
    {
        $manifest = $event->manifest;
        $users = User::where(function($query) use ($manifest) {
            $query->where('network_id', $manifest->dest_network_id);
            $query->whereIn('role_id', [5]);
        })
        ->get();

        Notification::send($users, new ReturnSentNotif($manifest));
    }

    public function returnReceived(ReturnReceived $event)
    {
        $manifest = $event->manifest;
        $user = $manifest->creator;

        $user->notify(new ReturnReceivedNotif($manifest));
    }

    public function accountingSent(AccountingSent $event)
    {
        $manifest = $event->manifest;
        $users = User::where(function($query) use ($manifest) {
            $query->where('network_id', $manifest->dest_network_id);
            $query->whereIn('role_id', [2]);
        })
        ->get();

        Notification::send($users, new AccountingSentNotif($manifest));
    }

    public function accountingReceived(AccountingReceived $event)
    {
        $manifest = $event->manifest;
        $user = $manifest->creator;

        $user->notify(new AccountingReceivedNotif($manifest));
    }

    public function problemSent(ProblemSent $event)
    {
        $manifest = $event->manifest;
        $user = $manifest->handler;

        $user->notify(new ProblemSentNotif($manifest));
    }

    public function problemReceived(ProblemReceived $event)
    {
        $manifest = $event->manifest;
        $user = $manifest->creator;

        $user->notify(new ProblemReceivedNotif($manifest));
    }
}
