@extends('layout.layout')

@section('title')
    {{ 'Price History Report' }}
@endsection

@section('content')
    <x-breadcrumb title="App" subtitle='Report' name='Price History Report' href="report.index" />

    <div class="card">
        <div class="card-body">

            <livewire:report-price-history />
        </div>
    </div>
@endsection
