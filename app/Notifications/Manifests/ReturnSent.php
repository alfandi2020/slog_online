<?php

namespace App\Notifications\Manifests;

use App\Entities\Manifests\Manifest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReturnSent extends Notification
{
    use Queueable;

    public $manifest;

    public function __construct(Manifest $manifest)
    {
        $this->manifest = $manifest;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'title' => $this->manifest->number,
            'url' => route('manifests.handovers.show', $this->manifest->number),
        ];
    }
}
