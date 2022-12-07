@inject('reference', 'App\Entities\References\Reference')

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading"><h3 class="panel-title">{{ trans('receipt.add_item') }}</h3></div>
            <table class="table">
                <thead>
                    <th class="col-md-1">{{ trans('receipt.weight') }}</th>
                    <th class="col-md-1">{{ trans('receipt.length') }}</th>
                    <th class="col-md-1">{{ trans('receipt.width') }}</th>
                    <th class="col-md-1">{{ trans('receipt.height') }}</th>
                    <th class="col-md-2">{{ trans('receipt.pack_type') }}</th>
                    <th class="col-md-2">{{ trans('app.notes') }}</th>
                    <th class="col-md-1">{{ trans('app.action') }}</th>
                </thead>
                <tbody>
                    {!! Form::open(['route'=>['receipts.draft-items', $receipt->receiptKey]]) !!}
                    <tr>
                        <td>{!! FormField::text('new_item_weight', ['label' => false, 'addon' => ['after' => 'Kg'], 'class' => 'text-center']) !!}</td>
                        <td>{!! FormField::text('new_item_length', ['label' => false, 'addon' => ['after' => 'cm'], 'class' => 'text-center']) !!}</td>
                        <td>{!! FormField::text('new_item_width', ['label' => false, 'addon' => ['after' => 'cm'], 'class' => 'text-center']) !!}</td>
                        <td>{!! FormField::text('new_item_height', ['label' => false, 'addon' => ['after' => 'cm'], 'class' => 'text-center']) !!}</td>
                        <td>{!! FormField::radios('new_item_type_id', $packTypes = $reference::whereCat('pack_type')->pluck('name','id')->all(), ['label' => false, 'placeholder' => false, 'value' => 1]) !!}</td>
                        <td>{!! FormField::text('new_item_notes', ['label' => false]) !!}</td>
                        <td>{!! Form::submit(trans('receipt.add_item'), ['class'=>'btn btn-primary btn-sm']) !!}</td>
                    </tr>
                    {!! Form::close() !!}
                </tbody>
            </table>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading"><h3 class="panel-title">{{ trans('receipt.items') }}</h3></div>
            <table class="table">
                <thead>
                    <th class="text-center ">{{ trans('receipt.item_no') }}</th>
                    <th class="text-center col-md-1">{{ trans('receipt.weight') }}</th>
                    <th class="text-center col-md-1">{{ trans('receipt.length') }}</th>
                    <th class="text-center col-md-1">{{ trans('receipt.width') }}</th>
                    <th class="text-center col-md-1">{{ trans('receipt.height') }}</th>
                    <th class="text-center col-md-1">{{ trans('receipt.volume') }}</th>
                    <th class="text-center col-md-1">{{ trans('receipt.volumetric_weight') }}</th>
                    <th class="text-center col-md-1">{{ trans('receipt.weight') }}</th>
                    <th class="text-center col-md-1">{{ trans('receipt.pack_type') }}</th>
                    <th class="col-md-2">{{ trans('app.notes') }}</th>
                    <th class="col-md-1">{{ trans('app.action') }}</th>
                </thead>
                <tbody>
                    <?php $no = 1 ?>
                    @foreach($receipt->items() as $key => $item)
                        <tr class="{{ ($item->getVolumetricWeight() > $item->weight) ? 'info' : '' }}">
                            <td class="text-center"><?php echo str_pad($no, 3, "0", STR_PAD_LEFT); $no++ ?></td>
                            {!! Form::open(['route' => ['receipts.draft-items-update', $receipt->receiptKey, $key], 'method' => 'patch']) !!}
                            <td>{!! Form::text("weight[$key]", $item->weight, ['class'=>'form-control text-center']) !!}</td>
                            <td>{!! Form::text("length[$key]", $item->length,['class'=>'form-control text-center']) !!}</td>
                            <td>{!! Form::text("width[$key]", $item->width,['class'=>'form-control text-center']) !!}</td>
                            <td>{!! Form::text("height[$key]", $item->height,['class'=>'form-control text-center']) !!}</td>
                            <td><span class="form-control text-center" readonly>{{ formatDecimal($item->getVolume()) }}</span></td>
                            <td><span class="form-control text-center" readonly>{{ formatDecimal($item->getVolumetricWeight()) }}</span></td>
                            <td><span class="form-control text-center" readonly>{{ formatDecimal($item->getChargedWeight()) }}</span></td>
                            <td>{!! FormField::radios("type_id[$key]", $packTypes, ['label' => false, 'placeholder' => false, 'value' => $item->type_id]) !!}</td>
                            <td>{!! Form::text("notes[$key]", $item->notes,['class'=>'form-control']) !!}</td>
                            <td>
                                {!! Form::submit(trans('app.update'), ['class'=>'btn btn-warning btn-xs']) !!}
                                {!! Form::close() !!}
                                {!! FormField::delete(['route'=>['receipts.draft-items-delete', $receipt->receiptKey, $key], 'class'=>'remove-items'], 'x', ['title'=>'Hapus Item','class'=>'btn btn-danger btn-xs']) !!}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td class="text-center" colspan="3">
                            {{ trans('receipt.volumetric_devider') }}
                            <p class="lead text-success">{{ $receipt->volumetric_devider }}</p>
                        </td>
                        <td class="text-center" colspan="2">
                            Total Jml <p class="lead text-success">{{ $receipt->itemsCount() }} Dus</p>
                        </td>
                        <td class="text-center" colspan="3">
                            Total Berat <p class="lead text-success">{{ formatDecimal($receipt->getChargedWeightSum()) }} Kg</p>
                        </td>
                        <td class="text-center" colspan="3">
                            Total Berat Pembulatan <p class="lead text-success">{{ $receipt->getChargedWeight() }} Kg</p>
                        </td>
                    </tr>
                </tfoot>
            </table>
            <div class="panel-footer">
                {!! link_to_route('receipts.draft', trans('receipt.project_data_entry'), [$receipt->receiptKey, 'step' => 2], ['class'=>'btn btn-success']) !!}
            </div>
        </div>
    </div>
</div>

@section('script')
<script>
(function() {
    $(".empty-items").submit(function() {
        return confirm('Anda yakin ingin mengosongkan Receipt Items?');
    });
})();
</script>
@endsection