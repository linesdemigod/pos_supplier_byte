@if (session()->has('message'))
    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)" x-show="show"
        {{ $attributes->merge(['class' => 'alert alert-success text-center text-success']) }}>

        {{ session('message') }}

    </div>
@endif
@if (session()->has('error'))
    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)" x-show="show"
        {{ $attributes->merge(['class' => 'alert alert-danger text-center text-danger']) }}>

        {{ session('error') }}

    </div>
@endif
