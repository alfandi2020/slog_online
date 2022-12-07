<div class="row">
    <div class="col-sm-4">
        @include('receipts.partials.receipt-data')
        @include('receipts.partials.receipt-customer')
    </div>
    <div class="col-sm-4">
        @include('receipts.partials.receipt-package')
        @include('receipts.partials.receipt-costs-detail')
    </div>
    <div class="col-sm-4">
        @include('receipts.partials.receipt-consignor')
        @include('receipts.partials.receipt-consignee')
        {!! FormField::formButton(['route'=>['receipts.draft-project-store', $receipt->receiptKey]], trans('receipt.save'), [
            'class' => 'btn btn-success btn-lg',
        ]) !!}
        {{ link_to_route('receipts.draft', trans('app.back'), [$receipt->receiptKey, 'step' => 1], ['class' => 'btn btn-default btn-lg']) }}
    </div>
</div>
