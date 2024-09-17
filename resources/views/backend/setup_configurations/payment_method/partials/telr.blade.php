<form class="form-horizontal" action="{{ route('payment_method.update') }}" method="POST">
    @csrf
    <input type="hidden" name="payment_method" value="telr">
    <div class="form-group row">
        <input type="hidden" name="types[]" value="TELR_STORE_ID">
        <div class="col-md-4">
            <label class="col-from-label">{{ translate('Telr Key') }}</label>
        </div>
        <div class="col-md-8">
            <input type="text" class="form-control" name="TELR_STORE_ID"
                value="{{ env('TELR_STORE_ID') }}" placeholder="{{ translate('TELR KEY') }}"
                required>
        </div>
    </div>
    <div class="form-group row">
        <input type="hidden" name="types[]" value="TELR_AUTHENTICATION_KEY">
        <div class="col-md-4">
            <label class="col-from-label">{{ translate('Telr Authentication Key') }}</label>
        </div>
        <div class="col-md-8">
            <input type="text" class="form-control" name="TELR_AUTHENTICATION_KEY"
                value="{{ env('TELR_AUTHENTICATION_KEY') }}" placeholder="{{ translate('TELR AUTHENTICATION KEY') }}"
                required>
        </div>
    </div>
    <div class="form-group row">
        <input type="hidden" name="types[]" value="TELR_MODE">
        <div class="col-md-4">
            <label class="col-from-label">{{ translate('Enable Test Mode') }}</label>
        </div>
        <div class="col-md-8 d-flex justify-content-start align-items-center">
            <input type="hidden" name="TELR_MODE" id="TELR_MODE" value="{{ env('TELR_MODE') }}">
            <div class="custom-control custom-radio custom-control-inline">
                <input type="radio" id="enableTelrTest" name="TELR_MODE_RADIO" onchange="$('#TELR_MODE').val(1)" class="custom-control-input"  @if (env('TELR_MODE')) checked @endif>
                <label class="custom-control-label" for="enableTelrTest">Enabled</label>
            </div>
            <div class="custom-control custom-radio custom-control-inline">
                <input type="radio" id="disableTelrTest" name="TELR_MODE_RADIO" onchange="$('#TELR_MODE').val(0)" class="custom-control-input"  @if (!env('TELR_MODE')) checked @endif>
                <label class="custom-control-label" for="disableTelrTest">Disabled</label>
            </div>
        </div>
    </div>
    <div class="form-group mb-0 text-right">
        <button type="submit" class="btn btn-sm btn-primary">{{ translate('Save') }}</button>
    </div>
</form>
