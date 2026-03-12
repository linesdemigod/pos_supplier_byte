@extends('layout.layout')

@section('title', 'Shift')


@section('content')
    <x-breadcrumb title="Apps" subtitle='Shift' name='Shift' />

    <div class="py-3">
        <div class="card">
            <x-flash-message />
            <div class="card-body">

                @if ($shift == null || $shift->status === 'closed')
                    <p class="fs-3 mb-0">Shift is closed</p>
                    <p>Open a shift to perform sales</p>

                    <form action="{{ route('shift.store') }}" method="POST">
                        @csrf
                        <div class="col-xxl-6 mb-3">
                            <label for="name" class="form-label">Starting Cash</label>

                            <input type="number" name="starting_cash" id="discount" class="form-control discount"
                                value="0">
                            @error('starting_cash')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary">Open Shift</button>
                    </form>
                @else
                    <p class="fs-5">Shift is open: <span
                            class="">{{ date(' d/m/Y H:i', strtotime($shift->opened_at)) }}</span></p>



                    <table class="table">
                        <tr>
                            <th>Starting Cash</th>
                            <td>{{ number_format($shift->starting_cash, 2) ?? 0 }}</td>
                        </tr>
                        <tr>
                            <th>Gross Sales</th>
                            <td>{{ number_format(optional($shiftTotal)->subtotal, 2) ?? 0 }}</td>
                        </tr>
                        <tr>
                            <th>Discount</th>
                            <td>{{ number_format(optional($shiftTotal)->discount, 2) ?? 0 }}</td>
                        </tr>
                        <tr>
                            <th class="fw-bold">Total Payments Received</th>
                            <td class="fw-bold">{{ number_format(optional($shiftTotal)->total, 2) ?? 0 }}</td>
                        </tr>
                        <tr>
                            <th class="fw-bold">Expected Cash</th>
                            <td class="fw-bold">{{ number_format($shift->expected_cash, 2) ?? 0 }}</td>
                        </tr>
                    </table>

                    <div class="mt-3">
                        <form action="{{ route('shift.update', $shift->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="col-xxl-6 mb-3">
                                <label for="name" class="form-label">Closing Cash</label>

                                <input type="number" name="closing_cash" id="discount" class="form-control discount"
                                    value="0">
                                @error('closing_cash')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-danger"
                                onclick="return confirm('Are you sure you want to close shift?');">Close Shift</button>
                        </form>
                    </div>
                @endif

            </div>
        </div>
    </div>
@endsection
