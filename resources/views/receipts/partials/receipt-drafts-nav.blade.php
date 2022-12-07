<ul class="nav nav-tabs receipt-drafts-list">
    @foreach(ReceiptCollection::content() as $key => $content)
        <?php $active = ($receipt->receiptKey == $key) ? 'class=active' : '' ?>
        <li {{ $active }} role="presentation">
            <a href="{{ route('receipts.draft', $key) }}">
                {{ $content->type }} - {{ $key }}
                {{-- {{ $content->rate->orig_city_id }} &raquo; {{ $content->rate->dest_city_id }} --}}
                {!! FormField::delete(['route'=>['receipts.remove-receipt', $key]], 'x', [
                    'class'=>'pull-right btn-link remove-receipt-draft',
                    'style' => 'margin: -4px -7px 0 0px;',
                    'id' => 'receipt-' . $key . '-remove',
                    'title' => trans('receipt.draft_delete')
                ]) !!}
            </a>
        </li>
    @endforeach
</ul><!-- Tab panes -->