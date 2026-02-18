@extends('layout.layout')

@section('title')
    {{ 'Home' }}
@endsection

@section('content')
    <x-breadcrumb title="Quick Menu" subtitle='Quick Menu' name='Quick Menu' />

    <p class="fw-bold">Welcome, <span class="fw-normal">{{ auth()->user()->name }}</span></p>

    <div class="container">
        <div class="d-flex align-items-center flex-wrap gap-5">
            <a href="{{ route('shop.index') }}" class="text-center text-black">
                <div class="custom-container">
                    <img src="{{ asset('images/store.png') }}" alt="Shop" class="icon">
                    <div class="tooltip">Shop</div>

                </div>

                Shop
            </a>
            <a href="{{ route('customer.index') }}" class="text-center text-black">
                <div class="custom-container">
                    <img src="{{ asset('images/rating.png') }}" alt="Customer" class="icon">
                    <div class="tooltip">Customer</div>

                </div>

                Customer
            </a>
            <a href="{{ route('return.index') }}" class="text-center text-black">
                <div class="custom-container">
                    <img src="{{ asset('images/exchange.png') }}" alt="Return Item" class="icon">
                    <div class="tooltip">Return Item</div>

                </div>

                Return Item
            </a>
        </div>
    </div>
@endsection
{{-- @section('script')
    <script>
        async function stockLevelMessage() {
            try {
                const res = await axios.get('stock-level-notification');
                const {
                    message
                } = res.data;
                if (res.status === 200 && message !== '') {
                    notyf.error(message)
                }
            } catch (error) {
                console.log(error)
            }
        }

        stockLevelMessage();
    </script>
@endsection --}}
