@extends('layout.layout')

@section('title')
    {{ 'Transfer Order Items' }}
@endsection

@section('content')
    <x-breadcrumb title="Transfer Order Item" subtitle='Request Item' name='Transfer Order' />

    <div class="card">
        <div class="card-body">

            {{-- <x-flash-message /> --}}
            <livewire:transfer-order-table />
        </div>
    </div>
@endsection
