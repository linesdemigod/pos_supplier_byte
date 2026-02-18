<div>

    <section>
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12 col-md-6 col-lg-6">
                    <div class="card">
                        <div class='table-responsive'>
                            <div class="card-body">
                                <p>Top 5 Selling Items</p>
                                <table class="table-centered w-100 dt-responsive nowrap table" id="report-tables">
                                    <thead class="table-light">
                                        <tr>
                                            <th scope="col">Item</th>
                                            <th scope="col">Quantity</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($bestSelling as $item)
                                            <tr style="cursor:pointer; line-height: 2px;">
                                                <td>{{ $item->name }}</td>
                                                <td>{{ $item->total_quantity }}</td>
                                            </tr>
                                        @endforeach

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="row">
                <div class="col-sm-12 col-md-12 col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="canvas-container">
                                <canvas id="salesChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        document.addEventListener('livewire:init', () => {

            // Fetch data from Livewire
            let bestSelling = @json($bestSelling);
            // let worstSelling = @json($worstSelling);

            // Prepare labels and data
            let labels = [
                ...bestSelling.map(item => item.name),
                // ...worstSelling.map(item => item.name)
            ];
            let data = [
                ...bestSelling.map(item => item.total_quantity),
                // ...worstSelling.map(item => item.total_quantity)
            ];

            // Initialize Chart.js
            const canvas = document.getElementById('salesChart')
            const ctx = canvas.getContext('2d');
            canvas.width = canvas.clientWidth;
            canvas.height = canvas.clientHeight;
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Top 5 Best Selling Items',
                        data: data,
                        backgroundColor: [
                            ...Array(bestSelling.length).fill(
                                'rgba(75, 192, 192, 0.7)'), // Green for best-selling
                            // ...Array(worstSelling.length).fill(
                            //     'rgba(255, 99, 132, 0.7)') // Red for worst-selling
                        ],
                        borderColor: [
                            ...Array(bestSelling.length).fill('rgba(75, 192, 192, 1)'),
                            // ...Array(worstSelling.length).fill('rgba(255, 99, 132, 1)')
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        datalabels: {
                            anchor: "end",
                            align: "end",
                            color: "black",
                            font: {
                                weight: "bold",
                            },
                        },
                        legend: {
                            display: true,
                            position: 'top'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return ` ${context.label}: ${context.raw} sales`;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });
    </script>
</div>
