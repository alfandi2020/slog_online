@inject('receiptQuery', 'App\Entities\Receipts\ReceiptQuery')

<div class="panel panel-default">
    <div class="panel-heading"><h3 class="panel-title"><i class="fa fa-newspaper-o fa-fw"></i> {{ trans('receipt.latest') }}</h3></div>
    <div class="panel-body">
        {!! Form::open(['method'=>'get','route'=>'receipts.search']) !!}
        <div class="input-group custom-search-form">
            {!! Form::text('query', Request::get('query'), ['class'=>'form-control','required','placeholder' => 'Cari Nomor/Scan Barcode Resi..']) !!}
            <span class="input-group-btn">
                <button class="btn btn-default" type="submit" title="trans('receipt.search')"><i class="fa fa-search"></i></button>
            </span>
        </div>
        {!! Form::close() !!}
    </div>
    <div class="list-group">
        @foreach($receiptQuery->getNetworkLatest() as $receipt)
        <a href="{{ route('receipts.show', $receipt->number) }}" class="list-group-item" title="{{ trans('receipt.show') }} {{ $receipt->number }}">
            <i class="fa fa-{{ $receipt->customer_id ? 'address-book-o' : 'tag' }} fa-fw" title="{{ $receipt->customer_id ? 'Customer' : 'Retail' }}"></i> {{ $receipt->number }}
            <span class="pull-right text-muted small"><em>{{ Date::parse($receipt->created_at)->diffForHumans() }}</em></span>
        </a>
        @endforeach
    </div>
</div>