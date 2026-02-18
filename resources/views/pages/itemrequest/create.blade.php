@extends('layout.layout')

@section('title')
    {{ 'Request For Item' }}
@endsection
@section('page-id', 'request_item')

@section('content')
    <x-breadcrumb title="Request Item" subtitle='Add Request Item' name='Request Item' href='itemrequest.index' />
    <section class="py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-12 mx-auto">
                    <div class="card border-1 mb-3 border">
                        <div class="card-body">
                            <h3 class="py-2 text-center">Add Item</h3>
                            <div class="row">
                                <div class="col-xxl-8 mb-4">
                                    <div class="item-search-container form-group">
                                        <label for="form-label">Search Item</label>
                                        <input type="text" name="item" class="form-control" id="item-search"
                                            autocomplete="off" placeholder="Start typing a item...">
                                        <ul id="suggestions" class="autocomplete-list">
                                        </ul>
                                    </div>

                                </div>
                                <div class="col-xxl-4">
                                    <div class="form-group mb-3">
                                        <label for="warehouse">Warehouse</label>
                                        <select name="warehouse_id" id="warehouse_id" class="form-select element_select">
                                            <option value="0">-- select warehouse --</option>
                                            @unless (count($warehouses) == 0)
                                                @foreach ($warehouses as $warehouse)
                                                    <option value="{{ $warehouse->id }}">{{ Str::title($warehouse->name) }}
                                                    </option>
                                                @endforeach
                                            @else
                                                <option value="0">No Warehouse</option>
                                            @endunless

                                        </select>
                                        <span class="text-danger warehouse-error"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive" data-simplebar style="max-height: 400px">
                                <table class="table-centered table">
                                    <thead class="table-secondary">
                                        <tr>
                                            <th class="d-none">ID</th>
                                            <th>Item</th>
                                            <th>Price</th>
                                            <th>Quantity</th>
                                            <th>Total</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="cart-table">

                                    </tbody>
                                </table>


                            </div>
                            {{-- calculation --}}
                            <div class="m-3">
                                <div class="row g-1 justify-content-end mb-2">
                                    <div class="col-auto">
                                        <label for="inputPassword6" class="col-form-label">Subtotal</label>
                                    </div>
                                    <!-- gross -->
                                    <div class="col-auto">
                                        <input type="text" name="gross" id="total-subtotal" class="form-control gross"
                                            readonly="">
                                    </div>
                                </div>

                                <div class="row g-3 justify-content-end mb-2">
                                    <div class="col-auto">
                                        <label for="net" class="col-form-label">Total</label>
                                    </div>
                                    <div class="col-auto">
                                        <input type="text" id="total-grandtotal" name="net"
                                            class="form-control net-total" readonly="">

                                    </div>
                                </div>
                            </div>


                            {{-- button --}}
                            <div class="d-flex justify-content-end gap-2">
                                <button class="btn btn-primary" id="place-order">Request</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </section>
@endsection
