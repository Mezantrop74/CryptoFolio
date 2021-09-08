<div class="modal fade" id="addWatchOnlyWalletBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
     aria-labelledby="addWatchOnlyWalletBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addWatchOnlyWalletBackdropLabel">{{ __('Add Watch-only wallets') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-secondary">
                    <div class="alert-message">
                        {{ __('Balance auto refresh possible only in: ') }} Bitcoin, Bitcoin Cash, Litecoin, Dogecoin Dash, Ethereum, Ethereum Classic.
                    </div>
                </div>
                <form action="{{ route('crypto.wallet.create.watch.only') }}" method="POST" class="row g-3"
                      id="addWatchOnlyWalletsForm">
                    @csrf
                    <input type="hidden" name="observer_id" value="{{ $observer->observer_id ?? '' }}">
                    <div class="col-md-4 wallet-only-name">
                        <label class="form-label">{{ __('Name') }}</label>
                        <input type="text" name="name[]" class="form-control" autocomplete="off">
                    </div>
                    <div class="col-md-8 wallet-only-address">
                        <label class="form-label">{{ __('Wallet Address') }}</label>
                        <input type="text" name="address[]" class="form-control" autocomplete="off">
                    </div>
                    <div class="col-md-12 wallet-only-note">
                            <textarea placeholder="{{ __('Note (optional)') }}" class="form-control" rows="1"
                                      name="note[]" autocomplete="off"></textarea>
                    </div>

                    <div class="col-md-6">
                        <button type="button" id="appendWatchOnlyWalletBtn" class="btn btn-outline-secondary">
                            {{ __('More') }}
                        </button>
                    </div>
                    <div class="col-md-6">
                        <button type="submit" class="btn btn-primary" style="float:right;">
                            {{ __('Create') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@push('scripts')
    <script>
        $(() => {
            $('#appendWatchOnlyWalletBtn').click(function () {
                $('.wallet-only-name').first().clone().find('input').val('').end().insertAfter('.wallet-only-note:last')
                $('.wallet-only-address').first().clone().find('input').val('').end().insertAfter('.wallet-only-name:last')
                $('.wallet-only-note').first().clone().find('textarea').val('').end().insertAfter('.wallet-only-address:last')
            });
        })
    </script>
@endpush
