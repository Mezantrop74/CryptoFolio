<div class="modal fade" id="addWalletBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
     aria-labelledby="addWalletBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addWalletBackdropLabel">{{ __('Add wallets') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('crypto.wallet.create') }}" method="POST" class="row g-3"
                      id="addWalletsForm">
                    @csrf
                    <input type="hidden" name="observer_id" value="{{ $observer->observer_id ?? '' }}">
                    <div class="col-md-6 wallet-name">
                        <label class="form-label">{{ __('Name') }}</label>
                        <input type="text" name="name[]" class="form-control" autocomplete="off">
                    </div>
                    <div class="col-md-6 wallet-balance">
                        <label class="form-label">{{ __('Balance') }}</label>
                        <input type="number" min="0" step="0.00000000000001" name="balance[]" class="form-control"
                               placeholder="zero if empty" autocomplete="off">
                    </div>
                    <div class="col-md-12 wallet-note">
                            <textarea placeholder="{{ __('Note (optional)') }}" class="form-control" rows="1"
                                      name="note[]" autocomplete="off"></textarea>
                    </div>

                    <div class="col-md-6">
                        <button type="button" id="appendWalletBtn" class="btn btn-outline-secondary">
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
            $('#appendWalletBtn').click(function () {
                $('.wallet-name').first().clone().find('input').val('').end().insertAfter('.wallet-note:last')
                $('.wallet-balance').first().clone().find('input').val('').end().insertAfter('.wallet-name:last')
                $('.wallet-note').first().clone().find('textarea').val('').end().insertAfter('.wallet-balance:last')
            });
        })
    </script>
@endpush
