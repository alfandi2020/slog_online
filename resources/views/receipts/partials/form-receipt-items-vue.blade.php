@inject('reference', 'App\Entities\References\Reference')
<?php
$buttonLinks = [
    [
        'url' => route('receipts.draft', [$receipt->receiptKey, 'step' => 1]),
        'text' => trans('app.back'),
        'class' => 'btn btn-default'
    ],
    [
        'url' => route('receipts.draft', [$receipt->receiptKey, 'step' => 3]),
        'text' => trans('app.next'),
        'class' => 'btn btn-default'
    ],
]; ?>

@include('receipts.partials.receipt-draft-step-nav', compact('buttonLinks'))

<div id="vue-el">
    <!-- <div class="alert alert-success" transition="success" v-if="add_success">Add new option success.</div>
    <div class="alert alert-success" transition="success" v-if="update_success">Update option success.</div> -->
    <options-list :options="options"></options-list>
    <options-new :newOption="newOption" :options="options"></options-new>
</div>

<template id="option-row-template">
<table class="table table-condensed">
    <thead>
        <tr class="row">
            <th class="col-md-1">{{ trans('receipt.weight') }}</th>
            <th class="col-md-1">{{ trans('receipt.length') }}</th>
            <th class="col-md-1">{{ trans('receipt.width') }}</th>
            <th class="col-md-1">{{ trans('receipt.height') }}</th>
            <th class="col-md-1">{{ trans('receipt.volume') }}</th>
            <th class="col-md-1">{{ trans('receipt.volumetric_weight') }}</th>
            <th class="col-md-1">{{ trans('receipt.weight') }}</th>
            <th class="col-md-1">{{ trans('receipt.pack_type') }}</th>
            <th class="col-md-2">{{ trans('app.notes') }}</th>
            <th class="col-md-2">{{ trans('app.action') }}</th>
        </tr>
    </thead>
    <tbody>
        <tr class="row" v-for="option in options">
            <td v-if="!option.inEditMode">@{{ option.weight }}</td>
            <td v-if="!option.inEditMode">@{{ option.length }}</td>
            <td v-if="!option.inEditMode">@{{ option.width }}</td>
            <td v-if="!option.inEditMode">@{{ option.height }}</td>

            <td v-if="!option.inEditMode">@{{ option.volume }}</td>
            <td v-if="!option.inEditMode">{{ trans('receipt.volumetric_weight') }}</td>
            <td v-if="!option.inEditMode">{{ trans('receipt.weight') }}</td>
            <td v-if="!option.inEditMode">@{{ option.pack_type }}</td>
            <td v-if="!option.inEditMode">@{{ option.notes }}</td>
            <td v-if="!option.inEditMode">
                <button class="btn btn-info btn-xs" v-on:click="editForm(option)">Edit</button>
            </td>
            <td v-if="option.inEditMode">{!! FormField::text('weight', ['label' => false,'v-model' => 'option.weight']) !!}</td>
            <td v-if="option.inEditMode">{!! FormField::text('length', ['label' => false,'v-model' => 'option.length']) !!}</td>
            <td v-if="option.inEditMode">{!! FormField::text('width', ['label' => false,'v-model' => 'option.width']) !!}</td>
            <td v-if="option.inEditMode">{!! FormField::text('height', ['label' => false,'v-model' => 'option.height']) !!}</td>
            <td v-if="option.inEditMode">{{ trans('receipt.volume') }}</td>
            <td v-if="option.inEditMode">{{ trans('receipt.volumetric_weight') }}</td>
            <td v-if="option.inEditMode">{{ trans('receipt.weight') }}</td>
            <td v-if="option.inEditMode">{!! FormField::radios('pack_type', $reference::whereCat('pack_type')->pluck('name','id')->all(), ['label' => false, 'placeholder' => false,'v-model' => 'option.pack_type']) !!}</td>
            <td v-if="option.inEditMode">{!! FormField::text('notes', ['label' => false,'v-model' => 'option.notes']) !!}</td>
            <td v-if="option.inEditMode">
                <button class="btn btn-info btn-xs" v-on:click="update(option)">Update</button>
                <button class="btn btn-danger btn-xs" v-on:click="deleteOpt(option)">X</button>
            </td>
        </tr>
    </tbody>
</table>
</template>

<template id="option-new-template">
    <form action="#" method="post" v-on:submit.prevent="addNewOption">
        <table class="table table-condensed">
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
                <tr>
                    <td>{!! FormField::text('weight', ['label' => false, 'addon' => ['after' => 'Kg'], 'class' => 'text-center','v-model' => 'newOption.weight']) !!}</td>
                    <td>{!! FormField::text('length', ['label' => false, 'addon' => ['after' => 'cm'], 'class' => 'text-center','v-model' => 'newOption.length']) !!}</td>
                    <td>{!! FormField::text('width', ['label' => false, 'addon' => ['after' => 'cm'], 'class' => 'text-center','v-model' => 'newOption.width']) !!}</td>
                    <td>{!! FormField::text('height', ['label' => false, 'addon' => ['after' => 'cm'], 'class' => 'text-center','v-model' => 'newOption.height']) !!}</td>
                    <td>{!! FormField::radios('pack_type', $reference::whereCat('pack_type')->pluck('name','id')->all(), ['label' => false, 'placeholder' => false,'v-model' => 'newOption.pack_type']) !!}</td>
                    <td>{!! FormField::text('notes', ['label' => false,'v-model' => 'newOption.notes']) !!}</td>
                    <td>{!! Form::submit('Tambahkan', ['class'=>'btn btn-primary btn-xs']) !!}</td>
                </tr>
            </tbody>
        </table>
    </form>
</template>

@push('ext_js')
    {{-- {!! Html::script(url('js/plugins/vue.js')) !!} --}}
    {!! Html::script(url('https://cdnjs.cloudflare.com/ajax/libs/vue/2.1.10/vue.js')) !!}
    {!! Html::script(url('https://unpkg.com/axios/dist/axios.min.js')) !!}
@endpush

@section('script')
<script>

Vue.component('options-list', {
    data: function() {
        return {
            inEditMode: false
        }
    },
    props: ['option','options'],
    template: '#option-row-template',
    methods: {
        editForm: function(option) {
            option.inEditMode = true;
        },
        update: function(option) {
            axios.patch("{{ route('api.receipts.patch-draft-items', $receipt->receiptKey) }}", option, {
                    headers: {'Authorization': "Bearer " + '{{ auth()->user()->api_token }}'}
                })
                .then((response) => {
                    // console.log(response.data);
                    // option = response.data;
                    // this.options.splice(option.id, 1, response.data);
                    this.options[option.id] =response.data;
                })
                .catch(function (error) {
                    console.log(error);
                });

            option.inEditMode = false;
        },
        cancelEditMode: function(option) {
            option.inEditMode = false;
        },
        deleteOpt: function(option) {
            var confirmBox = confirm('Delete this option?');

            if (confirmBox) {
                // axios.delete('api/options/' + option.id);
                // console.log (this.options);
                // this.options.$remove(option);
                this.options.splice(option.id, 1);
                // console.log (this.options);
                // this.fetchOptions();
            }

            option.inEditMode = false;
        }
    }
});

Vue.component('options-new', {
    data: function() {
        return {
            newOption: {
                weight: '',
                length: '',
                width: '',
                height: '',
                pack_type: '',
                notes: '',
                inEditMode: false,
            }
        }
    },
    props: ['options'],
    template: '#option-new-template',

    methods: {
        addNewOption: function() {
            // New Option Input
            var option = this.newOption;

            // Clear form
            this.newOption = {
                weight: '',
                length: '',
                width: '',
                height: '',
                pack_type: '',
                notes: '',
                inEditMode: false,
            }
            // this.options.push(option);
            // Send post request
            axios.post("{{ route('api.receipts.draft-items', $receipt->receiptKey) }}", option, {
                    headers: {'Authorization': "Bearer " + '{{ auth()->user()->api_token }}'}
                })
                .then((response) => {
                    this.options.push(response.data);
                })
                .catch(function (error) {
                    console.log(error);
                });
        }
    }
});

var vm = new Vue({
    el: "#vue-el",

    data: {
        newOption: {
            weight: '',
            length: '',
            width: '',
            height: '',
            pack_type: '',
            notes: '',
            inEditMode: false,
        }
    },

    methods: {
        fetchOptions: function() {
            axios.get("{{ route('api.receipts.draft-items', $receipt->receiptKey) }}", {
                    headers: {'Authorization': "Bearer " + '{{ auth()->user()->api_token }}'}
                })
                .then((response) => {
                    this.options = response.data;
                })
                .catch(function (error) {
                    console.log(error);
                });
        }
    },

    ready: function() {
        this.fetchOptions();
    }
});
</script>
@endsection
