@extends('layout.layout')

@section('title')
    {{ 'Return Item' }}
@endsection

@section('content')
    <x-breadcrumb title="App" subtitle='Return Items' name='Return Items' />

    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-5">
                @can('returnItem.create')
                    <a href="{{ route('return.create') }}" class="btn btn-primary">
                        <i class="uil-plus"></i>
                        Create Return Item</a>
                @endcan

            </div>
            {{-- <x-flash-message /> --}}
            <livewire:return-item-table />
        </div>
    </div>
@endsection
