@extends('layouts.app')
@section('title'){{ __('Dashboard') }} @endsection

@section('content')
    <main class="content">
        <div class="container-fluid p-0">

            <h1 class="h3 mb-3"><strong>{{ __('Dashboard') }}</strong></h1>
            <div class="row">
                <div class="col-xl-12 col-xxl-12">
                    <div class="card flex-fill w-100">
                        <div class="card-header">
                            <div class="float-end">
                                <form class="row g-2">
                                    <div class="col-auto">
                                        <div class="form-control form-control-sm bg-light border-0 text-center chart-current-balance" canvas-bind="all"></div>
                                    </div>
                                    <div class="col-auto">
                                        <select class="form-select form-select-sm bg-light border-0 chart-wallet-type-selector" canvas-bind="all">
                                            <option value="all">{{ __('All') }}</option>
                                            <option value="wo">{{ __('Watch-only') }}</option>
                                            <option value="nwo" selected>{{ __('Not Watch-only') }}</option>
                                        </select>
                                    </div>
                                    <div class="col-auto">
                                        <select class="form-select form-select-sm bg-light border-0 chart-date-selector" canvas-bind="all">
                                            <option value="1h">{{ __('1 Hour') }}</option>
                                            <option value="3h">{{ __('3 Hours') }}</option>
                                            <option value="12h">{{ __('12 Hours') }}</option>
                                            <option value="24h">{{ __('24 Hours') }}</option>
                                            <option value="week">{{ __('Week') }}</option>
                                            <option value="month">{{ __('1 Month') }}</option>
                                            <option value="3month">{{ __('3 Months') }}</option>
                                            <option value="6month">{{ __('6 Months') }}</option>
                                            <option value="12month">{{ __('12 Months') }}</option>
                                        </select>
                                    </div>
                                </form>
                            </div>
                            <h5 class="card-title mb-0">{{ __('Assets chart') }}</h5>
                        </div>
                        <div class="card-body py-3">
                            <div class="chart chart-sm">
                                <canvas id="all-chart-rate"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xl-6 col-xxl-6">
                    <div class="card flex-fill w-100">
                        <div class="card-header">
                            <div class="float-end">
                                <form class="row g-2">
                                    <div class="col-auto">
                                        <div class="form-control form-control-sm bg-light border-0 text-center chart-current-balance" canvas-bind="crypto"></div>
                                    </div>
                                    <div class="col-auto">
                                        <select class="form-select form-select-sm bg-light border-0 chart-wallet-type-selector" canvas-bind="crypto">
                                            <option value="all">{{ __('All') }}</option>
                                            <option value="wo">{{ __('Watch-only') }}</option>
                                            <option value="nwo" selected>{{ __('Not Watch-only') }}</option>
                                        </select>
                                    </div>
                                    <div class="col-auto">
                                        <select class="form-select form-select-sm bg-light border-0 chart-date-selector" canvas-bind="crypto">
                                            <option value="1h">{{ __('1 Hour') }}</option>
                                            <option value="3h">{{ __('3 Hours') }}</option>
                                            <option value="12h">{{ __('12 Hours') }}</option>
                                            <option value="24h">{{ __('24 Hours') }}</option>
                                            <option value="week">{{ __('Week') }}</option>
                                            <option value="month">{{ __('1 Month') }}</option>
                                            <option value="3month">{{ __('3 Months') }}</option>
                                            <option value="6month">{{ __('6 Months') }}</option>
                                            <option value="12month">{{ __('12 Months') }}</option>
                                        </select>
                                    </div>
                                </form>
                            </div>
                            <h5 class="card-title mb-0">{{ __('Crypto chart') }}</h5>
                        </div>
                        <div class="card-body py-3">
                            <div class="chart chart-sm">
                                <canvas id="crypto-chart-rate"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-6 col-xxl-6">
                    <div class="card flex-fill w-100">
                        <div class="card-header">
                            <div class="float-end">
                                <form class="row g-2">
                                    <div class="col-auto">
                                        <div class="form-control form-control-sm bg-light border-0 text-center chart-current-balance" canvas-bind="token"></div>
                                    </div>
                                    <div class="col-auto">
                                        <select class="form-select form-select-sm bg-light border-0 chart-wallet-type-selector" canvas-bind="token">
                                            <option value="all">{{ __('All') }}</option>
                                            <option value="wo">{{ __('Watch-only') }}</option>
                                            <option value="nwo" selected>{{ __('Not Watch-only') }}</option>
                                        </select>
                                    </div>
                                    <div class="col-auto">
                                        <select class="form-select form-select-sm bg-light border-0 chart-date-selector"
                                                canvas-bind="token">
                                            <option value="1h">{{ __('1 Hour') }}</option>
                                            <option value="3h">{{ __('3 Hours') }}</option>
                                            <option value="12h">{{ __('12 Hours') }}</option>
                                            <option value="24h">{{ __('24 Hours') }}</option>
                                            <option value="week">{{ __('Week') }}</option>
                                            <option value="month">{{ __('1 Month') }}</option>
                                            <option value="3month">{{ __('3 Months') }}</option>
                                            <option value="6month">{{ __('6 Months') }}</option>
                                            <option value="12month">{{ __('12 Months') }}</option>
                                        </select>
                                    </div>
                                </form>
                            </div>
                            <h5 class="card-title mb-0">{{ __('Tokens chart') }}</h5>
                        </div>
                        <div class="card-body py-3">
                            <div class="chart chart-sm">
                                <canvas id="token-chart-rate"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </main>
@endsection
@section('styles')
    <style>
        .chart-current-balance {
            min-height: 0;
        }
    </style>
    @endsection
@push('scripts')
    <script>
        var charts = {
            'token': null,
            'crypto': null,
            'all': null,
        }
        $(() => {
            Object.keys(charts).forEach(function (k) {
                let ctx = document.getElementById(`${k}-chart-rate`).getContext("2d");
                <?php if(auth()->user()->color_scheme == 'light'): ?>
                let gradient = ctx.createLinearGradient(0, 0, 0, 225);
                gradient.addColorStop(0, "rgba(215, 227, 244, 1)");
                gradient.addColorStop(1, "rgba(215, 227, 244, 0)");
                <?php else: ?>
                let gradient = ctx.createLinearGradient(0, 0, 0, 225);
                gradient.addColorStop(0, "rgba(51, 66, 84, 1)");
                gradient.addColorStop(1, "rgba(0, 0, 0, 0)");
                <?php endif; ?>
                    charts[k] = new Chart(document.getElementById(`${k}-chart-rate`), {
                    type: "line",
                    data: {
                        labels: [],
                        datasets: [{
                            label: "USD",
                            fill: true,
                            backgroundColor: gradient,
                            borderColor: window.theme.primary,
                            data: [],
                        }]
                    },
                    options: {
                        maintainAspectRatio: false,
                        legend: {
                            display: false
                        },
                        tooltips: {
                            intersect: false
                        },
                        hover: {
                            intersect: true
                        },
                        plugins: {
                            filler: {
                                propagate: false
                            }
                        },
                        scales: {
                            xAxes: [{
                                reverse: true,
                                gridLines: {
                                    color: "rgba(0,0,0,0.0)"
                                }
                            }],
                            yAxes: [{
                                display: true,
                                borderDash: [3, 3],
                                gridLines: {
                                    color: "rgba(0,0,0,0.0)"
                                }
                            }],
                        }
                    }
                });

            })

            function BalanceChart(labels, data, type) {
                charts[type].data.labels = labels
                charts[type].data.datasets[0].data = data
                charts[type].update()
                $(`.chart-current-balance[canvas-bind=${type}]`).text(`${data[Object.keys(data).length-1] ?? 0}$`);
            }

            function refreshData() {
                Object.keys(charts).forEach((type) => {
                    fetchBalancesChart($(`.chart-date-selector[canvas-bind="${type}"]`).val(), $(`.chart-wallet-type-selector[canvas-bind="${type}"]`).val(), type)
                })
                dataRefreshed()
            }

            $('#currentTime').click(function () {
                refreshData();
            })

            refreshData()
            setInterval(() => {
                refreshData()
                dataRefreshed()
            }, 10000)

            $('.chart-date-selector').change(function () {
                fetchBalancesChart($(this).val(), $(`.chart-wallet-type-selector[canvas-bind="${$(this).attr('canvas-bind')}"]`).val(), $(this).attr('canvas-bind'))
            });

            $('.chart-wallet-type-selector').change(function () {
                fetchBalancesChart($(`.chart-date-selector[canvas-bind="${$(this).attr('canvas-bind')}"]`).val(), $(`.chart-wallet-type-selector[canvas-bind="${$(this).attr('canvas-bind')}"]`).val(), $(this).attr('canvas-bind'))
            });

            function fetchBalancesChart(period = '24h',wallet_type="nwo", type = "all") {
                fetch(`{{ route('api.charts.observers.rates') }}?api_token={{ auth()->user()->api_token }}&period=${period}&type=${type}&wallet_type=${wallet_type}`, {
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                }).then(resp => resp.json()).then(resp => {
                    BalanceChart(Object.keys(resp), Object.values(resp), type)
                })
            }
        })
    </script>
@endpush
