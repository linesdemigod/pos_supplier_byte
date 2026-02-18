@extends('layout.layout')

@section('title')
    {{ 'Company' }}
@endsection

@section('content')
    <x-breadcrumb title="Apps" subtitle='Company' name='Company' />
    <div class="py-3">
        <div class="card">

            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-5">
                    <a href="{{ route('company.create') }}" class="btn btn-primary">
                        <i class="uil-plus"></i>
                        Add Company</a>
                </div>
                <div class="table-responsive">

                    @if ($company)
                        <table class="table-centered w-100 table">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col">Name</th>
                                    <th scope="col">Contact</th>
                                    <th scope="col">Address</th>
                                    <th scope="col">Action</th>

                                </tr>
                            </thead>
                            <tbody>

                                <tr>

                                    <td>{{ ucwords($company->name) ?? 'N/A' }} </td>
                                    <td> {{ $company->phone ?? 'N/A' }} </td>
                                    <td>{{ $company->address ?? 'N/A' }} </td>


                                    <td>
                                        <a href="{{ route('company.edit', $company->id) }}"
                                            class="action-icon btn btn-primary me-2 text-white" data-bs-toggle="tooltip"
                                            data-bs-placement="top" title="Edit">
                                            <i class="fas fa-pen-square"></i></a>
                                    </td>



                                </tr>

                            </tbody>
                        </table>
                    @else
                        <h3 class="text-center">No record available</h3>
                    @endif


                </div>

            </div>
        </div>
    </div>
@endsection
