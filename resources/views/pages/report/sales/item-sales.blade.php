@extends('layout.layout')

@section('title')
    {{ 'Sales by Item' }}
@endsection

@section('content')
    <x-breadcrumb title="App" subtitle='Report' name='Sales by Item' href="report.index" />

    <div class="card">
        <div class="card-body">

            <livewire:report-sales-item />
        </div>
    </div>
@endsection
