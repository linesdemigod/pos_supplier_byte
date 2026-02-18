@extends('layout.layout')

@section('title')
    {{ 'Sales by Employee' }}
@endsection

@section('content')
    <x-breadcrumb title="App" subtitle='Report' name='Sale by Employee' href="report.index" />
    <livewire:report-staff-sales />
@endsection
