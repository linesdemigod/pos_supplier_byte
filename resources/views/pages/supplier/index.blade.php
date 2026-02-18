@extends('layout.layout')

@section('title', 'Supplier')

@section('content')
    <x-breadcrumb title="Apps" subtitle='Supplier' name='Supplier' />


    <div class="py-3">
        <div class="card">
            <div class="card-body">

                <livewire:supplier-manager>
            </div>
        </div>
    </div>
@endsection
