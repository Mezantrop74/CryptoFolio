@extends('layouts.app')
@section('title'){{ $observer->crypto->name }} {{ __('Asset') }} @endsection
@section('content')
    <main class="content">
        <div class="container-fluid p-0">
            <div class="row mb-2 mb-xl-3">
                <div class="col-auto d-none d-sm-block">
                    <h1 class="h3 mb-3"><strong>{{ $observer->crypto->name }}</strong> {{ __('Asset') }}
                        @if($observer->crypto->ticker_type == \App\Domain\Ticker\Tickers::CUSTOM) <small
                            class="text-warning">{{ __('Not listed yet.') }}</small> @endif
                    </h1>
                </div>
            </div>

            @if($errors->any())
                <div class="row">
                    <div class="col-xl-6 col-xxl-5">
                        <div class="alert alert-danger">
                            <div class="alert-message">
                                @foreach ($errors->all() as $error)
                                    {{ $error }}<br>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            @if(session('error'))
                <div class="row">
                    <div class="col-xl-6 col-xxl-5">
                        <div class="alert alert-danger">
                            <div class="alert-message">
                                {{ session('error') }}
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            @if(session('success'))
                <div class="row">
                    <div class="col-xl-6 col-xxl-5">
                        <div class="alert alert-primary">
                            <div class="alert-message">
                                {{ session('success') }}
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            <div class="row">
                <div class="col-xl-6 col-xxl-5 d-flex">
                    <div class="w-100">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col mt-0">
                                                <h5 class="card-title">{{ __('Balance') }}</h5>
                                            </div>

                                            <div class="col-auto">
                                                <div class="stat text-primary">
                                                    <i class="align-middle" data-feather="credit-card"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <h1 class="mt-1 mb-3"><span class="total-balance">0</span><span
                                                class="text-secondary wo-total-balance"></span>
                                            <small class="text-muted"></small>
                                            <small class="text-muted"> {{ $observer->crypto->symbol }}</small>
                                        </h1>
                                        <div class="mb-0">
                                            <span class="text-danger"> <i class="mdi mdi-arrow-bottom-right"></i> &nbsp; </span>
                                            <span class="text-muted">&nbsp;</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col mt-0">
                                                <h5 class="card-title">{{ __('USD Amount') }}</h5>
                                            </div>

                                            <div class="col-auto">
                                                <div class="stat text-primary">
                                                    <i class="align-middle" data-feather="dollar-sign"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <h1 class="mt-1 mb-3"><span class="total-usd-balance">0</span><small
                                                class="text-muted"> USD</small></h1>
                                        <div class="mb-0">
                                            <span class="text-success total-balance-change"></span>
                                            <span class="text-muted period"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col mt-0">
                                                <h5 class="card-title">{{ __("Rate") }}</h5>
                                            </div>

                                            <div class="col-auto">
                                                <div class="stat text-primary">
                                                    <i class="align-middle" data-feather="divide"></i>
                                                </div>
                                            </div>
                                        </div>
                                        @if($observer->crypto->ticker_type == \App\Domain\Ticker\Tickers::CUSTOM)
                                            <form action="{{ route('crypto.rates.create') }}" method="POST"
                                                  class="mt-1 mb-3" id="updateRateForm" style="display: none;">
                                                @csrf
                                                <input type="hidden" name="observer_id"
                                                       value="{{ $observer->observer_id }}">
                                                <div class="col-lg-8">
                                                    <div class="input-group">
                                                        <input type="number" class="form-control"
                                                               placeholder="{{ __('Rate') }}" name="rate"
                                                               min="0.00000000000001" step="0.00000000000001">
                                                        <button type="submit" class="btn btn-outline-primary"><span
                                                                data-feather="save"></span></button>
                                                    </div>
                                                </div>
                                            </form>
                                        @endif
                                        <h1 id="rateBlock" class="mt-1 mb-3"><span class="rate-usd">0</span><small
                                                class="text-muted"> USD</small>
                                            @if($observer->crypto->ticker_type == \App\Domain\Ticker\Tickers::CUSTOM)
                                                <button type="button" class="btn btn-link p-0 m-0"
                                                        id="showUpdateRateFormBtn"><span data-feather="edit"></span>
                                                </button>
                                            @endif
                                        </h1>

                                        <div class="mb-0">
                                            <span class="text-success rate-change"></span>
                                            <span class="text-muted period"></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="card">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col mt-0">
                                                <h5 class="card-title">WO {{ __('USD Amount') }}</h5>
                                            </div>

                                            <div class="col-auto">
                                                <div class="stat text-primary">
                                                    <i class="align-middle" data-feather="dollar-sign"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <h1 class="mt-1 mb-3">
                                            @if(in_array($observer->crypto->cmc_id, $watchOnlyAllowedCMCIds))
                                                <span class="wo-total-usd-balance">0</span><small
                                                    class="text-muted"> USD</small>
                                            @else
                                                <span>-</span><small
                                                    class="text-muted"> USD</small>
                                            @endif
                                        </h1>
                                        <div
                                            class="mb-0 @if(!in_array($observer->crypto->cmc_id, $watchOnlyAllowedCMCIds)) invisible @endif">
                                            <span class="text-success wo-total-balance-change"></span>
                                            <span class="text-muted period"></span>
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
                                        <select class="form-select form-select-sm bg-light border-0"
                                                id="chartWalletTypeSelector">
                                            @if(in_array($observer->crypto->cmc_id, $watchOnlyAllowedCMCIds))
                                                <option value="all">{{ __('All') }}</option>
                                            @endif
                                            <option value="wo">{{ __('Watch-only') }}</option>
                                            @if(in_array($observer->crypto->cmc_id, $watchOnlyAllowedCMCIds))
                                                <option value="nwo" selected>{{ __('Not Watch-only') }}</option>
                                            @endif
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
                            <h5 class="card-title mb-0">{{ __('Rate chart') }}</h5>
                        </div>
                        <div class="card-body py-3">
                            <div class="chart chart-sm">
                                <canvas id="chart-rate"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-md-12 col-xxl-5 d-flex order-1 order-xxl-1">
                    <div class="card flex-fill">
                        <div class="card-header">
                            <div class="card-actions float-end">
                                    <span data-feather="plus" data-bs-toggle="modal"
                                          data-bs-target="#addWalletBackdrop" class="font-weight-bold add-wallet"
                                          style="cursor:pointer;"></span>
                            </div>
                            <h5 class="card-title mb-0">{{ __('Wallets') }}</h5>
                        </div>
                        <div class="card-body d-flex">
                            <div class="col-lg-12 table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>{{ __('Name') }}</th>
                                        <th>{{ __('Balance') }}</th>
                                        <th>{{ __('USD Amount') }}</th>
                                        <th>{{ __('Note') }}</th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($observer->wallets as $w)
                                        <tr class="wallet-{{ $w->wallet_id }}">
                                            <td><small class="text-muted">{{ substr($w->wallet_id, 0, 8) }}</small></td>
                                            <td>{{ $w->name }}</td>
                                            <td class="wallet-total-balance">0</td>
                                            <td class="wallet-total-usd-balance">0</td>
                                            <td class="wallet-note"><textarea data-wallet-id="{{ $w->wallet_id }}"
                                                                              autocomplete="off"
                                                                              class="form-control form-control-sm wallet-note-text"
                                                                              rows="1">{{ $w->note }}</textarea></td>
                                            <td>
                                                <form method="POST" action="{{ route('crypto.wallet.delete') }}"
                                                      class="form-inline form-with-confirm">
                                                    @csrf
                                                    <input type="hidden" name="wallet_id"
                                                           value="{{ $w->wallet_id }}">
                                                    <button type="submit" class="btn btn-sm btn-link text-danger">
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
                <div class="col-12 col-md-12 col-xxl-7 d-flex order-2 order-xxl-2">
                    <div class="card flex-fill">
                        <div class="card-header">
                            <h5 class="card-title mb-0">{{ __('Exchanges') }}</h5>
                        </div>
                        <div class="card-body d-flex">
                            <div class="col-lg-12 table-responsive">
                                <table class="table table-sm small">
                                    <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>{{ __('Sender') }}</th>
                                        <th>{{ __('Sent') }}</th>
                                        <th>{{ __('Received') }}</th>
                                        <th>{{ __('Receiver') }}</th>
                                        <th>{{ __('Fee') }}</th>
                                        <th>{{ __('Comment') }}</th>
                                        <th>{{ __('P') }}</th>
                                        <th>{{ __('P') }} %</th>
                                        <th>{{ __('Date') }}</th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($exchanges as $e)
                                        <tr class="exchange-{{ $e->exchange_id }}">
                                            <td><small class="text-muted"><a
                                                        href="{{ route('crypto.exchange.show', ['exchange_id' => $e->exchange_id]) }}">{{ substr($e->exchange_id, 0, 8) }}</a></small>
                                            </td>
                                            <td>{{ $e->senderWallet->name }}</td>
                                            <td>{{ \Brick\Math\BigDecimal::of($e->sender_amount)->toScale(4, \Brick\Math\RoundingMode::DOWN)->stripTrailingZeros() }}
                                                <a
                                                    href="{{ route('crypto.observer.show', ['observer_id' => $e->senderObserver->observer_id]) }}">{{ $e->senderCrypto->symbol }}</a>
                                            </td>
                                            <td>{{ \Brick\Math\BigDecimal::of($e->receiver_amount)->toScale(4, \Brick\Math\RoundingMode::DOWN)->stripTrailingZeros() }}
                                                <a
                                                    href="{{ route('crypto.observer.show', ['observer_id' => $e->receiverObserver->observer_id]) }}">{{ $e->receiverCrypto->symbol }}</a>
                                            </td>
                                            <td>{{ $e->receiverWallet->name }}</td>
                                            <td class="small">@isset($e->commission)
                                                    {{ \App\Domain\Convertor\Str::TrimZeroes(App\Domain\Exchange\Commission::getCommissionCryptoAmount($e)) }} {{ $e->senderCrypto->symbol }}
                                                    {{ App\Domain\Exchange\Commission::getCommissionUsdAmount($e) }}$
                                                @endisset</td>
                                            <td><textarea class="form-control" readonly>{{ $e->note }}</textarea></td>
                                            <td class="exchange-live-profit"></td>
                                            <td class="exchange-live-percent"></td>
                                            <td><small class="text-muted">{{ $e->created_at }}</small></td>
                                            <td>
                                                <form method="POST" action="{{ route('crypto.exchange.delete') }}"
                                                      class="form-inline form-with-confirm">
                                                    @csrf
                                                    <input type="hidden" name="exchange_id"
                                                           value="{{ $e->exchange_id }}">
                                                    <button type="submit" class="btn btn-sm btn-link text-danger">
                                                        <span data-feather="trash"></span></button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                                {{ $exchanges->links() }}
                            </div>
                        </div>
                    </div>
                </div>
                @if(in_array($observer->crypto->cmc_id, $watchOnlyAllowedCMCIds))
                    <div class="col-12 col-md-12 col-xxl-7 d-flex order-1 order-xxl-3">
                        <div class="card flex-fill">
                            <div class="card-header">
                                <div class="card-actions float-end">
                                    <span data-feather="plus" data-bs-toggle="modal"
                                          data-bs-target="#addWatchOnlyWalletBackdrop" class="font-weight-bold add-wo-wallet"
                                          style="cursor:pointer;"></span>
                                </div>
                                <h5 class="card-title mb-0">Watch-only {{ __('Wallets') }}</h5>
                            </div>
                            <div class="card-body d-flex">
                                <div class="col-lg-12 table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                        <tr>
                                            <th>Address</th>
                                            <th>{{ __('Name') }}</th>
                                            <th>{{ __('Balance') }}</th>
                                            <th>{{ __('USD Amount') }}</th>
                                            <th>{{ __('Note') }}</th>
                                            <th></th>
                                            <th></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($observer->watchOnlyWallets as $w)
                                            <tr class="wallet-{{ $w->wallet_id }}">
                                                <td><small class="text-muted">
                                                        {{ $w->address }}
                                                    </small>
                                                </td>
                                                <td>{{ $w->name }}</td>
                                                <td class="wallet-total-balance">0</td>
                                                <td class="wallet-total-usd-balance">0</td>
                                                <td class="wallet-note"><textarea data-wallet-id="{{ $w->wallet_id }}"
                                                                                  autocomplete="off"
                                                                                  class="form-control form-control-sm wallet-note-text"
                                                                                  rows="1">{{ $w->note }}</textarea>
                                                </td>
                                                <td>
                                                    <button
                                                        class="btn btn-sm btn-link text-primary text-decoration-none airdrop-btn"
                                                        data-wallet-id="{{ $w->wallet_id }}">A
                                                    </button>
                                                    <button
                                                        class="btn btn-sm btn-link text-primary text-decoration-none debank-btn"
                                                        data-wallet-id="{{ $w->wallet_id }}">D
                                                    </button>
                                                </td>
                                                <td>
                                                    <form method="POST" action="{{ route('crypto.wallet.delete') }}"
                                                          class="form-inline form-with-confirm">
                                                        @csrf
                                                        <input type="hidden" name="wallet_id"
                                                               value="{{ $w->wallet_id }}">
                                                        <button type="submit" class="btn btn-sm btn-link text-danger">
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
                @endif

                <div class="col-12 col-md-6 col-xxl-3 d-flex order-3 order-xxl-4">
                    <div class="card flex-fill w-100">
                        <div class="card-header">

                            <h5 class="card-title mb-0">{{ __('Notifiers') }}</h5>
                        </div>
                        <div class="card-body px-4">
                            @if(!auth()->user()->jabber)
                                <div class="alert alert-warning">
                                    <div class="alert-message">
                                        {{ __('You will not receive notifications without Jabber. Go to') }} <a
                                            class="alert-link"
                                            href="{{ route('settings.index') }}">{{ __('Settings') }}</a>.
                                    </div>
                                </div>
                            @endif

                            <form action="{{ route('crypto.notification.create') }}" method="POST" class="row g-3">
                                @csrf
                                <input type="hidden" name="observer_id" value="{{ $observer->observer_id }}">
                                <div class="col-md-12">
                                    <label class="form-label">{{ __('Change from start of the day') }}</label>
                                    <div class="input-group">
                                        <input type="number" min="-99" max="100" step="1" name="trigger_percent"
                                               class="form-control" placeholder="-99 -> 100">
                                        <span class="input-group-text" id="basic-addon1">%</span>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" checked="" id="muteEmptySwitch"
                                               name="mute_empty">
                                        <label class="form-check-label"
                                               for="muteEmptySwitch">{{ __('Mute when zero balance') }}</label>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="isGlobalSwitch"
                                               name="is_global">
                                        <label class="form-check-label"
                                               for="isGlobalSwitch">{{ __('Create for all assets') }}</label>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Add') }}
                                    </button>
                                </div>
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                        <tr>
                                            <th></th>
                                            <th>%</th>
                                            <th>{{ __('Last notify') }}</th>
                                            <th></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($observer->notifications as $n)
                                            <tr>
                                                <td>@if($n->mute_empty)<span class="text-muted"
                                                                             data-feather="volume-x"></span> @endif</td>
                                                <td @if($n->trigger_percent > 0)class="text-success"
                                                    @else class="text-danger" @endif>@if($n->trigger_percent > 0)
                                                        +@endif{{ $n->trigger_percent }}%
                                                </td>
                                                <td>@if($n->last_notified_at){{ $n->last_notified_at }} @else <span
                                                        class="text-muted">{{ __('Never') }}</span> @endif</td>
                                                <td>
                                                    <form method="POST"
                                                          action="{{ route('crypto.notification.delete') }}"
                                                          class="form-inline">
                                                        @csrf
                                                        <input type="hidden" name="notification_id"
                                                               value="{{ $n->notification_id }}">
                                                        <button type="submit" class="btn btn-sm btn-link text-danger">
                                                            <span data-feather="trash"></span></button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    @include('crypto.popups.airdrop')
    @include('crypto.popups.create_wallet')
    @if(in_array($observer->crypto->cmc_id, $watchOnlyAllowedCMCIds))
        @include('crypto.popups.create_watch_only_wallet')
    @endif
@endsection

@push('scripts')
    <script>
        var balanceChart
        $(() => {
            $('.add-wallet').click(function () {
                $('#addWalletsForm .wallet-name:not(:first),#addWalletsForm .wallet-balance:not(:first),#addWalletsForm .wallet-note:not(:first)').remove()
                $('#addWalletsForm input[type!="hidden"], #addWalletsForm textarea').val('')
            })

            $('.add-wo-wallet').click(function () {
                $('#addWatchOnlyWalletsForm .wallet-only-name:not(:first),#addWatchOnlyWalletsForm .wallet-only-address:not(:first),#addWatchOnlyWalletsForm .wallet-only-note:not(:first)').remove()
                $('#addWatchOnlyWalletsForm input[type!="hidden"], #addWatchOnlyWalletsForm textarea').val('')
            })
            $('#showUpdateRateFormBtn').click(function () {
                $('#updateRateForm').show();
                $('#rateBlock').hide();
            });
            $('#chartDateSelector').change(function () {
                fetchBalanceChart($(this).val(), $('#chartWalletTypeSelector').val())
                fetchStats($(this).val())
            });

            $('#chartWalletTypeSelector').change(function () {
                fetchBalanceChart($('#chartDateSelector').val(), $(this).val())
            });

            $('.form-with-confirm').submit(function () {
                return confirm("{{ __("Do you really want to submit the form?") }}");
            })
            var ctx = document.getElementById("chart-rate").getContext("2d");
            @if(auth()->user()->color_scheme == 'light')
            var gradient = ctx.createLinearGradient(0, 0, 0, 225);
            gradient.addColorStop(0, "rgba(215, 227, 244, 1)");
            gradient.addColorStop(1, "rgba(215, 227, 244, 0)");
            @else
            var gradient = ctx.createLinearGradient(0, 0, 0, 225);
            gradient.addColorStop(0, "rgba(51, 66, 84, 1)");
            gradient.addColorStop(1, "rgba(0, 0, 0, 0)");
            @endif

            balanceChart = new Chart(document.getElementById("chart-rate"), {
                type: "line",
                data: {
                    labels: [],
                    datasets: [{
                        label: "USD/{{ $observer->crypto->symbol }}",
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

            $('.wallet-note-text').change(function () {
                updateWallet($(this).attr('data-wallet-id'), $(this).val(), function (resp) {
                    if (resp && resp.data && resp.data.wallet_id) {
                        $(`textarea[data-wallet-id=${resp.data.wallet_id}]`).addClass('is-valid')
                    } else {
                        $(`textarea[data-wallet-id=${resp.data.wallet_id}]`).addClass('is-invalid')
                    }
                })
            })
            refreshData()
            setInterval(() => {
                refreshData()
            }, 10000)

            function refreshData() {
                fetchStats($('#chartDateSelector').val())
                fetchProfits(refreshProfits)
                fetchBalanceChart($('#chartDateSelector').val(), $('#chartWalletTypeSelector').val())
                dataRefreshed()
            }

            $('#currentTime').click(function () {
                refreshData();
            })

            $('.debank-btn').click(function () {
                fetch(`{{ route('api.wallets.debank') }}?api_token={{ auth()->user()->api_token }}&wallet_id=${$(this).attr('data-wallet-id')}`, {
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                }).then(resp => resp.json()).then(resp => {
                    $(this).text(resp.balance);
                })
            });

            $('.airdrop-btn').click(function () {
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

            function refreshProfits(profits) {
                Object.keys(profits.exchanges).forEach(k => {
                    $(`.exchange-${k} > .exchange-live-profit`).text(`${profits.exchanges[k].amount}`)
                    $(`.exchange-${k} > .exchange-live-percent`).text(`${profits.exchanges[k].percent}%`)
                    if (profits.exchanges[k].is_profit == true) {
                        $(`.exchange-${k} > .exchange-live-profit,.exchange-${k} > .exchange-live-percent`).removeClass('text-danger').addClass('text-success');
                    } else {
                        $(`.exchange-${k} > .exchange-live-profit,.exchange-${k} > .exchange-live-percent`).removeClass('text-success').addClass('text-danger');
                    }
                })
            }

            function fetchProfits(callback) {
                fetch('{{ route('api.observer.exchange.profits') }}?api_token={{ auth()->user()->api_token }}&observer_id={{ $observer->observer_id }}', {
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                }).then(resp => resp.json()).then(resp => {
                    callback(resp)
                })
            }

            function fetchBalanceChart(period = "24h", wallet_type = "nwo") {
                fetch(`{{ route('api.charts.observer.rate') }}?api_token={{ auth()->user()->api_token }}&observer_id={{ $observer->observer_id }}&period=${period}&wallet_type=${wallet_type}`, {
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                }).then(resp => resp.json()).then(resp => {
                    BalanceChart(Object.keys(resp.rates), Object.values(resp.rates))
                })
            }

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

            function BalanceChart(labels, data) {
                balanceChart.data.labels = labels
                balanceChart.data.datasets[0].data = data
                balanceChart.update()
            }


            function fetchStats(period) {
                fetch(`{{ route('api.observer.stats') }}?api_token={{ auth()->user()->api_token }}&observer_id={{ $observer->observer_id }}&period=${period}`, {
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                }).then(resp => resp.json()).then(resp => {
                    refreshObserver(resp)
                })
            }

            function refreshObserver(stats) {
                if (stats.change) {
                    $('.rate-change').text(`${stats.change.percent}%`)
                    $('.total-balance-change').text(`${stats.change.usd} USD`)
                    $('.wo-total-balance-change').text(`${stats.change.wo_usd} USD`)
                    $('.period').text($('#chartDateSelector option:selected').text())
                    if (stats.change.positive === true) {
                        $('.rate-change,.total-balance-change').addClass('text-success').removeClass('text-danger')
                    } else {
                        $('.rate-change,.total-balance-change').addClass('text-danger').removeClass('text-success')
                    }
                }
                $('.rate-usd').text(stats.rate)
                $('.total-balance').text(parseFloat(parseFloat(stats.total.crypto).toFixed(4)))
                $('.wo-total-balance').text((stats.total.wo_crypto ?? '0') == '0' ? '' : `+${parseFloat(parseFloat(stats.total.wo_crypto).toFixed(4))}`)
                $('.total-usd-balance').text(stats.total.usd)
                $('.wo-total-usd-balance').text(stats.total.wo_usd)
                Object.keys(stats.wallets).forEach(k => {
                    $(`.wallet-${k} > .wallet-total-balance`).text(parseFloat(parseFloat(stats.wallets[k].crypto).toFixed(4)))
                    $(`.wallet-${k} > .wallet-total-usd-balance`).text(stats.wallets[k].usd)
                    $(`.wallet-${k} .wallet-note-text`).text(stats.wallets[k].note)
                })
                Object.keys(stats.wo_wallets).forEach(k => {
                    $(`.wallet-${k} > .wallet-total-balance`).text(parseFloat(parseFloat(stats.wo_wallets[k].crypto).toFixed(4)))
                    $(`.wallet-${k} > .wallet-total-usd-balance`).text(stats.wo_wallets[k].usd)
                    $(`.wallet-${k} .wallet-note-text`).text(stats.wo_wallets[k].note)
                })
            }
        })

    </script>
@endpush

