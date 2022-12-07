<nav class="navbar navbar-default">
    <div class="container-fluid">
        <ul class="nav navbar-nav receipt-draft-steps">
            <li class="{{ in_array(request('step'), [null, 1]) ? 'active' : '' }}">
                <a href="{{ route('receipts.draft', [$receipt->receiptKey, 'step' => 1]) }}">
                    <span class="badge">1</span> Rincian Barang
                </a>
            </li>
            <li class="{{ request('step') == 2 ? 'active' : '' }}">
                <a href="{{ route('receipts.draft', [$receipt->receiptKey, 'step' => 2]) }}">
                    <span class="badge">2</span> Data Resi
                </a>
            </li>
            <li class="{{ request('step') == 3 ? 'active' : '' }}">
                <a href="{{ route('receipts.draft', [$receipt->receiptKey, 'step' => 3]) }}">
                    <span class="badge">3</span> Review Resi
                </a>
            </li>
        </ul>
        <div class="navbar-right">
            <h4 style="margin: 15px 10px;">
                {!! trans('receipt.origin_show', [
                    'origin' => $receipt->originName()
                ]) !!}
            </h4>
        </div>
    </div>
</nav>