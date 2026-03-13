@extends('layout.layout')

@section('title', 'Cash Movement')

@section('content')
    <x-breadcrumb title="Cash Movement" subtitle='Cash Movement' name='Cash Movement' href='cash_movement.create' />

    <div class="card">
        <div class="card-body">
            @can('cash_movement.create')
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-5">
                    <a href="{{ route('cash_movement.create') }}" class="btn btn-primary">
                        <i class="uil-plus"></i>
                        Create </a>
                </div>
            @endcan
            {{-- <x-flash-message /> --}}
            <livewire:cash-movement-manager />
        </div>
    </div>
@endsection
