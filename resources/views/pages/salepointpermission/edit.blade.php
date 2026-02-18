@extends('layout.layout')

@section('title')
    {{ 'Update Permission Sale Point' }}
@endsection

@section('content')
    <x-breadcrumb title="Update Permission Sale Point" subtitle='Update' name='Update Permission Sale Point'
        href='permission.sale.point' />
    <div class="py-3">
        <div class="card">
            <div class="card-body">
                <h3 class="card-title mb-3">Update Permission</h3>
                <x-flash-message />
                <form action="{{ route('permission.sale.point.update', $sale->id) }}" method="post">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-6 col-lg-6">
                            <div class="mb-3">
                                <label for="permission_name" class="form-label">Permission Name</label>
                                <input type="text" name="permission_name" class="form-control" id="permission_name"
                                    placeholder="Permission_name" value="{{ $sale->permission_name }}" readonly>
                                @error('permission_name')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-6">
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="status" id="status"
                                        value="1" {{ $sale->status === 1 ? 'checked' : '' }}>
                                    <label class="form-check-label" for="status">
                                        Allow
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="status" id="status2"
                                        value="0" {{ $sale->status === 0 ? 'checked' : '' }}>
                                    <label class="form-check-label" for="status2">
                                        Disallow
                                    </label>
                                </div>
                                @error('status')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror

                            </div>
                        </div>
                    </div>

                    <div class="">
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>

                </form>

            </div>
        </div>
    </div>
@endsection
@section('script')
    <script></script>
@endsection
