@extends('layouts.app')
@section('title'){{ __('Reports') }} @endsection
@section('content')
    <main class="content">
        <div class="container-fluid p-0">
            <div class="row mb-2 mb-xl-3">
                <div class="col-auto d-none d-sm-block">
                    <h1 class="h3 mb-3"><strong>{{ __('Reports') }}</strong>
                    </h1>
                </div>
            </div>
            <div class="row">
                <div class="col-xl-3 col-xxl-3">
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
                            <h5 class="card-title mb-0">{{ __('Export') }}</h5>
                        </div>
                        <div class="card-body py-3">
                            <form class="row g-3" action="{{ route('reports.create') }}"
                                  method="POST">
                                @csrf
                                <div class="col-lg-12">
                                    <label class="form-label">{{ __('Select Assets') }}</label>
                                    <select class="crypto-select form-control w-100" name="observers[]"
                                            multiple="multiple">
                                        @foreach($observers as $o)
                                            <option
                                                value="{{ $o->observer_id }}">{{ $o->crypto->name }}
                                                ({{ $o->crypto->symbol }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-12">
                                    <label class="form-label">{{ __('Export type')  }}</label>
                                    <select class="form-select" name="export_type">
                                        <option value="exchange">{{ __('Exchanges') }}</option>
                                    </select>
                                </div>
                                <div class="col-lg-12">
                                    <label class="form-label">{{ __('Export format')  }}</label>
                                    <select class="form-select" name="export_format">
                                        <option value="csv">CSV</option>
                                        <option value="xlsx">XLSX</option>
                                        <option value="pdf">PDF</option>
                                    </select>
                                </div>
                                <div class="col-lg-6">
                                    <label class="form-label">{{ __('From Date') }}</label>
                                    <div class="input-group date" data-provide="datepicker"
                                         data-date-format="dd-mm-yyyy">
                                        <input type="text" class="form-control" name="date[from]" readonly
                                               autocomplete="off">
                                        <div class="input-group-addon d-flex">
                                            <span class="input-group-text"><span data-feather="clock"></span></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <label class="form-label">{{ __('To Date') }}</label>
                                    <div class="input-group date" data-provide="datepicker"
                                         data-date-format="dd-mm-yyyy">
                                        <input type="text" class="form-control" name="date[to]" readonly
                                               autocomplete="off">
                                        <div class="input-group-addon d-flex">
                                            <span class="input-group-text"><span data-feather="clock"></span></span>
                                        </div>
                                    </div>
                                </div>


                                <div class="col-lg-12">
                                    <button type="submit" class="btn btn-primary mt-2">{{ __('Export') }}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
@section('styles')
    <link rel="stylesheet"
          href="{{ asset('static/css/datepicker/bootstrap-datepicker.min.css') }}">
    @if(auth()->user()->color_scheme == 'light')
        <link rel="stylesheet" href="{{ asset('static/css/select2/select2.css') }}"/>
        <link rel="stylesheet" href="{{ asset('static/css/select2/select2.bootstrap.css') }}"/>
    @else
        <link rel="stylesheet" href="{{ asset('static/css/select2/dark.select2.css') }}"/>
        <link rel="stylesheet" href="{{ asset('static/css/select2/dark.select2.bootstrap.css') }}"/>
    @endif
@endsection
@push('scripts')
    <script src="{{ asset('static/js/datepicker/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('static/js/select2.min.js') }}"></script>
    <script>
        $(() => {
            $('.crypto-select').select2({
                theme: "bootstrap-5",
            });

            // $('.datepicker').datepicker();
        })
    </script>
@endpush
