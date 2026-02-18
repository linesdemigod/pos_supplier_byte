@extends('layout.layout')

@section('title')
    {{ 'Shop' }}
@endsection
@section('page-id', 'shop')
@section('content')
    <section class="py-2">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <p class="fw-bold fs-3">Shop</p>
                </div>
            </div>
        </div>
    </section>
    <section class="container">
        <div class="row">

            <div class="col-xxl-12">
                <div class="card border">
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-2">
                                <div class="flex-grow-1 pe-2">
                                    <p class="fw-bold fs-3">Sales Point</p>
                                </div>
                                {{-- <div class="pe-2">
                                    <a href="#" class="btn btn-secondary rounded-3" data-bs-toggle="tooltip"
                                        data-bs-placement="top" data-bs-title="Orders"><i
                                            class="fa-solid fa-wand-magic-sparkles"></i>
                                        Today
                                        Sales</a>
                                </div> --}}
                                <div class="pe-2">
                                    <button type="button" class="btn btn-secondary rounded-3" data-bs-toggle="modal"
                                        data-bs-target="#exampleModal"><i class="fas fa-users"></i> Add
                                        Customer</button>
                                </div>
                                <div class="">

                                    <button type="button" class="btn btn-secondary rounded-3" data-bs-toggle="modal"
                                        id="show-hold-item" data-bs-target="#holdModal"><i class="fas fa-unlock"></i>
                                        Show
                                        Hold Sale</button>
                                </div>
                            </div>
                        </div>

                        {{-- search item --}}
                        <div class="row mb-3">
                            <div class="col-xxl-6 mb-2">
                                <div class="item-search-container">
                                    <label for="form-label">Search Item</label>
                                    <input type="text" name="item" class="form-control" id="item-search"
                                        autocomplete="off" placeholder="Start typing a item...">
                                    <ul id="suggestions" class="autocomplete-list">
                                    </ul>
                                </div>
                            </div>
                            <div class="col-xxl-3 mb-2">
                                <div class="">
                                    <label for="form-label">Category</label>
                                    <select name="category_id" id="category-search" class="form-select select_box">
                                        @unless (count($categories) == 0)
                                            <option value="0">All</option>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}">{{ Str::Title($category->name) }}
                                                </option>
                                            @endforeach
                                        @else
                                            <option value="">No Category</option>
                                        @endunless
                                    </select>
                                </div>
                            </div>
                            <div class="col-xxl-3 mb-2">
                                <div class="">
                                    <label for="form-label">Search Customer</label>
                                    {{-- <select name="customer_id" id="customer-search" class="form-select element_select">
                                        @unless (count($customers) == 0)
                                            <option value="">-- select customer --</option>
                                            @foreach ($customers as $customer)
                                                <option value="{{ $customer->id }}">{{ Str::Title($customer->name) }}</option>
                                            @endforeach
                                        @else
                                            <option value="">No Customer</option>
                                        @endunless
                                    </select> --}}
                                    <input type="text" name="item" class="form-control" id="customer-search"
                                        placeholder="Start typing customer name or phone...">
                                    <ul id="customer-suggestions" class="autocomplete-list">
                                    </ul>
                                </div>
                            </div>
                        </div>
                        {{-- end search item --}}

                        {{-- customer details --}}
                        <div class="mb-3">
                            <div class="d-flex justify-content-end align-items-center gap-2">
                                <p class="fw-bold">Customer:</p>
                                <div class="d-flex align-items-center gap-2" id="customer-container">

                                </div>

                            </div>
                        </div>

                        {{-- cart table --}}
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
                                    <label for="discount" class="col-form-label">Discount</label>
                                </div>
                                <!-- discount -->
                                <div class="col-auto">
                                    <input type="number" name="discount" id="discount" class="form-control discount"
                                        value="0" min="0" oninput="validity.valid||(value='');">
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
                            <button class="btn btn-primary" id="place-order"><i class="fas fa-receipt"></i>
                                Order</button>
                            <button class="btn btn-secondary" id="hold-order"><i class="fas fa-lock"></i> Hold
                                Sale</button>
                            <button class="btn btn-danger" id="clear-cart"><i class="fas fa-trash-alt"></i>
                                Clear</button>
                        </div>
                    </div>
                </div>
            </div>
            {{-- <div class="col-xxl-4">
                <div class="card border-1 border-muted border">
                    <div class="card-body">

                        <h2 class="fw-bold">Order Summary</h2>
                        
                    </div>
                </div>
            </div> --}}

        </div>


    </section>

    {{-- modal --}}
    <x-dom.customer-modal />

    <x-dom.hold-modal />

    {{-- @php
        use Carbon\Carbon;

        $todayDate = date('Y-m-d');

        if ($lastDailySale !== null && $lastDailySale->date !== null) {
            $lastSaleDate = new Carbon($lastDailySale->date);
            $formattedDate = $lastSaleDate->format('D, d M, Y');
        }
        $branch = auth()->user()->branch_id;
        $cookie_name = 'day_cookie_' . $branch;
    @endphp

    <div class="">
        {{ $lastDailySale }}
        @if ($lastDailySale == null || $lastDailySale->status == 'Closed')
            <x-popout-card title='Day Start Required' message='Would you like to start your day?' btnText='Start Day'
                btnColor='btn-primary' id="1" />
        @elseif($todayDate > $lastSaleDate)
            @if (Cookie::get($cookie_name) === null)
                <x-popout-card title='End Day'
                    message='You have not ended your business day in over 24 hours. We recommend you perform the End of Day Session before continuing'
                    btnText='End Day' btnColor='btn-danger' id="2" />
            @endif
        @endif
    </div> --}}

@endsection
