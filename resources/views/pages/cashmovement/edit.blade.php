@extends('layout.layout')

@section('title', 'Create cash Movement')


@section('content')
    <x-breadcrumb title="cash Movement" subtitle='Update cash Movement' name='cash Movement' href='cash_movement.index' />
    <section class="py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6 mx-auto">
                    <div class="card border-1 border">
                        <div class="card-body">
                            <h3 class="py-2 text-center">Update Cash Movement</h3>
                            <form action="{{ route('cash_movement.update', $record->id) }}" method="Post">
                                @csrf
                                @method('PUT')
                                <div class="form-group mb-3">
                                    <label for="type" class="text-muted">Amount</label>
                                    <select name="type" id="" class="form-select">
                                        <option value="in" {{ $record->type === 'in' ? 'selected' : '' }}>In</option>
                                        <option value="out" {{ $record->type === 'out' ? 'selected' : '' }}>Out</option>
                                    </select>
                                    @error('type')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror

                                </div>

                                <div class="form-group mb-3">
                                    <label for="amount" class="text-muted">Amount</label>
                                    <input type="text" class="form-control" name="amount" id="amount"
                                        value="{{ old('amount', $record->amount) }}">
                                    @error('amount')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror

                                </div>
                                <div class="form-group mb-3">
                                    <label for="reason" class="text-muted">Reason</label>
                                    <input type="text" class="form-control" name="reason" id="reason"
                                        value="{{ old('reason', $record->reason) }}">
                                    @error('reason')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <button type="submit" name="submit"
                                        class="form-control btn btn-primary btn-block">Update</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
