@extends('layouts.app')
@section('title'){{ __('Your Exchanges') }} @endsection
@section('content')
    <main class="content">
        <div class="container-fluid p-0">
            <div class="row mb-2 mb-xl-3">
                <div class="col-auto d-none d-sm-block">
                    <h1 class="h3 mb-3">{{ __('Crypto Exchange') }}
                    </h1>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12 col-xl-10 col-xxl-6">
                    @if(session('success'))
                        <div class="alert alert-primary alert-dismissible" role="alert">
                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            <div class="alert-message">
                                {{ session('success') }}
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

                    <div class="card flex-fill w-100">
                        <div class="card-header">
                            <h5 class="card-title mb-0">{{ __('New Exchange') }}</h5>
                        </div>
                        <div class="card-body py-3">
                            <form class="row g-2 needs-validation" action="{{ route('crypto.exchange.create') }}"
                                  method="POST" novalidate>
                                @csrf
                                <div class="col-md-12">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox"
                                               id="marketTradeSwitch" autocomplete="off">
                                        <label class="form-check-label"
                                               for="marketTradeSwitch">{{ __('Market trade') }}</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="sender_crypto_id" class="form-label">{{ __('Sender') }}</label>
                                    <select class="form-control" autocomplete="off" id="sender_crypto_id"
                                            name="sender[crypto_id]" required>
                                        <option value="">{{ __('Select sender crypto') }}</option>
                                        @foreach($observers as $obs)
                                            <option value="{{ $obs->observer_id }}">
                                                {{ $obs->crypto->name }} ({{ $obs->crypto->symbol }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">&nbsp;</label>
                                    <input type="text" class="form-control" readonly id="senderRate" autocomplete="off">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">&nbsp;</label>
                                    <input type="text" class="form-control" readonly id="receiverRate"
                                           autocomplete="off">
                                </div>
                                <div class="col-md-4">
                                    <label for="receiver_crypto_id" class="form-label">{{ __('Receiver') }}</label>
                                    <select class="form-control" autocomplete="off" id="receiver_crypto_id"
                                            name="receiver[crypto_id]" required>
                                        <option value="">{{ __('Select receiver crypto') }}</option>
                                        @foreach($observers as $obs)
                                            <option value="{{ $obs->observer_id }}">
                                                {{ $obs->crypto->name }} ({{ $obs->crypto->symbol }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <select class="form-control" autocomplete="off" id="sender_wallets"
                                            name="sender[wallet_id]" required>
                                        <option value="">{{ __('Select sender wallet') }}</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <select class="form-control" autocomplete="off" id="receiver_wallets"
                                            name="receiver[wallet_id]" required>
                                        <option value="">{{ __('Select receiver wallet') }}</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <input type="number" autocomplete="off" min="0" step="0.00000000000001"
                                           placeholder="{{ __('Sender amount') }}"
                                           class="form-control" name="sender[amount]" id="sender_amount" required>
                                    <div class="invalid-feedback" id="sender_amount_error">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <input type="number" autocomplete="off" min="0" step="0.00000000000001"
                                           placeholder="{{ __('Receiver amount') }}"
                                           class="form-control" name="receiver[amount]" id="receiver_amount" required>
                                </div>
                                <div class="col-md-3">
                                    <input type="number" autocomplete="off" min="0" step="0.00000000000001"
                                           placeholder="{{ __('With fee') }}"
                                           class="form-control" id="receiver_amount_with_commission" readonly>
                                </div>
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <input type="number" autocomplete="off" step="0.00000001"
                                               placeholder="{{ __('Fee (optional)') }}"
                                               class="form-control" name="commission" id="commission_amount">
                                        <span class="input-group-text" id="basic-addon1">%</span>
                                    </div>
                                    <div class="invalid-feedback" id="commission_amount_error">
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <textarea autocomplete="off" rows="1" class="form-control"
                                              placeholder="{{ __('Note (optional)') }}" name="note"></textarea>
                                </div>
                                <div class="col">
                                    <button type="submit" class="btn btn-primary">{{ __('Exchange') }}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-md-12 col-xxl-9 d-flex order-1 order-xxl-1">
                    <div class="card flex-fill">
                        <div class="card-header">
                            <h5 class="card-title mb-0">{{ __('Exchanges') }}</h5>
                        </div>
                        <div class="card-body d-flex">
                            <div class="col-lg-12 table-responsive">
                                <table class="table">
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
                                            <td>{{ \Brick\Math\BigDecimal::of($e->sender_amount)->toScale(4, \Brick\Math\RoundingMode::DOWN)->stripTrailingZeros() }} <a
                                                    href="{{ route('crypto.observer.show', ['observer_id' => $e->senderObserver->observer_id]) }}">{{ $e->senderCrypto->symbol }}</a>
                                            </td>
                                            <td>{{ \Brick\Math\BigDecimal::of($e->receiver_amount)->toScale(4, \Brick\Math\RoundingMode::DOWN)->stripTrailingZeros() }} <a
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
            </div>
        </div>
    </main>
@endsection
@push('scripts')
    <style>
        .form-control.is-valid:focus, .was-validated :valid.form-control {
            border: 1px solid #ced4da;
            background-image: inherit !important;
            box-shadow: inherit !important;
        }
    </style>
    <script>
        $(() => {
            (function () {
                'use strict'

                // Fetch all the forms we want to apply custom Bootstrap validation styles to
                var forms = document.querySelectorAll('.needs-validation')

                // Loop over them and prevent submission
                Array.prototype.slice.call(forms)
                    .forEach(function (form) {
                        form.addEventListener('submit', function (event) {
                            if (!form.checkValidity()) {
                                event.preventDefault()
                                event.stopPropagation()
                            }

                            form.classList.add('was-validated')
                        }, false)
                    })
            })()

            $('#marketTradeSwitch').click(function () {
                if ($(this).is(':checked')) {
                    $('#receiver_amount').attr('readonly', 'readonly')
                } else {
                    $('#receiver_amount').removeAttr('readonly')
                }
            })

            $('.form-with-confirm').submit(function () {
                return confirm("{{ __("Do you really want to submit the form?") }}");
            })

            refreshData()
            setInterval(() => {
                refreshData()
            }, 10000)

            $('#currentTime').click(function () {
                refreshData();
            })

            function refreshData() {
                fetchProfits(refreshProfits)
                dataRefreshed()
            }

            $('#sender_crypto_id').change(function () {
                fetchStats($(this).val(), (resp) => {
                    $('#sender_wallets').empty()
                    $('#sender_amount').val('')
                    $('#commission_amount').val('')
                    $('#senderRate').val(resp.rate)
                    if (Object.keys(resp.wallets).length == 1) {
                        //Object.keys(resp.wallets)[0]
                        let walletId = Object.keys(resp.wallets)[0]
                        $('#sender_wallets').append($('<option>').val(walletId).text(`${resp.wallets[walletId].name} (${resp.wallets[walletId].crypto} ${resp.currency})`))
                    } else {
                        $('#sender_wallets').append($('<option>').val('').text(`${Object.keys(resp.wallets).length} wallets`))
                        Object.keys(resp.wallets).forEach((walletId) => {
                            $('#sender_wallets').append($('<option>').val(walletId).text(`${resp.wallets[walletId].name} (${resp.wallets[walletId].crypto} ${resp.currency})`))
                        })
                    }

                })
            })

            $('#receiver_crypto_id').change(function () {
                fetchStats($(this).val(), (resp) => {
                    $('#receiverRate').val(resp.rate)
                    $('#receiver_wallets').empty()
                    $('#receiver_amount').val('')
                    if (Object.keys(resp.wallets).length == 1) {
                        let walletId = Object.keys(resp.wallets)[0];
                        $('#receiver_wallets').append($('<option>').val(walletId).text(`${resp.wallets[walletId].name} (${resp.wallets[walletId].crypto} ${resp.currency})`))
                    } else {
                        $('#receiver_wallets').append($('<option>').val('').text(`${Object.keys(resp.wallets).length} wallets`))
                        Object.keys(resp.wallets).forEach((walletId) => {
                            $('#receiver_wallets').append($('<option>').val(walletId).text(`${resp.wallets[walletId].name} (${resp.wallets[walletId].crypto} ${resp.currency})`))
                        })
                    }


                })
            })

            $('#sender_amount').keyup(function () {
                if (!$(this).val()) {
                    $(this)[0].setCustomValidity('');
                }
                CalculateExchange($('#sender_wallets').val(), $(this).val(), $('#commission_amount').val(), $('#receiver_crypto_id').val(), $('#receiver_amount').val(), amountValidationCallback)
            })

            $('#sender_wallets').change(function () {
                if (!$('#sender_amount').val()) {
                    $('#sender_amount')[0].setCustomValidity('');
                }
                CalculateExchange($('#sender_wallets').val(), $('#sender_amount').val(), $('#commission_amount').val(), $('#receiver_crypto_id').val(), $('#receiver_amount').val(), amountValidationCallback)
            })
            $('#commission_amount').keyup(function () {
                if (!$('#sender_amount').val()) {
                    $('#sender_amount')[0].setCustomValidity('');
                }
                CalculateExchange($('#sender_wallets').val(), $('#sender_amount').val(), $(this).val(), $('#receiver_crypto_id').val(), $('#receiver_amount').val(), amountValidationCallback)
            })

            $('#receiver_amount').keyup(function () {
                CalculateExchange($('#sender_wallets').val(), $('#sender_amount').val(), $('#commission_amount').val(), $('#receiver_crypto_id').val(), $('#receiver_amount').val(), amountValidationCallback)
            })
        })

        function CalculateExchange(wallet_id, amount, commission, receiverObserverId, receiverAmount, callback) {
            if (!$.isNumeric(amount)) {
                return
            }
            if (!$.isNumeric(commission)) {
                commission = '0'
            }

            if ($('#marketTradeSwitch').is(':checked')) {
                receiverAmount = ''
            }

            if ($.type(receiverObserverId) !== "string") {
                receiverObserverId = ''
            }
            fetch(`{{ route('api.exchanges.calculate') }}?api_token={{ auth()->user()->api_token }}&sender_wallet_id=${wallet_id}&sender_amount=${amount}&commission=${commission}&receiver_observer_id=${receiverObserverId}&receiver_amount=${receiverAmount}`, {
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
            }).then(resp => resp.json()).then(resp => {
                callback(resp)
            })
        }

        function fetchProfits(callback) {
            fetch('{{ route('api.exchanges.profits') }}?api_token={{ auth()->user()->api_token }}', {
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
            }).then(resp => resp.json()).then(resp => {
                callback(resp)
            })
        }

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

        function amountValidationCallback(resp) {
            if (Object.keys(resp.errors).length > 0) {
                $('#commission_amount, #sender_amount')[0].setCustomValidity(resp.errors[0])
                $('#commission_amount_error, #sender_amount_error').text(resp.errors[0])
            } else {
                $('#commission_amount, #sender_amount')[0].setCustomValidity('');
                $('#commission_amount_error, #sender_amount_error').text('')
            }

            if (resp.receiver_amount != null && resp.receiver_amount_with_commission != null) {
                if ($('#marketTradeSwitch').is(':checked')) {
                    $('#receiver_amount').val(resp.receiver_amount);
                }
                $('#receiver_amount_with_commission').val(resp.receiver_amount_with_commission);
            }
        }

        function fetchStats(observer_id, callback) {
            if (!observer_id) {
                return
            }
            fetch(`{{ route('api.observer.stats') }}?api_token={{ auth()->user()->api_token }}&observer_id=${observer_id}`, {
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
            }).then(resp => resp.json()).then(resp => {
                callback(resp)
            })
        }
    </script>
@endpush
