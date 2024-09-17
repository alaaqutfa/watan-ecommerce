<form class="form-horizontal" action="{{ route('payment_method.update') }}" method="POST">
    @csrf
    <input type="hidden" name="payment_method" value="magnati">
    @if(env('DEMO_MODE') == 'On')
        @if (!env('MAGNATI_MODE'))
            <div class="form-group row">
                <input type="hidden" name="types[]" value="MAGNATI_STORE_NAME_PROD">
                <div class="col-md-4">
                    <label class="col-from-label" for="MAGNATI_STORE_NAME_PROD">{{ translate('Magnati Store Name') }}</label>
                </div>
                <div class="col-md-8">
                    <input type="text"
                        class="form-control" name="MAGNATI_STORE_NAME_PROD" id="MAGNATI_STORE_NAME_PROD"
                        value="{{ env('MAGNATI_STORE_NAME_PROD') }}" placeholder="{{ translate('Magnati Store Name') }}"
                        required>
                </div>
            </div>
            <div class="form-group row">
                <input type="hidden" name="types[]" value="MAGNATI_SHARED_SECRET_PROD">
                <div class="col-md-4">
                    <label class="col-from-label" for="MAGNATI_SHARED_SECRET_PROD">{{ translate('Magnati Shared Secret') }}</label>
                </div>
                <div class="col-md-8">
                    <input type="text"
                        class="form-control" name="MAGNATI_SHARED_SECRET_PROD" id="MAGNATI_SHARED_SECRET_PROD"
                        value="{{ env('MAGNATI_SHARED_SECRET_PROD') }}" placeholder="{{ translate('Magnati Shared Secret') }}"
                        required>
                </div>
            </div>
        @else
            <div class="form-group row">
                <input type="hidden" name="types[]" value="MAGNATI_STORE_NAME_TEST">
                <div class="col-md-4">
                    <label class="col-from-label" for="MAGNATI_STORE_NAME_TEST">{{ translate('Magnati Store Name (Test)') }}</label>
                </div>
                <div class="col-md-8">
                    <input type="text" class="form-control" name="MAGNATI_STORE_NAME_TEST" id="MAGNATI_STORE_NAME_TEST"
                        value="{{ env('MAGNATI_STORE_NAME_TEST') }}" placeholder="{{ translate('Magnati Store Name (Test)') }}"
                        required>
                </div>
            </div>
            <div class="form-group row">
                <input type="hidden" name="types[]" value="MAGNATI_SHARED_SECRET_TEST">
                <div class="col-md-4">
                    <label class="col-from-label" for="MAGNATI_SHARED_SECRET_TEST">{{ translate('Magnati Shared Secret (Test)') }}</label>
                </div>
                <div class="col-md-8">
                    <input type="text"
                        class="form-control" name="MAGNATI_SHARED_SECRET_TEST" id="MAGNATI_SHARED_SECRET_TEST"
                        value="{{ env('MAGNATI_SHARED_SECRET_TEST') }}" placeholder="{{ translate('Magnati Shared Secret (Test)') }}"
                        required>
                </div>
            </div>
        @endif
    @endif
    <div class="form-group row">
        <input type="hidden" name="types[]" value="MAGNATI_MODE">
        <div class="col-md-4">
            <label class="col-from-label" for="MAGNATI_MODE">{{ translate('Enable Test Mode') }}</label>
        </div>
        <div class="col-md-8 d-flex justify-content-start align-items-center">
            <input type="hidden" name="MAGNATI_MODE" id="MAGNATI_MODE" value="{{ env('MAGNATI_MODE') }}">
            <label class="aiz-switch aiz-switch-success mb-0 float-right">
                <input type="checkbox" onchange="updatePaymentSettings(this, 'MAGNATI_MODE',0)" @if (env('MAGNATI_MODE')) checked @endif>
                <span class="slider round"></span>
            </label>
        </div>
    </div>
    <div class="form-group mb-0 text-right">
        <button type="submit" class="btn btn-sm btn-primary">{{ translate('Save') }}</button>
    </div>
</form>
