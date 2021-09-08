@section('title')Users @endsection
@extends('layouts.app')
@section('content')
    <main class="content">
        <div class="container-fluid p-1">
            <h1 class="h3 mb-3">{{ __('Users') }}</h1>
            <div class="col-12 col-md-12 col-xxl-7 d-flex order-1 order-xxl-1">
                <div class="card flex-fill">
                    <div class="card-header">
                        <div class="card-actions float-end">
                            <a href="{{ route('admin.users.create') }}">
                                <span data-feather="plus" class="font-weight-bold"
                                      style="cursor:pointer;"></span>
                            </a>

                        </div>
                        <h5 class="card-title mb-0">{{ __('Users list') }}</h5>
                    </div>
                    <div class="card-body d-flex">
                        <div class="col-lg-12 table-responsive">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>{{ __('Login') }}</th>
                                    <th>{{ __('Jabber') }}</th>
                                    <th>{{ __('Note') }}</th>
                                    <th>{{ __('Created at') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($users as $u)
                                    <tr>
                                        <td><small class="text-muted"><a
                                                    href="{{ route('admin.users.show', ['user_id' => $u->user_id]) }}">{{ substr($u->user_id, 0, 8) }}</a></small>
                                        </td>
                                        <td>{{ $u->login }} @if($u->is_admin) <span data-feather="zap"></span> @endif</td>
                                        <td>{{ $u->jabber ?? '' }}</td>
                                        <td><textarea autocomplete="off"
                                                      class="form-control form-control-sm" readonly
                                                      rows="1">{{ $u->note }}</textarea></td>
                                        <td>{{ $u->created_at }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
@push('scripts')
@endpush
