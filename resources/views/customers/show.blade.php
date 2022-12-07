@extends('layouts.customer-detail')

@section('show-links')
{!! link_to_route('customers.edit', trans('customer.edit'), [$customer->id], ['class' => 'btn btn-warning']) !!}
@endsection

@section('customer-content')
<div class="row">
    <div class="col-md-6">
        @include('customers.partials.show')
    </div>
    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading"><h3 class="panel-title">{{ trans('customer.category') }}</h3></div>
            <table class="table table-condensed">
                <tbody>
                    <tr><th>{{ trans('customer.category') }}</th><td>{{ $customer->category }}</td></tr>
                    <tr>
                        <th>{{ trans('customer.pod_checklist') }}</th>
                        <td>{!! $customer->pod_checklist_display !!}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
