<table class="table table-condensed table-striped">
    <thead>
        <tr><th>{{ trans('rate.origin') }}</th><th>{{ trans('rate.destination') }}</th></tr>
        <tr><th>{{ $costs->getOrigin() }}</th><th>{{ $costs->getDestination() }}</th></tr>
        <tr>
            <th>{{ trans('service.service') }}</th>
            <th>{{ trans('rate.rate') }}</th>
        </tr>
    </thead>
    <tbody>
        @if ($costs->rates->isEmpty())
        <p>{{ trans('rate.not_found_for') }}</p>
        @else
        @foreach($costs->rates as $rate)
        <tr><td>{{ $rate->service() }}</td><td>{{ formatRp($rate->cost) }}</td></tr>
        @endforeach
        @endif
    </tbody>
</table>