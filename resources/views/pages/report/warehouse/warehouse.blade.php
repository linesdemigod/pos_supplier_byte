@extends('layout.layout')

@section('title')
    {{ 'Warehouse Transfer' }}
@endsection

@section('content')
    <x-breadcrumb title="App" subtitle='Report' name='Warehouse Transfer' href="report.index" />

    <div class="card">
        <div class="card-body">

            <livewire:report-warehouse-inventory />
        </div>
    </div>
@endsection
