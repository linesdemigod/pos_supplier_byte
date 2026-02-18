@extends('layout.layout')

@section('title', 'Create Supplier')

@section('content')
    <x-breadcrumb title="Apps" subtitle='Create Supplier' name='Create Supplier' />

    <div class="py-3">
        <livewire:supplier-create-manager>
    </div>
@endsection
