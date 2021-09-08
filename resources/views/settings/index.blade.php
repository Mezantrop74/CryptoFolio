@extends('layouts.app')
@section('title'){{ __('Your Profile') }} @endsection
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
                            <h5 class="card-title mb-0">{{ __('User settings') }}</h5>
                        </div>
                        <div class="card-body py-3">
                            <form class="row g-3" action="{{ route('settings.user.update') }}"
                                  method="POST">
                                @csrf
                                <div class="col-md-12">
                                    <label class="form-label">{{ __('Old password') }}</label>
                                    <input type="password" autocomplete="off"
                                           class="form-control" name="old_password"
                                           required>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">{{ __('New password') }}</label>
                                    <input type="password" autocomplete="off"
                                           class="form-control" name="new_password"
                                           required>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">{{ __('Confirm new password') }}</label>
                                    <input type="password" autocomplete="off"
                                           class="form-control" name="confirm_password"
                                           required>
                                </div>
                                <div class="col">
                                    <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-xxl-4">
                    <div class="card flex-fill w-100">
                        <div class="card-header">
                            <h5 class="card-title mb-0">{{ __('Notifications') }}</h5>
                        </div>
                        <div class="card-body py-3">
                            <div class="alert alert-secondary">
                                <div class="alert-message">
                                    {{ __('After saving add') }}
                                    <strong>
                                        {{ config('services.jabber.username') .'@'. config('services.jabber.host')  }}</strong>
                                    {{ __('to your contact list.') }}
                                </div>
                            </div>
                            <form class="row g-3" action="{{ route('jabber.update') }}"
                                  method="POST">
                                @csrf
                                <div class="col-md-12">
                                    <label class="form-label">{{ __('Your Jabber') }}</label>
                                    <input type="email" autocomplete="off"
                                           placeholder="{{ __('test@jabber.com') }}"
                                           class="form-control" name="jabber" value="{{ auth()->user()->jabber ?? '' }}">
                                </div>
                                <div class="col">
                                    <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-xxl-2">
                    <div class="card flex-fill w-100">
                        <div class="card-header">
                            <h5 class="card-title mb-0">{{ __('View settings') }}</h5>
                        </div>
                        <div class="card-body py-3">
                            <form class="row g-3" action="{{ route('settings.view.update') }}"
                                  method="POST">
                                @csrf
                                <div class="col-md-12">
                                    <label class="form-label">{{ __('Language') }}</label>
                                    <select autocomplete="off"
                                            class="form-control" name="lang"
                                            required>
                                        @foreach(config('app.locales') as $k => $locale)
                                            <option @if(App::getLocale() == $k)selected
                                                    @endif value="{{ $k }}">{{ $locale }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">{{ __('Color scheme') }}</label>
                                    <select autocomplete="off"
                                            class="form-control" name="color_scheme"
                                            required>
                                        <option @if(auth()->user()->color_scheme == 'light')selected
                                                @endif value="light">{{ __('Light') }}</option>
                                        <option @if(auth()->user()->color_scheme == 'dark')selected
                                                @endif value="dark">{{ __('Dark') }}</option>
                                    </select>
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
    <script>
        $(() => {
        })
    </script>
@endpush
