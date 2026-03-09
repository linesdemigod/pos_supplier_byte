@extends('layout.layout')

@section('title', 'Customer Credit')


@section('content')
    <x-breadcrumb title="App" subtitle='Customer credit' name='Customer Credits' />

    <div class="card">
        <div class="card-body">

            {{-- <x-flash-message /> --}}
            <livewire:customer-credit-manager />
        </div>
    </div>
@endsection
