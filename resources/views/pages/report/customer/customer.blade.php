@extends('layout.layout')

@section('title')
    {{ 'Customer Purchase History Report' }}
@endsection

@section('content')
    <x-breadcrumb title="App" subtitle='Report' name='Customer Purchase History Report' href="report.index" />

    <div class="card">
        <div class="card-body">

            <livewire:report-customer-purchase />
        </div>
    </div>
@endsection
