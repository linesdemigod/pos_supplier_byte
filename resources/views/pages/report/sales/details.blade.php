@extends('layout.layout')

@section('title')
    {{ 'Sales Details' }}
@endsection

@section('content')
    <x-breadcrumb title="App" subtitle='Report' name='Sale Details' href="report.index" />
    <livewire:report-sale-detail />
@endsection
