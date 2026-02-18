@extends('layout.layout')

@section('title')
    {{ 'Store request Items' }}
@endsection

@section('content')
    <x-breadcrumb title="Store request Item" subtitle='Request Item' name='Store Request' />

    <div class="card">
        <div class="card-body">
            {{-- display only to stores --}}
            @can('storeRequest.create')
                @if (Auth::user()->store_id)
                    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-5">
                        <a href="{{ route('storerequest.create') }}" class="btn btn-primary">
                            <i class="uil-plus"></i>
                            Make Request</a>
                    </div>
                @endif
            @endcan
            {{-- <x-flash-message /> --}}
            <livewire:store-request-table />
        </div>
    </div>
@endsection
