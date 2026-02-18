@extends('layout.layout')

@section('title')
    {{ 'Daily Sale' }}
@endsection

@section('content')
    <x-breadcrumb title="App" subtitle='Daily Sales' name='Daily Sales' />

    <div class="card">
        <div class="card-body">
            {{-- <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-5">
                <a href="{{ route('return.create') }}" class="btn btn-primary">
                    <i class="uil-plus"></i>
                    Create Return Item</a>

            </div> --}}
            {{-- <x-flash-message /> --}}
            <livewire:daily-sale-table />
        </div>
    </div>
@endsection
