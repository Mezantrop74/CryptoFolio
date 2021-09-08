@section('title'){{ __('User') }} {{ $user->login }} @endsection
@extends('layouts.app')
@section('content')
    <main class="content">
        <div class="container-fluid p-1">
            <h1 class="h3 mb-3">{{ __('Users') }}</h1>

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
                        <h5 class="card-title mb-0">{{ __('User') }} {{ substr($user->user_id, 0, 8) }}</h5>
                    </div>
                    <div class="card-body d-flex">
                        <div class="col-lg-12">
                            <form action="{{ route('admin.users.update')  }}" method="POST" class="row g-3">
                                @csrf
                                <input type="hidden" name="user_id" value="{{ $user->user_id }}">
                                <div class="col-lg-6">
                                    <label class="form-label">{{ __('Login') }}</label>
                                    <input type="text" required name="login" value="{{ $user->login }}"
                                           class="form-control">
                                </div>
                                <div class="col-lg-6">
                                    <label class="form-label">{{ __('Jabber') }}</label>
                                    <input type="email" name="jabber" value="{{ $user->jabber }}"
                                           class="form-control">
                                </div>
                                <div class="col-lg-12">
                                    <label class="form-label">{{ __('Note') }}</label>
                                    <textarea type="text" name="note"
                                              class="form-control">{{ $user->note }}</textarea>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox"
                                               @if($user->is_active) checked
                                               @endif id="isActiveSwitch" name="is_active">
                                        <label class="form-check-label"
                                               for="isActiveSwitch">{{ __('Active') }}</label>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                                    <button type="button" onclick="$('#newPasswordForm').submit()"
                                            class="btn btn-outline-secondary float-end">{{ __('New password') }}</button>
                                </div>
                            </form>
                            <form action="{{ route('admin.users.update.password') }}" method="POST" id="newPasswordForm">
                                @csrf
                                <input type="hidden" name="user_id" value="{{ $user->user_id }}">
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
