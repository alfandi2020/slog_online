@inject('reference', 'App\Entities\References\Reference')
<div class="panel panel-default">
    <div class="panel-body">
        <div class="row">
            <fieldset class="col-md-7">
                {!! FormField::text('pickup_time', [
                    'label' => trans('receipt.pickup_time'),
                    'class' => 'time-select',
                    'value' => $receipt->pickup_time ? $receipt->pickup_time->format('Y-m-d H:i') : date('Y-m-d H:i'),
                    'required' => true,
                ]) !!}
                {!! FormField::radios('pack_type_id', $reference::whereCat('pack_type')->pluck('name','id')->all(), [
                    'label' => trans('receipt.pack_type'),
                    'list_style' => 'unstyled',
                    'value' => $receipt->pack_type_id ?: 1,
                    'required' => true,
                ]) !!}
                {!! FormField::text('charged_weight', [
                    'label' => trans('receipt.charged_weight'),
                    'addon' => ['after' => 'Kg'],
                    'class' => 'text-right',
                    'value' => $receipt->getChargedWeight() ?: $receipt->charged_weight ?: 1,
                    'required' => true,
                ]) !!}
                {!! FormField::text('pcs_count', [
                    'label' => trans('receipt.pcs_count'),
                    'addon' => ['after' => 'Koli'],
                    'class' => 'text-right',
                    'value' => $receipt->pcs_count ?: $receipt->itemsCount() ?: 1,
                    'required' => true,
                ]) !!}
                {!! FormField::text('items_count', [
                    'label' => trans('receipt.items_count'),
                    'addon' => ['after' => 'Dus'],
                    'class' => 'text-right',
                    'value' => $receipt->itemsCount() ?: $receipt->items_count ?: 1,
                    'required' => true,
                ]) !!}
                {!! FormField::textarea('pack_content', [
                    'label' => trans('receipt.pack_content'),
                    'class' => 'text-right',
                    'value' => $receipt->pack_content,
                    'rows' => 2
                ]) !!}
            </fieldset>
            <fieldset class="col-md-5">
                {!! FormField::price('base_charge', [
                    'label' => trans('receipt.base_charge'),
                    'value' => $receipt->base_charge ?: 0,
                ]) !!}
                {!! FormField::price('discount', [
                    'label' => trans('receipt.discount'),
                    'value' => $receipt->discount ?: 0,
                ]) !!}
                {!! FormField::price('packing_cost', [
                    'label' => trans('receipt.packing_cost'),
                    'value' => $receipt->packing_cost ?: 0,
                ]) !!}
                {!! FormField::price('insurance_cost', [
                    'label' => trans('receipt.insurance_cost'),
                    'value' => $receipt->insurance_cost ?: 0,
                ]) !!}
                {!! FormField::price('add_cost', [
                    'label' => trans('receipt.add_cost'),
                    'value' => $receipt->add_cost ?: 0,
                ]) !!}
                {!! FormField::price('admin_fee', [
                    'label' => trans('receipt.admin_fee'),
                    'value' => $receipt->admin_fee ?: 0,
                ]) !!}
            </fieldset>
        </div>
    </div>
</div>