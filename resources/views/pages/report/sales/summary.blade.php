@extends('layout.layout')

@section('title')
    {{ 'Sales Summary' }}
@endsection
@section('content')
    <section class="pt-4">
        <div class="container-fluid">
            <div class="d-flex justify-content-between">
                <p class="fs-3">Sales Summary</p>

                <div class="">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('report.index') }}">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Sales Summary</li>
                        </ol>
                    </nav>
                </div>

            </div>
            {{-- forms --}}
            <div class="d-flex mb-3 gap-2">
                <div class="">
                    <div class="input-group">
                        <span class="input-group-text bg-primary border-primary text-white">
                            <i class="fas fa-calendar-alt"></i>
                        </span>
                        <select name="period" class="form-select form-control-light" id="period">
                            <option value="today">Today</option>
                            <option value="yesterday">Yesterday</option>
                            <option value="weekly">This Week</option>
                            <option value="monthly">This Month</option>
                            <option value="yearly">This Year</option>
                        </select>
                    </div>
                </div>

            </div>

        </div>
    </section>

    <div class="row">
        <div class="col-sm-6 col-md-3">
            <div class="card card-stats card-round">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-icon">
                            <div class="icon-big icon-primary bubble-shadow-small text-center">
                                <i class="fas fa-dollar-sign"></i>
                            </div>
                        </div>
                        <div class="col col-stats ms-sm-0 ms-3">
                            <div class="numbers">
                                <p class="card-category">Grandtotal</p>
                                <h4 class="card-title" id="grand-total"></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="card card-stats card-round">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-icon">
                            <div class="icon-big icon-info bubble-shadow-small text-center">
                                <i class="fas fa-donate"></i>
                            </div>
                        </div>
                        <div class="col col-stats ms-sm-0 ms-3">
                            <div class="numbers">
                                <p class="card-category">Subtotal</p>
                                <h4 class="card-title" id="sub-total"></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="card card-stats card-round">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-icon">
                            <div class="icon-big icon-success bubble-shadow-small text-center">
                                <i class="fab fa-creative-commons-nc"></i>
                            </div>
                        </div>
                        <div class="col col-stats ms-sm-0 ms-3">
                            <div class="numbers">
                                <p class="card-category">Discount</p>
                                <h4 class="card-title" id="discount"></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="card card-stats card-round">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-icon">
                            <div class="icon-big icon-secondary bubble-shadow-small text-center">
                                <i class="fab fa-cuttlefish"></i>
                            </div>
                        </div>
                        <div class="col col-stats ms-sm-0 ms-3">
                            <div class="numbers">
                                <p class="card-category">Returned Items Count</p>
                                <h4 class="card-title" id="return-total"></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <section class="pt-3">
        <div class="container-fluid">

            {{-- <div class="row">
                <div class="col-xxl-3">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="fw-bold fs-4 card-title">
                                Grand Total
                            </h5>
                            <p class="fs-4" id="grand-total"></p>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-3">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="fw-bold fs-4 card-title">
                                Subtotal Total
                            </h5>
                            <p class="fs-4" id="sub-total"></p>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-3">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="fw-bold fs-4">
                                Discount
                            </h5>
                            <p class="fs-4" id="discount"></p>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-3">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="fw-bold fs-4">
                                Item Returned Total
                            </h5>
                            <p class="fs-4" id="return-total"></p>
                        </div>
                    </div>
                </div>
            </div> --}}

            <div class="">
                <canvas id="chart-summary" height="300" width="600"></canvas>
            </div>

        </div>
    </section>
@endsection
@section('script')
    <script>
        const periodSelect = document.getElementById("period");

        periodSelect.addEventListener('change', () => getAnalytics(periodSelect));

        //invoke the function
        getAnalytics(periodSelect);

        async function getAnalytics(period) {
            const config = {
                headers: {
                    Accept: "application/json",
                },
                params: {
                    period: period.value,
                },
            };
            try {
                const res = await axios.get("{{ route('report.sale.get.analytics') }}", config);
                const {
                    record,
                    itemReturnedSum,
                    hourSales
                } = res.data;


                const grandTotal = parseFloat(record.grandtotal) - parseFloat(itemReturnedSum.return_total);

                document.getElementById('grand-total').textContent = '₵ ' + grandTotal;
                document.getElementById('sub-total').textContent = '₵ ' + record.subtotal;
                document.getElementById('discount').textContent = '₵ ' + record.discount;
                document.getElementById('return-total').textContent = '₵ ' + itemReturnedSum.return_total;

                // Pass adjusted sales to chart
                getChart(hourSales);
            } catch (err) {
                console.error(err);
            }
        }

        let chartInstances = []; // Array to store chart instances


        function getChart(perHourValues) {

            const labels = perHourValues.map(bc => bc.hour);
            const grandtotal = perHourValues.map(bc => bc.grandtotal);

            Chart.defaults.set("plugins.datalabels", {
                color: "#AA520B",
            });

            // Destroy existing charts
            chartInstances.forEach(chart => {
                chart.destroy();
            });


            chartInstances = []; // Clear the array
            const ctx = document.getElementById("chart-summary").getContext("2d");

            //handle the percentage
            const myChart = new Chart(ctx, {
                type: "bar",
                data: {
                    labels: labels,
                    datasets: [{
                        label: "Transaction Per Hour",
                        data: grandtotal,
                        backgroundColor: [
                            "rgb(255, 99, 132)",
                            "rgb(255, 159, 64)",
                            "rgb(255, 205, 86)",
                            "rgb(75, 192, 192)",
                            "rgb(54, 162, 235)",
                            "rgb(153, 102, 255)",
                            "rgb(201, 203, 207)",
                        ],
                        borderColor: [
                            "rgb(255, 99, 132)",
                            "rgb(255, 159, 64)",
                            "rgb(255, 205, 86)",
                            "rgb(75, 192, 192)",
                            "rgb(54, 162, 235)",
                            "rgb(153, 102, 255)",
                            "rgb(201, 203, 207)",
                        ],
                        borderWidth: 1,
                    }, ],
                },
                options: {
                    responsive: true,
                    layout: {
                        padding: 30,
                    },
                    plugins: {
                        datalabels: {
                            anchor: "end",
                            align: "end",
                            color: "black",
                            font: {
                                weight: "bold",
                            },
                            formatter: function(value) {
                                return "₵ " + value.toString();
                            },
                        },
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return "GHS₵ " + value.toString();
                                },
                                stepSize: 1,
                            },
                        },
                    },
                    animations: {
                        tension: {
                            duration: 1000,
                            easing: 'linear',
                            from: 1,
                            to: 0,
                            loop: true
                        }
                    }
                },
            });

            chartInstances.push(myChart); // Store the new chart instance

        }
    </script>
@endsection
