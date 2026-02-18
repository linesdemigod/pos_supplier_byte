<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Transfer Invoice</title>
        <style>
            body {
                font-family: 'Arial', sans-serif;
                margin: 0;
                padding: 0;
                background-color: #fff;
            }

            .invoice-container {
                max-width: 800px;
                margin: 20px auto;
                background: #fff;
                border-radius: 8px;
                padding: 20px;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            }

            .header {
                text-align: center;
                margin-bottom: 20px;
            }

            .header img {
                max-width: 120px;
            }

            .header h1 {
                font-size: 24px;
                margin: 10px 0;
            }

            .info {
                margin: 20px 0;
            }

            .info p {
                font-size: 14px;
                margin: 5px 0;
            }

            .transfer-details {
                margin: 20px 0;
                border-collapse: collapse;
                width: 100%;
                font-size: 14px;
            }

            .transfer-details th,
            .transfer-details td {
                border: 1px solid #ddd;
                text-align: left;
                padding: 8px;
            }

            .transfer-details th {
                background: #f4f4f4;
            }

            .transfer-details tbody tr:nth-child(odd) {
                background: #f9f9f9;
            }

            .footer {
                margin-top: 20px;
                text-align: center;
            }

            .footer p {
                font-size: 12px;
                color: #777;
            }
        </style>
    </head>

    <body>
        <div class="invoice-container">
            <!-- Header -->
            <div class="header">

                <h1>Transfer Invoice</h1>
                <div>{{ $company->name }}</div>
                <div>{{ $company->phone }}</div>
                <div>{{ $company->address }}</div>
                <p><strong>Date:</strong> {{ $transfer->created_at->format('d/m/Y') }} | <strong>Transfer ID:</strong>
                    {{ $transfer->order_number }}</p>
            </div>

            <!-- From and To Information -->
            <div class="info">
                <p><strong>From (Warehouse):</strong></p>
                <p>{{ $transfer->warehouse->name }}<br>{{ $transfer->warehouse->address }}<br>Phone:
                    {{ $transfer->warehouse->phone }} <br> Status: {{ $transfer->status }}</p>

                <p><strong>To (Store):</strong></p>
                <p>{{ $transfer->store->name }}<br>{{ $transfer->store->location }}<br>Phone:
                    {{ $transfer->store->phone }}</p>
            </div>

            <!-- Transfer Items Details -->
            <table class="transfer-details">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Item Code</th>
                        <th>Item Name</th>
                        <th>Quantity</th>
                        <th>Unit</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($transfer->transferOrderdetails as $key => $value)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $value->item->item_code }}</td>
                            <td>{{ $value->item->name }}</td>
                            <td>{{ $value->quantity }}</td>
                            <td>PCS</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Footer Notes -->
            <div class="footer">
                <p>This transfer invoice confirms the movement of items from the warehouse to the specified store
                    location.</p>

            </div>
        </div>
    </body>

</html>
