@extends('layout.layout')

@section('title')
    {{ $customer->name }} - Credit Details
@endsection

@section('content')
    <x-breadcrumb title="Credit" subtitle='{{ $customer->name }} credit details' name="{{ $customer->name }}'s credit details"
        href='credit.index' />

    <div class="card">
        <div class="card-body">

            <div class="mb-3">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-5">


                    <div class="col-md-4">
                        <h5 class="bg-primary mb-3 p-2 text-white">{{ $customer->name }}</h5>
                        <div class="table-responsive">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <th class="">Phone:</th>
                                        <td class="text-muted text-end">{{ $customer->phone ?? 'N/A' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="">Address:</th>
                                        <td class="text-muted text-end">
                                            {{ $customer->location ?? 'N/A' }}</td>
                                    </tr>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <livewire:credit-detail-manager :customer-id="$customer->id" />
        </div>
    </div>
@endsection
