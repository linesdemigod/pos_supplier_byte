<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Sales Report</title>
        <style>
            /* PDF GENERATION OPTIMIZED CSS */

            body {
                font-family: 'Helvetica', 'Arial', sans-serif;
                font-size: 12px;
                color: #333;
                line-height: 1.5;
                margin: 0;
                padding: 20px;
            }

            /* --- Report Header Section --- */
            .report-header {
                margin-bottom: 30px;
                border-bottom: 2px solid #333;
                padding-bottom: 20px;
            }

            .company-info h1 {
                margin: 0 0 10px 0;
                font-size: 24px;
                text-transform: uppercase;
                color: #2c3e50;
            }

            .report-meta {
                display: flex;
                /* Note: Flexbox works in most modern PDF engines. If issues arise, use a table for layout */
                justify-content: space-between;
                margin-top: 15px;
            }

            .meta-item strong {
                display: block;
                font-size: 10px;
                color: #7f8c8d;
                text-transform: uppercase;
            }

            /* --- Table Styling --- */
            .table-container {
                width: 100%;
                margin-bottom: 20px;
            }

            table {
                width: 100%;
                border-collapse: collapse;
                /* Essential for PDF tables */
                margin-bottom: 1rem;
            }

            thead th {
                background-color: #f8f9fa;
                color: #2c3e50;
                font-weight: bold;
                text-align: left;
                padding: 12px 8px;
                border-bottom: 2px solid #dee2e6;
                font-size: 11px;
                text-transform: uppercase;
            }

            tbody td {
                padding: 10px 8px;
                border-bottom: 1px solid #dee2e6;
                vertical-align: middle;
            }

            /* Alignment helpers */
            .text-right {
                text-align: right;
            }

            .text-center {
                text-align: center;
            }

            /* Prevent rows from breaking across pages awkwardly */
            tr {
                page-break-inside: avoid;
            }

            /* --- Empty State --- */
            .no-results {
                text-align: center;
                padding: 40px;
                color: #6c757d;
                font-style: italic;
                border: 1px dashed #dee2e6;
                border-radius: 4px;
            }

            /* --- Footer / Totals (Optional) --- */
            .report-footer {
                margin-top: 20px;
                text-align: right;
                font-size: 10px;
                color: #7f8c8d;
            }

            .total-row td {
                font-weight: bold;
                background-color: #fafafa;
            }
        </style>
    </head>

    <body>

        <!-- 1. Header Section -->
        <div class="report-header">
            <div class="company-info">
                <!-- Replace with dynamic company name -->
                <h1>{{ $store->company->name }}</h1>
                <span>{{ $store->name }}</span> <br>
                <span>{{ $store->location }}</span>
            </div>

            <div class="report-meta">
                <div class="meta-item">
                    <strong>Report Name</strong>
                    Sales Summary Report
                </div>
                <div class="meta-item">
                    <strong>Period</strong>
                    <!-- Replace with dynamic date -->
                    {{ date('d-m-Y H:i:s', strtotime($from)) }} - {{ date('d-m-Y H:i:s', strtotime($to)) }}
                </div>
                {{-- <div class="meta-item">
                    <strong>Generated On</strong>
                    <!-- Replace with current date -->
                    Oct 24, 2023
                </div> --}}
            </div>
        </div>

        <!-- 2. Table Section -->
        <div class="table-container">

            <!-- BLADE LOGIC START -->
            @if ($records && $records->count() > 0)

                <table>
                    <thead>
                        <tr>
                            <th scope="col" style="width: 40%;">Item Description</th>
                            <th scope="col" class="text-center">Qty</th>
                            <th scope="col" class="text-right">Unit Price</th>
                            <th scope="col" class="text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($records as $item)
                            <tr>
                                <td>{{ $item->name }}</td>
                                <td class="text-center">{{ $item->quantity }}</td>
                                <td class="text-right">{{ number_format($item->price, 2) }}</td>
                                <td class="text-right">{{ number_format($item->subtotal, 2) }}</td>
                            </tr>
                        @endforeach

                        <!-- Example Grand Total Row -->
                        <tr class="total-row">
                            <td colspan="3">Grand Total</td>
                            <td class="numeric text-right">
                                {{ number_format($records->sum('subtotal'), 2) }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            @elseif($records && $records->count() === 0)
                <div class="no-results">
                    No results found for the selected date range.
                </div>

            @endif
            <!-- BLADE LOGIC END -->

        </div>

        <!-- 3. Footer Section -->
        <div class="report-footer">
            Generated on {{ now()->format('Y-m-d H:i:s') }}
            <p>Page 1 of 1 &bull; Confidential Document</p>
        </div>

    </body>

</html>
