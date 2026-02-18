@extends('layout.layout')

@section('title')
    {{ 'Sales Trends' }}
@endsection

@section('content')
    <x-breadcrumb title="App" subtitle='Report' name='Sales Trends' href="report.index" />

    <livewire:report-sales-trends />
@endsection
