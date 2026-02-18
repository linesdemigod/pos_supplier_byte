@extends('layout.layout')

@section('title')
    {{ 'Stock History Report' }}
@endsection

@section('content')
    <x-breadcrumb title="App" subtitle='Report' name='Stock History Report' href="report.index" />

    <div class="card">
        <div class="card-body">

            <livewire:report-stock-history />
        </div>
    </div>
@endsection
