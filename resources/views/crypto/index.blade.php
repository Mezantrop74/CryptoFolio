@extends('layouts.app')
@section('title'){{ __('Your Assets') }} @endsection
@section('content')
    <main class="content">
        <div class="container-fluid p-0">
            <h1 class="h3 mb-3">{{ __('Your Assets') }}</h1>
            <div class="row">
                <div class="col-xl-6 col-xxl-5 d-flex">
                    <div class="w-100">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col mt-0">
                                                <h5 class="card-title">{{ __('Crypto USD Amount') }}</h5>
                                            </div>

                                            <div class="col-auto">
                                                <div class="stat text-primary">
                                                    <i class="align-middle" data-feather="dollar-sign"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <h1 class="mt-1 mb-3"><span class="total-crypto-usd-balance">0</span><small
                                                class="text-muted"> USD</small></h1>
                                        <div class="mb-0 invisible">
                                            <span class="text-success"> <i class="mdi mdi-arrow-bottom-right"></i> 6.65% </span>
                                            <span class="text-muted">Since last week</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="card">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col mt-0">
                                                <h5 class="card-title">WO {{ __('Crypto USD Amount') }}</h5>
                                            </div>

                                            <div class="col-auto">
                                                <div class="stat text-primary">
                                                    <i class="align-middle" data-feather="dollar-sign"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <h1 class="mt-1 mb-3"><span class="total-wo-crypto-usd-balance">0</span><small
                                                class="text-muted"> USD</small></h1>
                                        <div class="mb-0 invisible">
                                            <span class="text-success"> <i class="mdi mdi-arrow-bottom-right"></i> 6.65% </span>
                                            <span class="text-muted">Since last week</span>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="col-sm-6">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col mt-0">
                                                <h5 class="card-title">{{ __('Tokens USD Amount') }}</h5>
                                            </div>

                                            <div class="col-auto">
                                                <div class="stat text-primary">
                                                    <i class="align-middle" data-feather="dollar-sign"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <h1 class="mt-1 mb-3"><span class="total-tokens-usd-balance">0</span><small
                                                class="text-muted"> USD</small></h1>
                                        <div class="mb-0 invisible">
                                            <span class="text-success"> <i class="mdi mdi-arrow-bottom-right"></i> 6.65% </span>
                                            <span class="text-muted">Since last week</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col mt-0">
                                                <h5 class="card-title">WO {{ __('Tokens USD Amount') }}</h5>
                                            </div>

                                            <div class="col-auto">
                                                <div class="stat text-primary">
                                                    <i class="align-middle" data-feather="dollar-sign"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <h1 class="mt-1 mb-3"><span class="total-wo-tokens-usd-balance">0</span><small
                                                class="text-muted"> USD</small></h1>
                                        <div class="mb-0 invisible">
                                            <span class="text-success"> <i class="mdi mdi-arrow-bottom-right"></i> 6.65% </span>
                                            <span class="text-muted">Since last week</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-6 col-xxl-7">
                    <div class="card flex-fill w-100">
                        <div class="card-header">
                            <div class="float-end">
                                <form class="row g-2">
                                    <div class="col-auto">
                                        <select class="form-select form-select-sm bg-light border-0" id="leftObserver">
                                            @foreach($observers as $o)
                                                <option value="{{ $o->observer_id }}">{{ $o->crypto->symbol }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-auto">
                                        <select class="form-select form-select-sm bg-light border-0" id="rightObserver">
                                            @foreach($observers as $k => $o)
                                                <option @if($k == 1) selected
                                                        @endif value="{{ $o->observer_id }}">{{ $o->crypto->symbol }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-auto">
                                        <select class="form-select form-select-sm bg-light border-0"
                                                id="chartDateSelector">
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
                            <h5 class="card-title mb-0">{{ __('Convert chart') }}</h5>
                        </div>
                        <div class="card-body py-3">
                            <div class="chart chart-sm">
                                <canvas id="convert-chart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="w-100">
                    <div class="row">
                        <div class="col-md-12 col-lg-12 col-xxl-8">
                            @if(session('success'))
                                <div class="alert alert-primary alert-dismissible" role="alert">
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                            aria-label="Close"></button>
                                    <div class="alert-message">
                                        {{ session('success') }}
                                    </div>
                                </div>
                            @endif
                            @if(session('error'))
                                <div class="alert alert-danger alert-dismissible" role="alert">
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                            aria-label="Close"></button>
                                    <div class="alert-message">
                                        {{ session('error') }}
                                    </div>
                                </div>
                            @endif
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <div class="alert-message">
                                        @foreach ($errors->all() as $error)
                                            {{ $error }}<br>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col">
                                            <h5 class="card-title">{{ __('Your Assets') }}</h5>
                                        </div>

                                        <div class="col-auto">
                                            <div class="stat text-primary">
                                                <i class="align-middle" data-feather="eye"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <form action="{{ route('crypto.observer.custom') }}" method="POST"
                                          style="display: none;" id="customTickerForm">
                                        @csrf
                                        <div class="row g-2">
                                            <div class="col-lg-3 col-sm-auto">
                                                <div class="input-group">
                                                    <input type="text" class="form-control" name="ticker"
                                                           placeholder="{{ __('Custom ticker') }}">
                                                    <button type="button" id="showTickerForm"
                                                            class="btn btn-outline-secondary"><span
                                                            data-feather="list"></span></button>
                                                </div>

                                            </div>
                                            <div class="col-lg-3 col-sm-auto">
                                                <button type="submit"
                                                        class="btn btn-outline-primary">{{ __('Add') }}</button>
                                            </div>
                                        </div>
                                    </form>
                                    <form action="{{ route('crypto.observer.create') }}" method="POST" id="tickerForm">
                                        @csrf
                                        <div class="row g-2">
                                            <div class="col-lg-3 col-sm-auto">
                                                <div class="input-group">
                                                    <select class="form-control crypto-select" name="crypto_id">
                                                        <option value="">{{ __('Select crypto') }}</option>
                                                        @foreach($notObservableCryptos as $c)
                                                            <option value="{{ $c->id }}">{{ $c->name }}({{ $c->symbol }}
                                                                )
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <button type="button" id="showCustomTickerForm"
                                                            class="btn btn-outline-secondary"><span
                                                            data-feather="plus"></span></button>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-4">
                                                <button type="submit"
                                                        class="btn btn-outline-primary">{{ __('Add') }}</button>
                                            </div>
                                        </div>
                                    </form>

                                    <div class="table-responsive mt-2">
                                        <table class="table" id="observersTable">
                                            <thead>
                                            <tr>
                                                <th></th>
                                                <th>{{ __('Name') }}</th>
                                                <th>{{ __('Ticker') }}</th>
                                                <th>{{ __('Rate') }}</th>
                                                <th>{{ __('24h') }} ($)</th>
                                                <th>{{ __('24h') }} (%)</th>
                                                <th>{{ __('Wallets') }}</th>
                                                <th>{{ __('Balance') }}</th>
                                                <th>{{ __('USD Amount') }}</th>
                                                <th></th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($observers as $o)
                                                <tr class="observer-{{ $o->observer_id }}"
                                                    data-id="{{ $o->observer_id }}">
                                                    <td class="details-control"><span class="text-primary"
                                                                                      data-feather="plus"></span></td>
                                                    <td>
                                                        <a href="{{ route('crypto.observer.show', ['observer_id' => $o->observer_id]) }}">{{ $o->crypto->name }}</a>
                                                    </td>
                                                    <td>{{ $o->crypto->symbol }}</td>
                                                    <td class="observer-rate-usd">0</td>
                                                    <td class="observer-usd-change"></td>
                                                    <td class="observer-percent-change"></td>
                                                    <td class="observer-wallets-count"></td>
                                                    <td class="observer-total-balance"></td>
                                                    <td class="observer-total-usd-balance"></td>
                                                    <td>
                                                        <form method="POST"
                                                              action="{{ route('crypto.observer.delete') }}"
                                                              class="form-inline form-with-confirm">
                                                            @csrf
                                                            <input type="hidden" name="observer_id"
                                                                   value="{{ $o->observer_id }}">
                                                            <button data-bs-toggle="modal"
                                                                    data-bs-target="#addWalletBackdrop" type="button"
                                                                    data-observer="{{ $o->observer_id }}"
                                                                    data-observer-crypto="{{ $o->crypto->symbol }}"
                                                                    class="btn btn-sm btn-link text-success p-0 add-wallet">
                                                                <span data-feather="plus"></span></button>
                                                            <button type="submit"
                                                                    class="btn btn-sm btn-link text-danger p-0">
                                                                <span data-feather="trash"></span></button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </main>
    @include('crypto.popups.create_wallet')
    @include('crypto.popups.airdrop')
    <div class="modal" id="debankModal" tabindex="-1">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <small class="modal-title"><a href="" target="_blank"></a></small>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h2 class="text-center"></h2>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('styles')
    <link rel="stylesheet" href="{{ asset('static/css/datatables/dataTables.bootstrap5.min.css') }}">
    @if(auth()->user()->color_scheme == 'light')
        <link rel="stylesheet" href="{{ asset('static/css/select2/select2.css') }}"/>
        <link rel="stylesheet" href="{{ asset('static/css/select2/select2.bootstrap.css') }}"/>
    @else
        <link rel="stylesheet" href="{{ asset('static/css/select2/dark.select2.css') }}"/>
        <link rel="stylesheet" href="{{ asset('static/css/select2/dark.select2.bootstrap.css') }}"/>
    @endif


@endsection
@push('scripts')
    <script src="{{ asset('static/js/select2.min.js') }}"></script>
    <script src="{{ asset('static/js/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('static/js/datatables/dataTables.bootstrap5.min.js') }}"></script>


    <script>

        var convertChart, observerTable

        $(() => {
            $(document).on('select2:open', () => {
                document.querySelector('.select2-search__field').focus();
            });

            var ctx = document.getElementById("convert-chart").getContext("2d");
            @if(auth()->user()->color_scheme == 'light')
            var gradient = ctx.createLinearGradient(0, 0, 0, 225);
            gradient.addColorStop(0, "rgba(215, 227, 244, 1)");
            gradient.addColorStop(1, "rgba(215, 227, 244, 0)");
            @else
            var gradient = ctx.createLinearGradient(0, 0, 0, 225);
            gradient.addColorStop(0, "rgba(51, 66, 84, 1)");
            gradient.addColorStop(1, "rgba(0, 0, 0, 0)");
            @endif

            // Line chart
            convertChart = new Chart(document.getElementById("convert-chart"), {
                type: "line",
                data: {
                    labels: [],
                    datasets: [{
                        label: "",
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

            $('#chartDateSelector').change(function () {
                fetchConvertChart($('#chartDateSelector').val(), $('#leftObserver').val(), $('#rightObserver').val())
            });

            $('.form-with-confirm').submit(function () {
                return confirm("{{ __("Do you really want to submit the form?") }}");
            })

            $('.crypto-select').select2({
                theme: "bootstrap-5",
            });
            refreshData()
            setInterval(() => {
                refreshData();
            }, 10000)

            observerTable = $('#observersTable').DataTable({
                "order": [[8, "desc"]],
                "paging": false,
                "bInfo": false,
                "searching": false,
                @if(auth()->user()->lang == 'ru')
                "language": {
                    'url': '{{ asset('static/js/datatables/lang/ru.json') }}'
                }
                @endif
            }).draw();

            $('#leftObserver,#rightObserver').change(function () {
                if ($('#leftObserver').val().length > 0 && $('#rightObserver').val().length > 0) {
                    fetchConvertChart($('#chartDateSelector').val(), $('#leftObserver').val(), $('#rightObserver').val())
                }
            });

            function refreshData() {
                fetchStats()
                dataRefreshed()
                fetchConvertChart($('#chartDateSelector').val(), $('#leftObserver').val(), $('#rightObserver').val())
            }

            $('#currentTime').click(function () {
                refreshData();
            })

            function updateWallet(walletId, note, callback) {
                fetch('{{ route('api.wallets.update') }}?api_token={{ auth()->user()->api_token }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        'wallet_id': walletId,
                        'note': note
                    }),

                })
                    .then(response => response.json())
                    .then(response => {
                        if (response.error) {
                            throw Error('Comment update error');
                        }
                        callback(response)
                    }).catch(error => {
                    alert(error)
                });
            }


            function fetchConvertChart(period = "24h", leftObserver, rightObserver) {
                if (!leftObserver || !rightObserver) {
                    return
                }
                fetch(`{{ route('api.charts.convert') }}?api_token={{ auth()->user()->api_token }}&left_observer=${leftObserver}&right_observer=${rightObserver}&period=${period}`, {
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                }).then(resp => resp.json()).then(resp => {
                    ConvertChart(Object.keys(resp), Object.values(resp))
                })
            }

            function fetchStats() {
                fetch('{{ route('api.observer.stats.all') }}?api_token={{ auth()->user()->api_token }}&period=24h', {
                    headers: {
                        'Content-Type': 'application/json'
                    },
                }).then(resp => resp.json()).then(resp => {
                    refreshObservers(resp)
                })
            }

            function ConvertChart(labels, data) {
                convertChart.data.labels = labels
                convertChart.data.datasets[0].data = data
                convertChart.update()
            }

            function refreshObservers(stats) {
                $('.total-crypto-usd-balance').text(stats.crypto.usd)
                $('.total-wo-crypto-usd-balance').text(stats.crypto.wo_usd)
                $('.total-tokens-usd-balance').text(stats.tokens.usd)
                $('.total-wo-tokens-usd-balance').text(stats.tokens.wo_usd)
                Object.keys(stats.observers).forEach((obsId) => {
                    $(`.observer-${obsId} > .observer-rate-usd`).text(stats.observers[obsId].rate)
                    $(`.observer-${obsId} > .observer-total-balance`).text(stats.observers[obsId].total.crypto)
                    $(`.observer-${obsId} > .observer-total-usd-balance`).text(stats.observers[obsId].total.usd)
                    $(`.observer-${obsId} > .observer-wallets-count`).text(Object.keys(stats.observers[obsId].wallets).length)
                    $(`.observer-${obsId} > .observer-percent-change`).text(`${stats.observers[obsId].change.percent}%`)
                        .addClass(stats.observers[obsId].change.positive ? 'text-success' : 'text-danger')
                    $(`.observer-${obsId} > .observer-usd-change`).text(`${stats.observers[obsId].change.usd}`)
                        .addClass(stats.observers[obsId].change.positive ? 'text-success' : 'text-danger')

                    let walletsTable = $(`.wallets-${obsId}`)
                    let woWalletsTable = $(`.wo-wallets-${obsId}`)
                    let walletsTbody = walletsTable.find('tbody')
                    let woWalletsTbody = woWalletsTable.find('tbody')
                    if (walletsTable.length > 0) {
                        walletsTbody.html('')
                        Object.keys(stats.observers[obsId].wallets).forEach((wId) => {
                            if ($(`.wallet-${wId}`).length === 0) {
                                let tr = $('<tr>')
                                tr.addClass(`wallet-${wId}`)
                                tr.html(`<td class="wallet-name"></td>
            <td class="wallet-total-balance">0</td>
            <td class="wallet-total-usd-balance">0</td>
            <td class="wallet-note" style="max-width:100px;"><textarea data-wallet-id=""
                                              autocomplete="off"
                                              class="form-control form-control-sm wallet-note-text"
                                              rows="1"></textarea></td>
            <td>
                <form method="POST" action="{{ route('crypto.wallet.delete') }}"
                      class="form-inline form-with-confirm wallet-delete-form">
                    @csrf
                                <input type="hidden" name="wallet_id"
                                       value="">
                                <button type="submit" class="btn btn-sm btn-link text-danger">
                                    <span data-feather="trash"></span></button>
                            </form>
                        </td>`)
                                walletsTbody.append(tr)
                            }
                            $(`.wallet-${wId} > .wallet-name`).text(stats.observers[obsId].wallets[wId].name)
                            $(`.wallet-${wId} > .wallet-total-balance`).text(parseFloat(parseFloat(stats.observers[obsId].wallets[wId].crypto).toFixed(4)))
                            $(`.wallet-${wId} > .wallet-total-usd-balance`).text(stats.observers[obsId].wallets[wId].usd)
                            $(`.wallet-${wId} form.wallet-delete-form input[name="wallet_id"]`).val(wId)
                            $(`.wallet-${wId} .wallet-note-text`).attr('data-wallet-id', wId)
                            $(`.wallet-${wId} .wallet-note-text`).text(stats.observers[obsId].wallets[wId].note)
                            $(`.wallet-${wId} button span`).attr('data-feather', 'trash')

                            $(`.wallet-${wId} .wallet-note-text`).change(function () {
                                updateWallet($(this).attr('data-wallet-id'), $(this).val(), function (resp) {
                                    if (resp && resp.data && resp.data.wallet_id) {
                                        $(`textarea[data-wallet-id=${resp.data.wallet_id}]`).addClass('is-valid')
                                    } else {
                                        $(`textarea[data-wallet-id=${resp.data.wallet_id}]`).addClass('is-invalid')
                                    }
                                })
                            })
                        })
                        feather.replace()
                    }

                    if (woWalletsTable.length > 0) {
                        woWalletsTbody.html('')
                        Object.keys(stats.observers[obsId].wo_wallets).forEach((wId) => {
                            if ($(`.wallet-${wId}`).length === 0) {
                                let tr = $('<tr>')
                                tr.addClass(`wallet-${wId}`)
                                tr.html(`<td style="max-width:100px"><small class="wallet-address text-muted"></small></td>
            <td class="wallet-name"></td>
            <td class="wallet-total-balance">0</td>
            <td class="wallet-total-usd-balance">0</td>
            <td class="wallet-note" style="max-width:100px;"><textarea data-wallet-id=""
                                              autocomplete="off"
                                              class="form-control form-control-sm wallet-note-text"
                                              rows="1"></textarea></td>
                                            <td>
                                            <button
                                                        class="btn btn-sm btn-link text-primary text-decoration-none airdrop-btn"
                                                        data-wallet-id="">A
                                                    </button>
                                            <button
                                                        class="btn btn-sm btn-link text-primary text-decoration-none debank-btn"
                                                        data-wallet-id="">D
                                                    </button></td>
            <td>
                <form method="POST" action="{{ route('crypto.wallet.delete') }}"
                      class="form-inline form-with-confirm wallet-delete-form">
                    @csrf
                                <input type="hidden" name="wallet_id"
                                       value="">
                                <button type="submit" class="btn btn-sm btn-link text-danger">
                                    <span data-feather="trash"></span></button>
                            </form>
                        </td>`)
                                woWalletsTbody.append(tr)
                            }
                            $(`.wallet-${wId} > td > .wallet-address`).text(stats.observers[obsId].wo_wallets[wId].address)
                            $(`.wallet-${wId} > .wallet-name`).text(stats.observers[obsId].wo_wallets[wId].name)
                            $(`.wallet-${wId} > .wallet-total-balance`).text(parseFloat(parseFloat(stats.observers[obsId].wo_wallets[wId].crypto).toFixed(4)))
                            $(`.wallet-${wId} > .wallet-total-usd-balance`).text(stats.observers[obsId].wo_wallets[wId].usd)
                            $(`.wallet-${wId} form.wallet-delete-form input[name="wallet_id"]`).val(wId)
                            $(`.wallet-${wId} .wallet-note-text`).attr('data-wallet-id', wId)
                            $(`.wallet-${wId} .wallet-note-text`).text(stats.observers[obsId].wo_wallets[wId].note)
                            $(`.wallet-${wId} button span`).attr('data-feather', 'trash')

                            $(`.wallet-${wId} .wallet-note-text`).change(function () {
                                updateWallet($(this).attr('data-wallet-id'), $(this).val(), function (resp) {
                                    if (resp && resp.data && resp.data.wallet_id) {
                                        $(`textarea[data-wallet-id=${resp.data.wallet_id}]`).addClass('is-valid')
                                    } else {
                                        $(`textarea[data-wallet-id=${resp.data.wallet_id}]`).addClass('is-invalid')
                                    }
                                })
                            })
                            $(`.wallet-${wId} .debank-btn,.wallet-${wId} .airdrop-btn`).attr('data-wallet-id', wId)
                            $(`.wallet-${wId} .debank-btn`).click(function () {
                                fetch(`{{ route('api.wallets.debank') }}?api_token={{ auth()->user()->api_token }}&wallet_id=${$(this).attr('data-wallet-id')}`, {
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'Accept': 'application/json'
                                    },
                                }).then(resp => resp.json()).then(resp => {
                                    $(this).text(resp.balance);
                                    let debankModal = new bootstrap.Modal(document.getElementById('debankModal'))
                                    $('#debankModal .modal-title a').text(resp.wallet).attr('href', resp.link)
                                    $('#debankModal h2').text(resp.balance)
                                    debankModal.show()
                                })
                            });

                            $(`.wallet-${wId} .airdrop-btn`).click(function () {
                                fetch(`{{ route('api.wallets.airdrop') }}?api_token={{ auth()->user()->api_token }}&wallet_id=${$(this).attr('data-wallet-id')}`, {
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'Accept': 'application/json'
                                    },
                                }).then(resp => resp.json()).then(resp => {
                                    let airDropModal = new bootstrap.Modal(document.getElementById('airdropModal'))
                                    let tbody = $('#airdropModal tbody');
                                    $('#airdropModal .modal-title a').text(resp.wallet).attr('href', resp.link)
                                    tbody.html('');
                                    Object.keys(resp.unclaimed).forEach(function (k) {
                                        let tr = $('<tr>')
                                        tr.html(`<td class="name small"></td><td class="value small"></td>`);
                                        tr.find('.name').text(k)
                                        tr.find('.value').text(resp.unclaimed[k])
                                        tr.appendTo(tbody)
                                    })

                                    airDropModal.show();
                                })
                            });
                        })
                        feather.replace()
                    }

                })
                observerTable.rows().invalidate().draw(false);
            }

            $('.add-wallet').click(function () {
                $('#addWalletsForm .wallet-name:not(:first),#addWalletsForm .wallet-balance:not(:first),#addWalletsForm .wallet-note:not(:first)').remove();
                $('#addWalletsForm input[name!="_token"], #addWalletsForm textarea').val('');
                $('#addWalletsForm input[name="observer_id"]').val($(this).attr('data-observer'))
                $('#addWalletBackdropLabel').text(`{{ __('Add wallets') }} ${$(this).attr('data-observer-crypto')}`)
            })

            $('#showCustomTickerForm,#showTickerForm').click(function () {
                $('#customTickerForm,#tickerForm').toggle();
            })

            function format(d) {
                let div = $('<div>')
                let body = `
            <h6 class="text-muted">{{ __('Wallets') }}</h6>
    <table class="table table-wallets table-sm small">
    <thead>
    <tr>
        <th>{{ __('Name') }}</th>
        <th>{{ __('Balance') }}</th>
        <th>{{ __('USD Amount') }}</th>
        <th>{{ __('Note') }}</th>
        <th></th>
    </tr>
    </thead>
    <tbody>
</tbody>
</table>
            <h6 class="text-muted">Watch-only {{ __('Wallets') }}</h6>
    <table class="table table-wo-wallets table-sm small">
    <thead>
    <tr>
        <th>{{ __('Address') }}</th>
        <th>{{ __('Name') }}</th>
        <th>{{ __('Balance') }}</th>
        <th>{{ __('USD Amount') }}</th>
        <th>{{ __('Note') }}</th>
        <th></th>
        <th></th>
    </tr>
    </thead>
    <tbody>
</tbody>
</table>
`
                div.html(body)
                div.find('.table-wallets').addClass(`wallets-${d}`)
                div.find('.table-wo-wallets').addClass(`wo-wallets-${d}`)
                return div.html()
            }

            $('.details-control').click(function () {
                $(this).html(function () {
                    return $(this).find('.feather-plus').length === 0 ? `<span class="text-primary" data-feather="plus"></span>` : `<span data-feather="minus"></span>`
                });
                feather.replace();
                refreshData()
                var tr = $(this).parents('tr');
                var row = observerTable.row(tr);

                if (row.child.isShown()) {
                    row.child.hide();
                    tr.removeClass('shown');
                } else {
                    row.child(format($(tr).attr('data-id'))).show();
                    tr.addClass('shown');
                }
            });
        });
    </script>
@endpush
