<form class="form-horizontal" action="{{ route('payment_method.update') }}" method="POST">
    @csrf
    <input type="hidden" name="payment_method" value="paytiko">
    <div class="form-group row">
        <input type="hidden" name="types[]" value="PAYTIKO_MERCHANT_SECRET_KEY">
        <div class="col-md-4">
            <label class="col-from-label" for="PAYTIKO_MERCHANT_SECRET_KEY">{{ translate('Paytiko merchant secret key') }}</label>
        </div>
        <div class="col-md-8">
            <input type="text" class="form-control" name="PAYTIKO_MERCHANT_SECRET_KEY" id="PAYTIKO_MERCHANT_SECRET_KEY"
                value="{{ env('PAYTIKO_MERCHANT_SECRET_KEY') }}" placeholder="{{ translate('Paytiko merchant secret key') }}" required />
        </div>
    </div>
    <div class="form-group row">
        <input type="hidden" name="types[]" value="PAYTIKO_MODE">
        <div class="col-md-4">
            <label class="col-from-label" for="PAYTIKO_MODE">{{ translate('Enable Test Mode') }}</label>
        </div>
        <div class="col-md-8 d-flex justify-content-start align-items-center">
            <input type="hidden" name="PAYTIKO_MODE" id="PAYTIKO_MODE" value="{{ env('PAYTIKO_MODE') }}">
            <label class="aiz-switch aiz-switch-success mb-0 float-right">
                <input type="checkbox" onchange="updatePaymentSettings(this, 'PAYTIKO_MODE',0)"
                    @if (env('PAYTIKO_MODE')) checked @endif>
                <span class="slider round"></span>
            </label>
        </div>
    </div>
    <div class="form-group mb-0 text-right">
        <button type="submit" class="btn btn-sm btn-primary">{{ translate('Save') }}</button>
    </div>
</form>
