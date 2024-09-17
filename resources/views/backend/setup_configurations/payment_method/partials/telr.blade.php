<form class="form-horizontal" action="{{ route('payment_method.update') }}" method="POST">
    @csrf
    <input type="hidden" name="payment_method" value="telr">
    @if(env('DEMO_MODE') == 'On')
        <div class="form-group row">
            <input type="hidden" name="types[]" value="TELR_STORE_ID">
            <div class="col-md-4">
                <label class="col-from-label" for="TELR_STORE_ID">{{ translate('Telr store key') }}</label>
            </div>
            <div class="col-md-8">
                <input type="text"
                    class="form-control" name="TELR_STORE_ID" id="TELR_STORE_ID"
                    value="{{ env('TELR_STORE_ID') }}" placeholder="{{ translate('Telr store key') }}"
                    required />
            </div>
        </div>
        <div class="form-group row">
            <input type="hidden" name="types[]" value="TELR_AUTHENTICATION_KEY">
            <div class="col-md-4">
                <label class="col-from-label" for="TELR_AUTHENTICATION_KEY">{{ translate('Telr Authentication Key') }}</label>
            </div>
            <div class="col-md-8">
                <input type="text"
                    class="form-control" name="TELR_AUTHENTICATION_KEY" id="TELR_AUTHENTICATION_KEY"
                    value="{{ env('TELR_AUTHENTICATION_KEY') }}" placeholder="{{ translate('TELR AUTHENTICATION KEY') }}"
                    required />
            </div>
        </div>
    @endif
    <div class="form-group row">
        <input type="hidden" name="types[]" value="TELR_MODE">
        <div class="col-md-4">
            <label class="col-from-label" for="TELR_MODE">{{ translate('Enable Test Mode') }}</label>
        </div>
        <div class="col-md-8 d-flex justify-content-start align-items-center">
            <input type="hidden" name="TELR_MODE" id="TELR_MODE" value="{{ env('TELR_MODE') }}">
            <label class="aiz-switch aiz-switch-success mb-0 float-right">
                <input type="checkbox" onchange="updatePaymentSettings(this, 'TELR_MODE',0)" @if (env('TELR_MODE')) checked @endif>
                <span class="slider round"></span>
            </label>
        </div>
    </div>
    <div class="form-group mb-0 text-right">
        <button type="submit" class="btn btn-sm btn-primary">{{ translate('Save') }}</button>
    </div>
</form>
