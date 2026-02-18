@extends('layout.layout')

@section('title')
    {{ $supplier->name }} - Purchase Details
@endsection

@section('content')
    <x-breadcrumb title="Supplier" subtitle='{{ $supplier->name }} supplier details'
        name="{{ $supplier->name }}'s supplier details" href='supplier.index' />

    <div class="card">
        <div class="card-body">

            <div class="mb-3">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-5">


                    <div class="col-md-4">
                        <h5 class="bg-primary mb-3 p-2 text-white">{{ $supplier->name }}</h5>
                        <div class="table-responsive">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <th class="">Phone:</th>
                                        <td class="text-muted text-end">{{ $supplier->contact_info }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="">Email:</th>
                                        <td class="text-muted text-end">
                                            {{ $supplier->email ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th class="">Address:</th>
                                        <td class="text-muted text-end">
                                            {{ $supplier->address ?? 'N/A' }}</td>
                                    </tr>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <livewire:supplier-purchase-manager :supplier-id="$supplier->id" />
        </div>
    </div>
@endsection
