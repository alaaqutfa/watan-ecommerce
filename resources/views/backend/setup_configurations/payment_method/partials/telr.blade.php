<form class="form-horizontal" action="{{ route('payment_method.update') }}" method="POST">
    @csrf
    <input type="hidden" name="payment_method" value="telr">
    <div class="form-group row">
        <input type="hidden" name="types[]" value="TELR_KEY">
        <div class="col-md-4">
            <label class="col-from-label">{{ translate('Telr Key') }}</label>
        </div>
        <div class="col-md-8">
            <input type="text" class="form-control" name="TELR_KEY"
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
    <div class="form-group mb-0 text-right">
        <button type="submit" class="btn btn-sm btn-primary">{{ translate('Save') }}</button>
    </div>
</form>
