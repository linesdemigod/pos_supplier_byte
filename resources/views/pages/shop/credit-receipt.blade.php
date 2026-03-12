<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Document</title>
        <style>
            body {
                font-size: 12px;
                margin: 0;
                width: 72mm;
            }

            @page {
                margin: 5mm;
                /* Minimal margin to maximize printable area */
            }

            .container {
                width: 100%;
                max-width: 72mm;
                margin: 0 auto;
                padding: 5px;
            }

            .heading {
                margin-bottom: 0.5rem;
                text-align: center;
                font-size: 1.5rem;
                font-weight: bold;
            }

            .company-details,
            .invoice-details {
                text-align: center;
            }

            .company-details p,
            h2 {
                margin-bottom: 0 0;
            }

            .invoice-details p {
                margin: 0.5rem 0;

            }

            table {
                width: 100%;
                font-size: 12px;
                border-collapse: collapse;
            }

            th,
            td {
                padding: 2px;
            }

            .text-right {
                text-align: right;
            }

            th {
                text-align: left;
                padding: 0 10px;
            }

            .underline {
                text-decoration: underline;
            }

            .border-b {
                border-bottom: 1px solid #ccc;
            }

            .font-bold {
                font-weight: bold;
            }

            .font-semibold {
                font-weight: 600;
            }

            .text-gray-600 {
                color: #4b5563;
            }

            .text-gray-800 {
                color: #1f2937;
            }

            .bg-gray-200 {
                background-color: #e5e7eb;
            }

            .px-4 {
                padding-right: 1rem;
                padding-left: 1rem;
            }

            .py-2 {
                padding-top: 0.5rem;
                padding-bottom: 0.5rem;
            }

            .w-full {
                width: 100%;
            }

            .mt-6 {
                margin-top: 1.5rem;
            }

            .mb-6 {
                margin-bottom: 1.5rem;
            }

            .py-6 {
                padding-top: 1.5rem;
            }

            .text-white {
                color: #fff;
            }

            .page-break {
                page-break-after: always;
            }
        </style>
    </head>

    <body>
        <div class="container">
            <h1 class="heading">Electronic Receipt</h1>

            <!-- Company Details -->
            <div class="company-details">
                <h2 class="mb-0 font-semibold">{{ $company->name }}</h2>
                <p class="mb-0">{{ $sale->store->name ?? 'N/A' }}</p>
                <p class="mb-0">{{ $sale->store->location ?? 'N/A' }}</p>
                <p>Phone: {{ $sale->store->phone ?? 'N/A' }}</p>
            </div>

            <!-- Invoice Details -->
            <div class="invoice-details">
                <p class="text-center underline">Original Receipt</p>
                <p>Date: <span class="text-gray-600">{{ date('d-m-Y H:i:s', strtotime($sale->created_at)) }}</span>
                </p>
                <p>Reference #: <span class="text-gray-600">{{ $sale->reference }}</span></p>
                <p>Customer: <span class="text-gray-600">{{ $sale->customer->name ?? '' }}</span></p>
            </div>

            <!-- Services Table -->
            <table class="mt-6 w-full">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="px-4 py-2 text-left">Item</th>
                        <th class="px-4 py-2 text-right">Qty</th>
                        <th class="px-4 py-2 text-right">Price</th>
                        <th class="px-4 py-2 text-right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Consultation -->

                    @foreach ($sale->creditItems as $saleItem)
                        <tr class="border-b">
                            <td class="px-4 py-2">{{ $saleItem->item->name }}</td>
                            <td class="px-4 py-2 text-right">{{ $saleItem->quantity }}</td>
                            <td class="px-4 py-2 text-right">{{ $saleItem->price }}</td>
                            <td class="px-4 py-2 text-right">{{ $saleItem->total }}</td>
                        </tr>
                    @endforeach



                    <!-- Bill Breakdown -->
                    <tr>
                        <td colspan="3" class="px-4 text-right font-bold">Subtotal:</td>
                        <td class="px-4 text-right">{{ $sale->subtotal }}</td>
                    </tr>
                    <tr>
                        <td colspan="3" class="px-4 text-right font-bold">Discount:</td>
                        <td class="px-4 text-right">{{ $sale->discount }}</td>
                    </tr>
                    <tr>
                        <td colspan="3" class="px-4 text-right font-bold">Total:</td>
                        <td class="px-4 text-right font-bold">
                            {{ $sale->total_amount }}
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Payment Details -->
            <div class="mt-6">
                <p class="font-semibold text-gray-800">Payment Mode: Credit</p>
                <p class="font-semibold text-gray-800">Cashier/Biller: <span
                        class="font-normal text-gray-600">{{ $sale->user->name }}</span></p>
            </div>
        </div>
    </body>

</html>
