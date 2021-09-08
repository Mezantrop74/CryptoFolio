@extends('layouts.app')
@section('title'){{ sprintf(__('Exchange #%s'), substr($exchange->exchange_id, 0, 8)) }} @endsection
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
                            <h5 class="card-title mb-0">{{ __('Edit exchange') }}</h5>
                        </div>
                        <div class="card-body py-3">
                            <form class="row g-2 needs-validation" action="{{ route('crypto.exchange.update') }}"
                                  method="POST" novalidate>
                                <input type="hidden" name="exchange_id" value="{{ $exchange->exchange_id }}">
                                <input type="hidden" name="sender[crypto_id]"
                                       value="{{ $exchange->senderObserver->observer_id }}">
                                <input type="hidden" id="receiver_crypto_id" name="receiver[crypto_id]"
                                       value="{{ $exchange->receiverObserver->observer_id }}">
                                <input type="hidden" name="sender[wallet_id]" id="sender_wallet_id"
                                       value="{{ $exchange->senderWallet->wallet_id }}">
                                <input type="hidden" name="receiver[wallet_id]"
                                       value="{{ $exchange->receiverWallet->wallet_id }}">

                                @csrf
                                <div class="col-md-2">
                                    <label for="sender_crypto_id" class="form-label">{{ __('Sender') }}</label>
                                    <input type="text" name="" class="form-control"
                                           value="{{ $exchange->senderCrypto->name }}" readonly>

                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">&nbsp;</label>
                                    <input type="text" class="form-control" readonly id="senderRate" autocomplete="off"
                                           value="{{ App\Domain\Convertor\Str::Beautify($exchange->senderRate->rate) }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">&nbsp;</label>
                                    <input type="text" class="form-control" readonly id="receiverRate"
                                           value="{{ App\Domain\Convertor\Str::Beautify($exchange->receiverRate->rate) }}"
                                           autocomplete="off">
                                </div>
                                <div class="col-md-2">
                                    <label for="receiver_crypto_id" class="form-label">{{ __('Receiver') }}</label>
                                    <input type="text" name="receiver[crypto_id]" class="form-control"
                                           value="{{ $exchange->receiverCrypto->name }}" readonly>
                                </div>

                                <div class="col-md-6">
                                    <input type="text" class="form-control"
                                           value="{{ $exchange->senderWallet->name }} ({{ \App\Domain\Convertor\Str::Beautify($exchange->senderWallet->balance->getAmount()->toFloat()) }} {{ $exchange->senderCrypto->symbol }})"
                                           readonly>
                                </div>
                                <div class="col-md-6">
                                    <input type="text" class="form-control"
                                           value="{{ $exchange->receiverWallet->name }} ({{ \App\Domain\Convertor\Str::Beautify($exchange->receiverWallet->balance->getAmount()->toFloat()) }} {{ $exchange->receiverCrypto->symbol }})"
                                           readonly>
                                </div>

                                <div class="col-md-6">
                                    <input type="number" autocomplete="off" min="0" step="0.00000000000001"
                                           placeholder="{{ __('Sender amount') }}"
                                           class="form-control" name="sender[amount]" id="sender_amount"
                                           value="{{ App\Domain\Convertor\Str::trimZeroes($exchange->sender_amount) }}"
                                           required>
                                    <div class="invalid-feedback" id="sender_amount_error">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <input type="number" autocomplete="off" min="0" step="0.00000000000001"
                                           placeholder="{{ __('Receiver amount') }}"
                                           class="form-control" name="receiver[amount]" id="receiver_amount"
                                           value="{{ App\Domain\Convertor\Str::trimZeroes($exchange->receiver_amount) }}"
                                           required>
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
                                               class="form-control" name="commission" id="commission_amount"
                                               value="{{ App\Domain\Convertor\Str::trimZeroes($exchange->commission) }}">
                                        <span class="input-group-text" id="basic-addon1">%</span>
                                    </div>
                                    <div class="invalid-feedback" id="commission_amount_error">
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <textarea autocomplete="off" rows="1" class="form-control"
                                              placeholder="{{ __('Note (optional)') }}"
                                              name="note">{{ $exchange->note }}</textarea>
                                </div>
                                <div class="col">
                                    <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                                </div>
                            </form>
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


            $('#sender_amount').keyup(function () {
                if (!$(this).val()) {
                    $(this)[0].setCustomValidity('');
                }
                CalculateExchange($('#sender_wallet_id').val(), $(this).val(), $('#commission_amount').val(), $('#receiver_crypto_id').val(), $('#receiver_amount').val(), amountValidationCallback)
            })

            $('#commission_amount').keyup(function () {
                if (!$('#sender_amount').val()) {
                    $('#sender_amount')[0].setCustomValidity('');
                }
                CalculateExchange($('#sender_wallet_id').val(), $('#sender_amount').val(), $(this).val(), $('#receiver_crypto_id').val(), $('#receiver_amount').val(), amountValidationCallback)
            })

            $('#receiver_amount').keyup(function () {
                CalculateExchange($('#sender_wallet_id').val(), $('#sender_amount').val(), $('#commission_amount').val(), $('#receiver_crypto_id').val(), $('#receiver_amount').val(), amountValidationCallback)
            })

            $('#sender_amount').trigger('keyup')
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

        function amountValidationCallback(resp) {
            if (Object.keys(resp.errors).length > 0) {
                $('#commission_amount, #sender_amount')[0].setCustomValidity(resp.errors[0])
                $('#commission_amount_error, #sender_amount_error').text(resp.errors[0])
            } else {
                $('#commission_amount, #sender_amount')[0].setCustomValidity('');
                $('#commission_amount_error, #sender_amount_error').text('')
            }

            if (resp.receiver_amount != null && resp.receiver_amount_with_commission != null) {
                $('#receiver_amount_with_commission').val(resp.receiver_amount_with_commission);
            }
        }
    </script>
@endpush
