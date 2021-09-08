@extends('layouts.app')
@section('title'){{ __('News feed') }} @endsection
@section('content')
    <main class="content">
        <div class="container-fluid p-0">
            <div class="row">
                <div class="col-md-8 col-lg-8 col-xxl-8">
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
                </div>
            </div>
            <div class="row">
                @if(count($posts) > 0)
                    <div class="col-md-8 col-lg-8 col-xxl-8">
                        <div class="w-100">
                            <div class="row mb-2">
                                @foreach($posts as $p)
                                    <div class="col-md-4">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="g-0">
                                                    <strong
                                                        class="d-inline-block text-primary">
                                                        <a href="{{ url("https://t.me/s/{$p->source->link}") }}"
                                                           target="_blank">
                                                            {{ $p->source->name }}
                                                        </a>
                                                    </strong>
                                                    <div
                                                        class="mb-2 text-muted">{{ $p->posted_at->diffForHumans() }}</div>
                                                    <p class="card-text mb-auto">{{ $p->content }}</p>
                                                    <a href="{{ url("https://t.me/s/{$p->source->link}/{$p->origin_post_id}") }}"
                                                       target="_blank">{{ __('Source') }}</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            {{ $posts->links() }}
                        </div>
                    </div>
                @endif
                <div class="col-md-12 col-lg-4 col-xxl-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <h5 class="card-title">{{ __('Data Sources') }}</h5>
                                </div>

                                <div class="col-auto">
                                    <div class="stat text-primary">
                                        <i class="align-middle" data-feather="eye"></i>
                                    </div>
                                </div>
                            </div>
                            <form action="{{ route('newsfeed.source.store') }}" method="POST" class="row g-3">
                                @csrf
                                <div class="col-sm-12 col-md-8 col-lg-8 col-xl-8">
                                    <div class="col-lg-12">
                                        <div class="input-group">
                                            <input type="text" required class="form-control"
                                                   placeholder="{{ __('channel_name') }}" name="link">
                                            <button type="submit"
                                                    class="btn btn-outline-secondary">{{ __('Add') }}</button>
                                        </div>
                                        <small>t.me/s/<span
                                                class="text-primary text-decoration-underline">{{ __('channel_name') }}</span></small>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="mt-2">
                                            <div class="col-lg-12">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox"
                                                           id="withNotifySwitch" name="with_notify">
                                                    <label class="form-check-label"
                                                           for="withNotifySwitch">{{ __('Forward to Jabber') }}</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            @if(count($subscriptions) > 0)
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                        <tr>
                                            <th>{{ __('Name') }}</th>
                                            <th>{{ __('Last post at') }}</th>
                                            <th></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($subscriptions as $s)
                                            <tr>
                                                <td>
                                                    <a href="{{ url("https://t.me/s/{$s->source->link}") }}"
                                                       target="_blank">{{ $s->source->name }}</a>
                                                </td>
                                                <td>{{ $s->source->last_post_at ? $s->source->last_post_at->diffForHumans() : __('Never') }}</td>
                                                <td>
                                                    <form method="POST"
                                                          action="{{ route('newsfeed.subscription.notifications.toggle') }}"
                                                          class="form-inline form-with-confirm">
                                                        @csrf
                                                        <input type="hidden" name="subscription_id"
                                                               value="{{ $s->subscription_id }}">
                                                        <button type="submit"
                                                                class="btn btn-sm btn-link @if(!$s->with_notify) text-muted @endif p-0">
                                                                <span @if($s->with_notify) data-feather="volume-2"
                                                                      @else data-feather="volume-x" @endif></span>
                                                        </button>
                                                    </form>
                                                    <form method="POST"
                                                          action="{{ route('newsfeed.subscription.delete') }}"
                                                          class="form-inline form-with-confirm">
                                                        @csrf
                                                        <input type="hidden" name="subscription_id"
                                                               value="{{ $s->subscription_id }}">
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
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection

@push('scripts')
    <script>

    </script>
@endpush
