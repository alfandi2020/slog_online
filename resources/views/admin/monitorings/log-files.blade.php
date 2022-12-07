@extends('layouts.app')

@section('title','Log Files')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="panel panel-default">
            <table class="table table-condensed">
                <thead>
                    <th>#</th>
                    <th>File Name</th>
                    <th>Size</th>
                    <th>Time</th>
                    <th>{{ trans('app.action') }}</th>
                </thead>
                <tbody>
                    @if (File::exists('error_log'))
                    <tr>
                        <td>0</td>
                        <td>error_log</td>
                        <td>{{ formatSizeUnits(File::size('error_log')) }}</td>
                        <td>{{ date('Y-m-d H:i:s', File::lastModified('error_log')) }}</td>
                        <td>
                            {!! html_link_to_route('log-files.server-error-log','',[],[
                                'class'=>'btn btn-default btn-xs',
                                'icon' => 'search',
                                'id' => 'view-error-log',
                                'title' => 'View file error_log',
                                'target' => '_blank',
                            ]) !!}
                        </td>
                    </tr>
                    @endif
                    @forelse($logFiles as $key => $logFile)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $logFile->getFilename() }}</td>
                        <td>{{ formatSizeUnits($logFile->getSize()) }}</td>
                        <td>{{ date('Y-m-d H:i:s', $logFile->getMTime()) }}</td>
                        <td>
                            {!! html_link_to_route('admin.log-files.download','',[$logFile->getFilename()],[
                                'class'=>'btn btn-default btn-xs',
                                'icon' => 'download',
                                'id' => 'download-' . $logFile->getFilename(),
                                'title' => 'Download file ' . $logFile->getFilename()
                            ]) !!}
                            {!! html_link_to_route('admin.log-files.show','',[$logFile->getFilename()],[
                                'class'=>'btn btn-default btn-xs',
                                'icon' => 'search',
                                'id' => 'view-' . $logFile->getFilename(),
                                'title' => 'View file ' . $logFile->getFilename(),
                                'target' => '_blank'
                            ]) !!}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3">No Log File Exists</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
