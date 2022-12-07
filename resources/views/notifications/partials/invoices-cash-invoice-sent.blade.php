<?php $isPageContent = $isPageContent ?? false ?>
@if ($isPageContent)
    <div>
        {!! html_link_to_route('invoices.cash.show', $notification->data['title'], [
            $notification->data['invoice_id'], 'source'=>'notif', 'notif_id' => $notification->id
        ], [
            'icon' => 'upload',
            'style' => 'text-decoration:none',
            'title' => 'Lihat detail Invoice Tunai ' . $notification->data['title'],
        ]) !!}
        <span class="pull-right text-muted small">{{ $notification->created_at->diffForHumans() }}</span>
    </div>
    <p>
        Invoice Tunai ({{ $notification->data['title'] }}) telah dikirim
        oleh <strong>{{App\Entities\Users\User::findOrFail($notification->data['sender_id'])->name }}</strong>
        ke Kasir
    </p>
@else
<a style="white-space:normal" href="{{ route('invoices.cash.show', [$notification->data['invoice_id'], 'source'=>'notif', 'notif_id' => $notification->id]) }}">
    <div>
        <i class="fa fa-upload fa-fw"></i> {{ $notification->data['title'] }}
        <span class="pull-right text-muted small">{{ $notification->created_at->diffForHumans() }}</span>
    </div>
    <p class="small">
        Invoice Tunai ({{ $notification->data['title'] }}) telah dikirim
        oleh <strong>{{App\Entities\Users\User::findOrFail($notification->data['sender_id'])->name }}</strong>
        ke Kasir
    </p>
</a>
@endif