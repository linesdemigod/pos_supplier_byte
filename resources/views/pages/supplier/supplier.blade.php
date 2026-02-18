@extends('layout.layout')

@section('title', 'Suppliers')


@section('content')
    <x-breadcrumb title="App" subtitle='Suppliers' name='Suppliers' />

    <div class="card">
        <div class="card-body">

            {{-- <x-flash-message /> --}}
            <livewire:supplier-records-manager />
        </div>
    </div>
@endsection
