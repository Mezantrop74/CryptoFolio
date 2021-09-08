@section('title'){{ __('New user') }} @endsection
@extends('layouts.app')
@section('content')
    <main class="content">
        <div class="container-fluid p-1">
            <h1 class="h3 mb-3">{{ __('Create new user') }}</h1>

            @if(session('success'))
                <div class="col-12 col-md-12 col-xxl-5">
                    <div class="alert alert-primary alert-dismissible" role="alert">
                        <button type="button" class="btn-close" data-bs-dismiss="alert"
                                aria-label="Close"></button>
                        <div class="alert-message">
                            {{ session('success') }}
                        </div>
                    </div>
                </div>
            @endif

            @if ($errors->any())
                <div class="col-12 col-md-12 col-xxl-5">

                    <div class="alert alert-danger">
                        <div class="alert-message">
                            @foreach ($errors->all() as $error)
                                {{ $error }}<br>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
            <div class="col-12 col-md-12 col-xxl-5 d-flex order-1 order-xxl-1">
                <div class="card flex-fill">
                    <div class="card-header">
                        <h5 class="card-title mb-0">{{ __('User info') }}</h5>
                    </div>
                    <div class="card-body d-flex">
                        <div class="col-lg-12">
                            <form action="{{ route('admin.users.create')  }}" method="POST" class="row g-3">
                                @csrf
                                <div class="col-lg-6">
                                    <label class="form-label">{{ __('Login') }}</label>
                                    <input type="text" required name="login"
                                           class="form-control">
                                </div>
                                <div class="col-lg-6">
                                    <label class="form-label">{{ __('Password') }}</label>
                                    <input type="text" name="password"
                                           class="form-control" value="{{ Str::random(20) }}">
                                </div>
                                <div class="col-lg-6">
                                    <label class="form-label">{{ __('Jabber') }}</label>
                                    <input type="email" name="jabber"
                                           class="form-control">
                                </div>
                                <div class="col-lg-6">
                                    <label class="form-label">{{ __('Note') }}</label>
                                    <textarea type="text" name="note"
                                              class="form-control"></textarea>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox"
                                                id="isActiveSwitch" name="is_active" checked>
                                        <label class="form-check-label"
                                               for="isActiveSwitch">{{ __('Active') }}</label>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <button type="submit" class="btn btn-primary">{{ __('Add') }}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
