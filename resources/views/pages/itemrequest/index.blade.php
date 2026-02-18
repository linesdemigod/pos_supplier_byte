@extends('layout.layout')

@section('title')
    {{ 'Requested Items' }}
@endsection

@section('content')
    <x-breadcrumb title="Requested Item" subtitle='Request Item' name='Items' />

    <div class="card">
        <div class="card-body">
            {{-- display only to stores --}}
            @can('storeRequest.create')
                @if (Auth::user()->store_id)
                    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-5">
                        <a href="{{ route('itemrequest.create') }}" class="btn btn-primary">
                            <i class="uil-plus"></i>
                            Make Request</a>
                    </div>
                @endif
            @endcan
            {{-- <x-flash-message /> --}}
            <livewire:item-request-table />
        </div>
    </div>
@endsection
