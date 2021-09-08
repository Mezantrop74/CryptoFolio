@extends('layouts.app')
@section('title'){{ __('Global Settings') }} @endsection
@section('content')
    <main class="content">
        <div class="container-fluid p-0">
            <div class="row mb-2 mb-xl-3">
                <div class="col-auto d-none d-sm-block">
                    <h1 class="h3 mb-3"><strong>{{ __('Settings') }}</strong>
                    </h1>
                </div>
            </div>
            <div class="row">
                <div class="col-xl-5 col-xxl-5">
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

                    <div class="card flex-fill w-100">
                        <div class="card-header">
                            <h5 class="card-title mb-0">{{ __('API settings') }}</h5>
                        </div>
                        <div class="card-body py-3">
                            <form class="row g-3" action="{{ route('admin.settings.update.api') }}"
                                  method="POST">
                                @csrf
                                <div class="col-md-8">
                                    <label class="form-label">{{ __('CoinMarketCap API Token') }}</label>
                                    <input type="text" autocomplete="off"
                                           class="form-control" name="cmc_api_token"
                                           value="{{ $apiSettings['cmc_api_token'] }}"
                                           required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">{{ __('Monthly usage') }}</label>
                                    <input type="text" autocomplete="off"
                                           class="form-control" readonly id="cmcMonthlyUsage">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">{{ __('Cryptoapis API Token') }}</label>
                                    <input type="text" autocomplete="off"
                                           class="form-control" name="cryptoapis_api_token"
                                           value="{{ $apiSettings['cryptoapis_api_token'] }}"
                                           required>
                                </div>
                                <div class="col">
                                    <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-xl-5 col-xxl-5">
                    <div class="card flex-fill w-100">
                        <div class="card-header">
                            <h5 class="card-title mb-0">{{ __('Backup Settings') }}</h5>
                        </div>
                        <div class="card-body py-3">
                            <form class="row g-3" action="{{ route('admin.backup.import') }}"
                                  method="POST" enctype="multipart/form-data">
                                @csrf
                                <div>
                                    <input type="file" name="import_file" required>
                                    <button type="submit" class="btn btn-primary">{{ __('Import') }}</button>
                                    <button type="button" id="exportBtn" class="btn btn-outline-primary"><span
                                            data-feather="download"></span></button>
                                </div>
                            </form>
                            <form action="{{ route('admin.backup.export') }}" id="exportForm"
                                  method="POST">
                                @csrf
                            </form>
                            <hr>
                            <form class="row g-3" action="{{ route('admin.backup.export') }}"
                                  method="POST">
                                @csrf
                                <div class="col">
                                    <label>{{ __('Last rate refresh') }}</label>
                                    <div class="input-group mt-2">
                                        <input type="text" readonly class="form-control"
                                               value="{{ isset($lastRate->created_at) ? $lastRate->created_at->diffForHumans() : __('Never') }}">
                                    </div>
                                </div>
                                <div class="col">
                                    <label>&nbsp;</label>
                                    <div class="col-sm-12 mt-2">
                                        <button type="button" id="rateBtn"
                                                class="btn btn-primary">{{ __('Refresh') }}</button>
                                    </div>
                                </div>
                            </form>
                            <form action="{{ route('admin.rates.fill') }}" method="POST" id="rateForm">
                                @csrf
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
@push('scripts')
    <script>
        function refreshApiUsage(callback) {
            fetch('{{ route('api.settings.api-usage') }}?api_token={{ auth()->user()->api_token }}', {
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
            }).then(resp => resp.json()).then(resp => {
                callback(resp)
            })
        }

        function refreshApiUsageCallback(resp) {
            $('#cmcMonthlyUsage').val(resp.usage);
        }

        $(() => {
            refreshData()
            setInterval(() => {
                refreshData()
            }, 10000)

            $('#exportBtn').click(function () {
                $('#exportForm').submit();
            });

            $('#rateBtn').click(function () {
                $('#rateForm').submit();
            });

            $('#currentTime').click(function () {
                refreshData();
            })

            function refreshData() {
                refreshApiUsage(refreshApiUsageCallback)
                dataRefreshed()
            }
        })
    </script>
@endpush
