@extends('layouts.error')

@section('title', 'Unauthorize Action.')
@section('content')
<div class="title">{{ $exception->getMessage() }}</div>
@endsection
