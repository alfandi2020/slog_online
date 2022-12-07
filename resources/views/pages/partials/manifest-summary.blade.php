@inject('manifestSummary', 'App\Entities\Manifests\ManifestSummaryQuery')

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-retweet fa-fw"></i> {{ trans('manifest.list') }}</h3>
    </div>
    <div class="panel-body">
        {!! Form::open(['method'=>'get','route'=>'manifests.index']) !!}
        <div class="input-group custom-search-form">
            {!! Form::text('manifest_number', Request::get('manifest_number'), ['class'=>'form-control','required','placeholder' => 'Cari Nomor/Scan Barcode Manifest..']) !!}
            <span class="input-group-btn">
                <button class="btn btn-default" type="submit" title="trans('manifest.search')"><i class="fa fa-search"></i></button>
            </span>
        </div>
        {!! Form::close() !!}
    </div>
    <div class="list-group">
        @component('pages.partials.manifest-summary-item')
            @slot('text', trans('manifest.handover'))
            @slot('linkRoute', route('manifests.handovers.index'))
            @slot('icon', 'upload')
            @slot('number', $manifestSummary->handover())
        @endcomponent

        @component('pages.partials.manifest-summary-item')
            @slot('text', trans('manifest.delivery_out'))
            @slot('linkRoute', route('manifests.deliveries.index', ['type' => 'out']))
            @slot('icon', 'sign-out')
            @slot('number', $manifestSummary->deliveryOut())
        @endcomponent

        @component('pages.partials.manifest-summary-item')
            @slot('text', trans('manifest.delivery_in'))
            @slot('linkRoute', route('manifests.deliveries.index', ['type' => 'in']))
            @slot('icon', 'sign-in')
            @slot('number', $manifestSummary->deliveryIn())
        @endcomponent

        @component('pages.partials.manifest-summary-item')
            @slot('text', trans('manifest.distribution'))
            @slot('linkRoute', route('manifests.distributions.index'))
            @slot('icon', 'truck')
            @slot('number', $manifestSummary->distribution())
        @endcomponent

        @component('pages.partials.manifest-summary-item')
            @slot('text', trans('manifest.return_out'))
            @slot('linkRoute', route('manifests.returns.index', ['type' => 'out']))
            @slot('icon', 'rotate-left')
            @slot('number', $manifestSummary->returnOut())
        @endcomponent

        @component('pages.partials.manifest-summary-item')
            @slot('text', trans('manifest.return_in'))
            @slot('linkRoute', route('manifests.returns.index', ['type' => 'in']))
            @slot('icon', 'rotate-right')
            @slot('number', $manifestSummary->returnIn())
        @endcomponent

        @component('pages.partials.manifest-summary-item')
            @slot('text', trans('manifest.accounting'))
            @slot('linkRoute', route('manifests.accountings.index'))
            @slot('icon', 'money')
            @slot('number', $manifestSummary->accounting())
        @endcomponent

        @component('pages.partials.manifest-summary-item')
            @slot('text', trans('manifest.problem'))
            @slot('linkRoute', route('manifests.problems.index'))
            @slot('icon', 'exclamation-circle')
            @slot('number', $manifestSummary->problem())
        @endcomponent
    </div>
</div>
<!-- /.panel -->