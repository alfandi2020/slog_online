<?php $isPageContent = $isPageContent ?? false ?>
@if ($isPageContent)
    <div>
        {!! html_link_to_route('manifests.deliveries.show', $notification->data['title'], [
            $notification->data['title'], 'source'=>'notif', 'notif_id' => $notification->id
        ], [
            'icon' => 'upload',
            'style' => 'text-decoration:none',
            'title' => 'Lihat detail Manifest ' . $notification->data['title'],
        ]) !!}
        <span class="pull-right text-muted small">{{ $notification->created_at->diffForHumans() }}</span>
    </div>
    <div>Manifest Pengiriman ({{ $notification->data['title'] }}) telah diterima oleh Cabang Penerima</div>
@else
<a style="white-space:normal" href="{{ route('manifests.deliveries.show', [$notification->data['title'], 'source'=>'notif', 'notif_id' => $notification->id]) }}">
    <div>
        <i class="fa fa-upload fa-fw"></i> {{ $notification->data['title'] }}
        <span class="pull-right text-muted small">{{ $notification->created_at->diffForHumans() }}</span>
    </div>
    <div class="small">Manifest Pengiriman ({{ $notification->data['title'] }}) telah diterima oleh Cabang Penerima</div>
</a>
@endif