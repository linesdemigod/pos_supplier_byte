@extends('layout.layout')

@section('title')
    {{ 'Sale' }}
@endsection

@section('content')
    <x-breadcrumb title="App" subtitle='Sales' name='Sales' />

    <div class="card">
        <div class="card-body">

            {{-- <x-flash-message /> --}}
            <livewire:sale-table />
        </div>
    </div>
@endsection
